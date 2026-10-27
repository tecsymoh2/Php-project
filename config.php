<?php
// Settings. Environment variables (if set) override these values. Keep this file private.
function cfg($k,$d){return getenv($k)!==false&&getenv($k)!==''?getenv($k):$d;}
define('DB_HOST',cfg('DB_HOST','ep-rapid-dew-b5ujrc03.c-7.us-east-2.aws.neon.tech'));
define('DB_NAME',cfg('DB_NAME','neondb'));
define('DB_USER',cfg('DB_USER','neondb_owner'));
define('DB_PASS',cfg('DB_PASS','npg_bwMUl1qyWG4m'));
define('DB_ENDPOINT',cfg('DB_ENDPOINT','ep-rapid-dew-b5ujrc03'));
define('S3_ENDPOINT',cfg('S3_ENDPOINT','https://br-weathered-paper-b5pv1y3o.storage.c-7.us-east-2.aws.neon.tech'));
define('S3_REGION',cfg('S3_REGION','us-east-2'));
define('S3_BUCKET',cfg('S3_BUCKET','symoh-files'));
define('S3_KEY_ID',cfg('S3_KEY_ID','nak_live_c60c80eaaa1a414a95ba0c2f1672cf91'));
define('S3_SECRET',cfg('S3_SECRET','nsk_live_ea259472c76349e8e102bcea7d9c558306fec791c87834bbd4d303c82ef2bf77'));
define('APP_PASSWORD',cfg('APP_PASSWORD','tyraWknxZJwlXP'));
define('AUTH_SECRET',cfg('AUTH_SECRET','233f65486d0b4bde64d1d2c7509f73f80fedc9243c15c18e083dafd7bd5150c7'));
define('CAP',(float)cfg('STORAGE_CAP_GB','5')*1073741824);
