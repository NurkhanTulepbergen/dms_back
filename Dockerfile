FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts
RUN php artisan storage:link || true
RUN chmod -R 775 storage bootstrap/cache

CMD php -S 0.0.0.0:${PORT:-8080} -t public
