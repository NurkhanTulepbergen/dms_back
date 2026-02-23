FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
RUN php artisan storage:link || true
RUN chmod -R 775 storage bootstrap/cache || true

RUN php artisan config:clear
RUN php artisan cache:clear
Run php artisan config:cache
Run php artisan optimize:clear



CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
