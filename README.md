<?php
require __DIR__ . '/lib.php';
header('Content-Type: application/json');

function out($d, int $code = 200): void {
  http_response_code($code);
  echo json_encode($d, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
  exit;
}
function setc(string $v, int $exp): void {
  setcookie('t', $v, ['expires' => $exp, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']), 'httponly' => true, 'samesite' => 'Strict']);
}
function idlist($ids): string {
  $ok = array_values(array_filter((array)$ids, fn($i) => is_string($i) && preg_match('/^[0-9a-f-]{36}$/i', $i)));
  return '{' . implode(',', $ok) . '}';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(['error' => 'POST only'], 405);
$b = json_decode(file_get_contents('php://input'), true) ?: [];
$a = $b['a'] ?? '';

if ($a === 'login') {
  if (!hash_equals(sign((string)($b['password'] ?? '')), sign(APP_PASSWORD))) out(['error' => 'Wrong password'], 401);
  $e = time() + 30 * 86400;
  setc("$e." . sign((string)$e), $e);
  out(['ok' => true]);
}
if ($a === 'logout') { setc('', time() - 3600); out(['ok' => true]); }
if ($a === 'pk_options') {
  out(['token' => new_challenge(), 'ids' => array_column(q('select id from passkeys')->fetchAll(), 'id'), 'pin' => (bool)q('select 1 from app_pin')->fetchColumn()]);
}
if ($a === 'pk_login') {
  try {
    $chal = read_challenge((string)($b['token'] ?? ''));
    if (!$chal) throw new Exception('Expired. Please try again.');
    $host = host_of();
    check_client((string)($b['clientDataJSON'] ?? ''), 'webauthn.get', $chal, $host);
    $k = q('select id, public_key from passkeys where id = ?', [(string)($b['id'] ?? '')])->fetch();
    if (!$k || !verify_assertion($k['public_key'], (string)($b['authenticatorData'] ?? ''), (string)($b['clientDataJSON'] ?? ''), (string)($b['signature'] ?? ''), $host)) throw new Exception('Fingerprint not recognised');
    q('update passkeys set last_used_at = now() where id = ?', [$k['id']]);
    $until = time() + 30 * 86400;
    setc("$until." . sign((string)$until), $until);
  } catch (Throwable $err) {
    out(['error' => $err->getMessage()], 401);
  }
  out(['ok' => true]);
}
if ($a === 'pin_login') {
  try {
    $p = q('select pin_hash, fails, locked_until from app_pin where id = 1')->fetch();
    if (!$p) out(['error' => 'No PIN is set'], 401);
    if ($p['locked_until'] && strtotime($p['locked_until']) > time()) {
      $m = (int)ceil((strtotime($p['locked_until']) - time()) / 60);
      out(['error' => "Too many wrong tries. Try again in $m minute(s), or use your password."], 429);
    }
    if (!password_verify((string)($b['pin'] ?? ''), $p['pin_hash'])) {
      $f = (int)$p['fails'] + 1;
      if ($f >= 5) q("update app_pin set fails = 0, locked_until = now() + interval '15 minutes' where id = 1");
      else q('update app_pin set fails = ? where id = 1', [$f]);
      out(['error' => $f >= 5 ? 'Too many wrong tries. The PIN is locked for 15 minutes.' : 'Wrong PIN. ' . (5 - $f) . ' tries left.'], 401);
    }
    q('update app_pin set fails = 0, locked_until = null where id = 1');
    $until = time() + 30 * 86400;
    setc("$until." . sign((string)$until), $until);
  } catch (Throwable $err) {
    out(['error' => $err->getMessage()], 500);
  }
  out(['ok' => true]);
}
if (!authed()) out(['error' => 'Not signed in'], 401);

try {
  switch ($a) {
    case 'list': {
      wipe(q("select id,key,thumb from files where trashed_at < now() - interval '30 days'")->fetchAll());
      $f = "'YYYY-MM-DD\"T\"HH24:MI:SS.US\"Z\"'";
      $rows = q("select id,name,key,size,type,folder,favorite,share_slug,share_views,thumb,
        to_char(trashed_at at time zone 'UTC',$f) as trashed_at,
        to_char(created_at at time zone 'UTC',$f) as created_at,
        to_char(share_expires_at at time zone 'UTC',$f) as share_expires_at
        from files order by files.created_at desc limit 5000")->fetchAll();
      out(['rows' => array_map(function ($r) {
        $k = $r['key']; unset($r['key']);
        $r['size'] = (int)$r['size'];
        $r['favorite'] = tf($r['favorite']);
        $r['share_views'] = (int)$r['share_views'];
        $r['thumb'] = tf($r['thumb']) ? presign_stable($k . '.t') : null;
        $r['url'] = preg_match('#^(image|video|audio)/|^application/pdf$#', (string)$r['type']) ? presign_stable($k) : null;
        return $r;
      }, $rows)]);
    }
    case 'upload': {
      $size = (int)($b['size'] ?? 0);
      $used = (int)q('select coalesce(sum(size),0)::text from files')->fetchColumn();
      if ($used + $size > CAP) out(['error' => 'Storage is full'], 413);
      $name = (string)($b['name'] ?? 'file');
      $key = uuid() . (preg_match('/\.[a-z0-9]{1,8}$/i', $name, $m) ? $m[0] : '');
      $type = ($b['type'] ?? '') ?: 'application/octet-stream';
      $th = !empty($b['thumb']);
      $id = q('insert into files (name,key,size,type,folder,thumb) values (?,?,?,?,?,?) returning id',
        [$name, $key, $size, $type, (string)($b['folder'] ?? ''), $th ? 't' : 'f'])->fetchColumn();
      out(['id' => $id, 'url' => presign('PUT', $key), 'thumbUrl' => $th ? presign('PUT', $key . '.t') : null]);
    }
    case 'fav':
      q('update files set favorite = ? where id = ?', [!empty($b['v']) ? 't' : 'f', $b['id']]);
      out(['ok' => true]);
    case 'edit':
      q('update files set name = ?, folder = ? where id = ?', [(string)$b['name'], (string)($b['folder'] ?? ''), $b['id']]);
      out(['ok' => true]);
    case 'bulk': {
      $ids = idlist($b['ids'] ?? []);
      switch ($b['op'] ?? '') {
        case 'fav': q('update files set favorite = true where id = any(?::uuid[])', [$ids]); break;
        case 'trash': q('update files set trashed_at = now() where id = any(?::uuid[])', [$ids]); break;
        case 'restore': q('update files set trashed_at = null where id = any(?::uuid[])', [$ids]); break;
        case 'move': q('update files set folder = ? where id = any(?::uuid[])', [(string)($b['folder'] ?? ''), $ids]); break;
        case 'del': wipe(q('select id,key,thumb from files where id = any(?::uuid[])', [$ids])->fetchAll()); break;
      }
      out(['ok' => true]);
    }
    case 'empty':
      wipe(q('select id,key,thumb from files where trashed_at is not null')->fetchAll());
      out(['ok' => true]);
    case 'folder':
      q('update files set folder = ? where folder = ?', [(string)($b['to'] ?? ''), (string)($b['from'] ?? '')]);
      out(['ok' => true]);
    case 'publish': {
      $cur = q('select share_slug from files where id = ?', [$b['id']])->fetchColumn();
      $s = strtolower((string)(($b['slug'] ?? '') ?: $cur));
      if (!preg_match('/^[a-z0-9][a-z0-9._-]{0,100}\.[a-z0-9]{1,8}$/', $s) || preg_match('/^(index|api|f|lib|config|setup|manifest|icon)\.(php|html)$/', $s))
        out(['error' => 'Link name must look like photo-1.png'], 400);
      if (q('select 1 from files where share_slug = ? and id <> ?', [$s, $b['id']])->fetchColumn()) out(['error' => 'That link name is already used'], 409);
      $days = (int)($b['days'] ?? 0);
      q('update files set share_slug = ?, share_expires_at = ? where id = ?', [$s, $days ? gmdate('c', time() + $days * 86400) : null, $b['id']]);
      out(['slug' => $s]);
    }
    case 'pk_reg_options':
      out(['token' => new_challenge(), 'ids' => array_column(q('select id from passkeys')->fetchAll(), 'id')]);
    case 'pk_register': {
      $chal = read_challenge((string)($b['token'] ?? ''));
      if (!$chal) out(['error' => 'Expired. Please try again.'], 400);
      check_client((string)($b['clientDataJSON'] ?? ''), 'webauthn.create', $chal, host_of());
      if (!openssl_pkey_get_public(spki_pem((string)($b['publicKey'] ?? '')))) out(['error' => 'Bad public key'], 400);
      q('insert into passkeys (id, public_key, alg, name) values (?,?,?,?) on conflict (id) do nothing',
        [(string)$b['id'], (string)$b['publicKey'], (int)($b['alg'] ?? -7), substr((string)($b['name'] ?? 'This device'), 0, 60)]);
      out(['ok' => true]);
    }
    case 'pk_list':
      out(['keys' => q('select id, name, created_at, last_used_at from passkeys order by created_at')->fetchAll(), 'pin' => (bool)q('select 1 from app_pin')->fetchColumn()]);
    case 'pin_set': {
      $pin = (string)($b['pin'] ?? '');
      if (!preg_match('/^\d{4,8}$/', $pin)) out(['error' => 'PIN must be 4 to 8 digits'], 400);
      q('insert into app_pin (id, pin_hash) values (1, ?) on conflict (id) do update set pin_hash = excluded.pin_hash, fails = 0, locked_until = null', [password_hash($pin, PASSWORD_DEFAULT)]);
      out(['ok' => true]);
    }
    case 'pin_del':
      q('delete from app_pin');
      out(['ok' => true]);
    case 'pk_del':
      q('delete from passkeys where id = ?', [(string)$b['id']]);
      out(['ok' => true]);
    case 'unpublish':
      q('update files set share_slug = null, share_expires_at = null where id = ?', [$b['id']]);
      out(['ok' => true]);
    case 'dl': {
      $f = q('select name,key from files where id = ?', [$b['id']])->fetch();
      $fn = preg_replace('/[^\x20-\x7e]/', '_', str_replace('"', '', $f['name']));
      out(['url' => presign('GET', $f['key'], 3600, ['response-content-disposition' => "attachment; filename=\"$fn\""])]);
    }
    default:
      out(['error' => 'Unknown action'], 400);
  }
} catch (Throwable $e) {
  out(['error' => $e->getMessage()], 500);
}
