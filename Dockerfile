FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    nginx git unzip curl libzip-dev libicu-dev \
    && docker-php-ext-install pdo pdo_mysql zip intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN php artisan storage:link || true
RUN chmod -R 775 storage bootstrap/cache

# nginx config
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 8080

CMD service nginx start && php-fpm
