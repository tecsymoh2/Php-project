FROM php:8.3-apache
RUN apt-get update && apt-get install -y libpq-dev && rm -rf /var/lib/apt/lists/* \
 && docker-php-ext-install pdo_pgsql && a2enmod rewrite \
 && sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
COPY . /var/www/html/
