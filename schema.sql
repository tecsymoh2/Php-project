-- Already applied to your Neon project. Kept for reference.
create table if not exists files (id uuid primary key default gen_random_uuid(), name text not null, key text not null unique, size bigint not null default 0, type text not null default '', folder text not null default '', favorite boolean not null default false, trashed_at timestamptz, created_at timestamptz not null default now());
create index if not exists files_folder_idx on files (folder) where trashed_at is null;
create index if not exists files_created_idx on files (created_at desc);
alter table files add column if not exists share_slug text unique;
alter table files add column if not exists share_expires_at timestamptz;
alter table files add column if not exists share_views int not null default 0;
alter table files add column if not exists thumb boolean not null default false;
