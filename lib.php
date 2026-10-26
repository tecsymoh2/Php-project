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
function presign(string $method, string $key, int $expires = 3600, array $extra = []): string {
  $host = parse_url(S3_ENDPOINT, PHP_URL_HOST);
  $port = parse_url(S3_ENDPOINT, PHP_URL_PORT);
  $hostHeader = $port ? "$host:$port" : $host;
  $path = '/' . S3_BUCKET . '/' . implode('/', array_map('rawurlencode', explode('/', $key)));
  $now = gmdate('Ymd\THis\Z'); $date = substr($now, 0, 8);
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
