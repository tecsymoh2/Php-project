<?php
// Web app manifest (lets the app be installed on a phone's home screen).
header('Content-Type: application/manifest+json');
header('Cache-Control: public, max-age=86400');
echo json_encode([
  'name' => 'Symoh Storage',
  'short_name' => 'Storage',
  'start_url' => './',
  'display' => 'standalone',
  'background_color' => '#0e0f1d',
  'theme_color' => '#5b4bff',
  'icons' => [['src' => 'icon.php', 'sizes' => 'any', 'type' => 'image/svg+xml', 'purpose' => 'any']],
], JSON_UNESCAPED_SLASHES);
