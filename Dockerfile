FROM php:8.4-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# install deps (ВАЖНО: без scripts)
RUN composer install --no-dev --optimize-autoloader --no-scripts

# run migrations + start server
CMD sh -c "php artisan migrate --force && php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"