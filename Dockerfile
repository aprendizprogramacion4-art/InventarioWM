FROM php:8.5-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libonig-dev \
    libzip-dev \
    unzip \
    && docker-php-ext-install bcmath mbstring pdo_sqlite zip \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/000-default.conf \
    && printf '%s\n' \
        '<Directory /var/www/html/public>' \
        '    Options FollowSymLinks' \
        '    AllowOverride All' \
        '    Require all granted' \
        '    <IfModule mod_rewrite.c>' \
        '        RewriteEngine On' \
        '        RewriteCond %{REQUEST_FILENAME} !-f' \
        '        RewriteRule ^ index.php [L]' \
        '    </IfModule>' \
        '</Directory>' \
        >> /etc/apache2/apache2.conf \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache database \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R ug+rwX storage bootstrap/cache database

EXPOSE 80

CMD ["apache2-foreground"]
