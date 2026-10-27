<?php
// One-time database setup. Sign in to the app first, then open /setup.php.
// Safe to run again: every statement only creates what is missing.
require __DIR__ . '/lib.php';
header('Content-Type: text/plain; charset=utf-8');
if (!authed()) { http_response_code(401); exit("Sign in to the app first, then open this page again.\n"); }
$statements = [
  "create table if not exists files (id uuid primary key default gen_random_uuid(), name text not null, key text not null unique, size bigint not null default 0, type text not null default '', folder text not null default '', favorite boolean not null default false, trashed_at timestamptz, created_at timestamptz not null default now())",
  "create index if not exists files_folder_idx on files (folder) where trashed_at is null",
  "create index if not exists files_created_idx on files (created_at desc)",
  "alter table files add column if not exists share_slug text unique",
  "alter table files add column if not exists share_expires_at timestamptz",
  "alter table files add column if not exists share_views int not null default 0",
  "alter table files add column if not exists thumb boolean not null default false",
  "create table if not exists passkeys (id text primary key, public_key text not null, alg int not null default -7, name text not null default 'This device', created_at timestamptz not null default now(), last_used_at timestamptz)",
  "create table if not exists app_pin (id int primary key default 1 check (id = 1), pin_hash text not null, fails int not null default 0, locked_until timestamptz)",
];
try {
  foreach ($statements as $sql) { db()->exec($sql); }
  echo "Done. " . count($statements) . " statements ran.\n";
} catch (Throwable $e) {
  http_response_code(500);
  echo 'Setup failed: ' . $e->getMessage() . "\n";
}
