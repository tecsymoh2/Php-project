<?php
// Public link: yourdomain.com/<slug>. Small files are streamed so the address stays clean; big ones redirect.
require __DIR__ . '/lib.php';

$s = strtolower((string)($_GET['s'] ?? ''));
$f = q('select key,name,type,size,share_expires_at,share_views,share_max_views,share_pass,owner from files where share_slug = ? and trashed_at is null and ready', [$s])->fetch();
if (!$f || ($f['share_expires_at'] && strtotime($f['share_expires_at']) < time())) { http_response_code(404); exit('Not found'); }
if ($f['share_pass']) { http_response_code(403); exit('This link is password protected. Open it on the main site.'); }
if ($f['share_max_views'] && (int)$f['share_views'] >= (int)$f['share_max_views']) { http_response_code(404); exit('Not found'); }
if ($f['owner']) {
  try { if (tf(q('select suspended from developers where id = ?', [$f['owner']])->fetchColumn())) { http_response_code(404); exit('Not found'); } } catch (Throwable $e) { /* no developers table: ignore */ }
}

$range = $_SERVER['HTTP_RANGE'] ?? '';
if ($range === '' || strpos($range, 'bytes=0-') === 0) q('update files set share_views = share_views + 1 where share_slug = ?', [$s]);
$dl = isset($_GET['dl']);
$fn = preg_replace('/[^\x20-\x7e]/', '_', str_replace('"', '', $f['name']));

if ((int)$f['size'] > 40 * 1048576) {
  $x = ['response-content-type' => $f['type'], 'response-content-disposition' => 'inline'];
  if ($dl) $x['response-content-disposition'] = "attachment; filename=\"$fn\"";
  header('Location: ' . presign('GET', $f['key'], 3600, $x), true, 302);
  exit;
}

while (ob_get_level()) ob_end_clean();
set_time_limit(0);
$status = 200;
$ch = curl_init(presign('GET', $f['key']));
curl_setopt_array($ch, [
  CURLOPT_HTTPHEADER => $range !== '' ? ["Range: $range"] : [],
  CURLOPT_HEADERFUNCTION => function ($c, $l) use (&$status, $f, $dl, $fn) {
    $n = strlen($l);
    if (preg_match('#^HTTP/\S+\s+(\d+)#', $l, $m)) { $status = (int)$m[1]; if ($status < 400) http_response_code($status); return $n; }
    if ($status >= 400) return $n;
    if (trim($l) === '') {
      header('Content-Type: ' . ($f['type'] ?: 'application/octet-stream'));
      header('Accept-Ranges: bytes');
      header('Content-Disposition: ' . ($dl ? 'attachment' : 'inline') . "; filename=\"$fn\"");
      if (!preg_match('#^(video|audio)/|^image/(?!svg)#', (string)$f['type'])) header('Content-Security-Policy: sandbox');
      header('X-Content-Type-Options: nosniff');
      header('Cache-Control: ' . ($f['share_max_views'] ? 'private, no-store' : 'public, max-age=300'));
      return $n;
    }
    [$k, $v] = array_pad(explode(':', $l, 2), 2, '');
    $k = strtolower(trim($k));
    if ($k === 'content-length') header('Content-Length: ' . trim($v));
    if ($k === 'content-range') header('Content-Range: ' . trim($v));
    return $n;
  },
  CURLOPT_WRITEFUNCTION => function ($c, $d) use (&$status) {
    if ($status >= 400) return strlen($d);
    echo $d; flush();
    return strlen($d);
  },
]);
curl_exec($ch);
curl_close($ch);
if ($status >= 400 && !headers_sent()) { http_response_code(502); echo 'File unavailable'; }
