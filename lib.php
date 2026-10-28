<?php
require __DIR__ . '/config.php';

function db(): PDO {
  static $pdo;
  if (!$pdo) {
    $dsn = 'pgsql:host=' . DB_HOST . ';port=5432;dbname=' . DB_NAME . ';sslmode=require;options=endpoint=' . DB_ENDPOINT;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
  }
  return $pdo;
}
function q(string $sql, array $p = []): PDOStatement { $s = db()->prepare($sql); $s->execute($p); return $s; }
function tf($v): bool { return $v === true || $v === 't' || $v === 'true' || $v === '1' || $v === 1; }
function uuid(): string {
  $d = random_bytes(16); $d[6] = chr(ord($d[6]) & 0x0f | 0x40); $d[8] = chr(ord($d[8]) & 0x3f | 0x80);
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($d), 4));
}

// ---- Auth ----
function sign(string $s): string { return hash_hmac('sha256', $s, AUTH_SECRET); }
function authed(): bool {
  $t = $_COOKIE['t'] ?? '';
  if ($t === '') return false;
  [$exp, $sig] = array_pad(explode('.', $t, 2), 2, '');
  return (int)$exp > time() && hash_equals(sign($exp), $sig);
}

// ---- S3 (AWS Signature V4, presigned URLs, no SDK needed) ----
function presign(string $method, string $key, int $expires = 3600, array $extra = [], ?int $at = null): string {
  $host = parse_url(S3_ENDPOINT, PHP_URL_HOST);
  $port = parse_url(S3_ENDPOINT, PHP_URL_PORT);
  $hostHeader = $port ? "$host:$port" : $host;
  $path = '/' . S3_BUCKET . '/' . implode('/', array_map('rawurlencode', explode('/', $key)));
  $now = gmdate('Ymd\THis\Z', $at ?? time()); $date = substr($now, 0, 8);
  $scope = $date . '/' . S3_REGION . '/s3/aws4_request';
  $params = $extra + [
    'X-Amz-Algorithm' => 'AWS4-HMAC-SHA256',
    'X-Amz-Credential' => S3_KEY_ID . '/' . $scope,
    'X-Amz-Date' => $now,
    'X-Amz-Expires' => $expires,
    'X-Amz-SignedHeaders' => 'host',
  ];
  ksort($params);
  $qs = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
  $canon = "$method\n$path\n$qs\nhost:$hostHeader\n\nhost\nUNSIGNED-PAYLOAD";
  $sts = "AWS4-HMAC-SHA256\n$now\n$scope\n" . hash('sha256', $canon);
  $k = hash_hmac('sha256', 'aws4_request', hash_hmac('sha256', 's3', hash_hmac('sha256', S3_REGION, hash_hmac('sha256', $date, 'AWS4' . S3_SECRET, true), true), true), true);
  return rtrim(S3_ENDPOINT, '/') . $path . '?' . $qs . '&X-Amz-Signature=' . hash_hmac('sha256', $sts, $k);
}
// Same link for 6 hours, so browsers reuse photos they already downloaded
function presign_stable(string $key): string {
  return presign('GET', $key, 43200, ['response-cache-control' => 'private, max-age=21600'], intdiv(time(), 21600) * 21600);
}
function s3_delete(string $key): void {
  $ch = curl_init(presign('DELETE', $key, 300));
  curl_setopt_array($ch, [CURLOPT_CUSTOMREQUEST => 'DELETE', CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 30]);
  curl_exec($ch);
  curl_close($ch);
}
function wipe(array $rows): void {
  foreach ($rows as $r) {
    s3_delete($r['key']);
    if (tf($r['thumb'])) s3_delete($r['key'] . '.t');
    q('delete from files where id = ?', [$r['id']]);
  }
}

// ---- Fingerprint / passkeys (WebAuthn) ----
function b64u(string $b): string { return rtrim(strtr(base64_encode($b), '+/', '-_'), '='); }
function unb64(string $s): string { return (string)base64_decode(strtr($s, '-_', '+/') . str_repeat('=', (4 - strlen($s) % 4) % 4)); }
function new_challenge(): string { $r = b64u(random_bytes(32)); $exp = time() + 600; return "$r.$exp." . sign("pk$r$exp"); }
function read_challenge(string $tok): ?string {
  [$r, $exp, $sig] = array_pad(explode('.', $tok, 3), 3, '');
  return $r !== '' && (int)$exp > time() && hash_equals(sign("pk$r$exp"), $sig) ? $r : null;
}
function host_of(): string { return trim(explode(',', $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? '')[0]); }
function check_client(string $cdB64, string $type, string $chal, string $host): void {
  $cd = json_decode(unb64($cdB64), true);
  if (!is_array($cd) || ($cd['type'] ?? '') !== $type || ($cd['challenge'] ?? '') !== $chal) throw new Exception('Challenge check failed');
  $o = parse_url($cd['origin'] ?? '');
  if (!$o || ($o['host'] ?? '') . (isset($o['port']) ? ':' . $o['port'] : '') !== $host) throw new Exception('Origin check failed');
}
function spki_pem(string $spkiB64): string {
  return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode(unb64($spkiB64)), 64, "\n") . "-----END PUBLIC KEY-----\n";
}
function verify_assertion(string $spki, string $authData, string $cdB64, string $sig, string $host): bool {
  $ad = unb64($authData);
  if (strlen($ad) < 37) return false;
  if (substr($ad, 0, 32) !== hash('sha256', explode(':', $host)[0], true)) return false;
  $flags = ord($ad[32]);
  if (!($flags & 1) || !($flags & 4)) return false; // user present and verified
  $data = $ad . hash('sha256', unb64($cdB64), true);
  return openssl_verify($data, unb64($sig), spki_pem($spki), OPENSSL_ALGO_SHA256) === 1;
}
