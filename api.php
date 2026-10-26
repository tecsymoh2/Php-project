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
        $r['thumb'] = tf($r['thumb']) ? presign('GET', $k . '.t') : null;
        $r['url'] = preg_match('#^(image|video|audio)/|^application/pdf$#', (string)$r['type']) ? presign('GET', $k) : null;
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
      if (!preg_match('/^[a-z0-9][a-z0-9._-]{0,100}\.[a-z0-9]{1,8}$/', $s) || $s === 'index.html' || $s === 'api.php')
        out(['error' => 'Link name must look like photo-1.png'], 400);
      if (q('select 1 from files where share_slug = ? and id <> ?', [$s, $b['id']])->fetchColumn()) out(['error' => 'That link name is already used'], 409);
      $days = (int)($b['days'] ?? 0);
      q('update files set share_slug = ?, share_expires_at = ? where id = ?', [$s, $days ? gmdate('c', time() + $days * 86400) : null, $b['id']]);
      out(['slug' => $s]);
    }
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
