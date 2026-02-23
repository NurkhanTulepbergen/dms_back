FROM php:8.4-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-scripts

RUN php artisan storage:link || true
RUN php artisan optimize || true

# Railway сам проксирует запросы к FPM
CMD sh -c "php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php-fpm"
