FROM php:8.4-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# install deps (без scripts — ок для CI)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# важные штуки для Laravel + Filament
RUN php artisan storage:link || true
RUN php artisan optimize || true

# ❗ КЛЮЧЕВОЕ: убрали router public/index.php
CMD sh -c "php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php -S 0.0.0.0:${PORT:-8080} -t public"