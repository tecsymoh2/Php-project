# Symoh Storage (PHP)

Needs PHP 8.0+ with the pdo_pgsql and curl extensions, and permission to make outgoing HTTPS and Postgres (port 5432) connections.

1. Upload everything in this folder to the root of your site (for example public_html).
2. Open your site and sign in with APP_PASSWORD from config.php.

config.php is already filled in. Environment variables, if set, override it.

Apache: .htaccess handles the clean links (yourdomain.com/photo-1.png).
Nginx: add this to your server block, next to the usual PHP handling:
    location / { try_files $uri $uri/ @slug; }
    location @slug { rewrite ^/([^/]+\.[A-Za-z0-9]+)$ /f.php?s=$1&$args last; }
    location ~ ^/(config|lib)\.php$ { deny all; }

No PHP host? Use the Dockerfile on any Docker host (Render, Fly.io, Railway) and set the settings as environment variables.
