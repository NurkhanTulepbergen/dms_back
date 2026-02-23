FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# ✅ важно: НЕ отключай scripts
RUN composer install --no-dev --optimize-autoloader

# ✅ создать нужные директории
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache

# (optional) линк storage
RUN php artisan storage:link || true

# ✅ права
RUN chmod -R 775 storage bootstrap/cache || true

# (optional) можно прогреть кеши, но только если APP_KEY уже задан при сборке (обычно нет)
# RUN php artisan config:cache && php artisan route:cache && php artisan view:cache || true

CMD php -S 0.0.0.0:${PORT:-8080} -t public
