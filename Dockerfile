FROM php:8.4-cli

# System deps
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

# 👉 создаём .env
#RUN cp .env.example .env || true

# install deps
RUN composer install --no-dev --optimize-autoloader

# generate key + migrate + run server
CMD sh -c "php artisan migrate --force && php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"