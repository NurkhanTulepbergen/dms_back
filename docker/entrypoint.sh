#!/bin/bash
set -e

echo "🔧 Bootstrapping Laravel..."

# 1. Создаём .env если нет
if [ ! -f /var/www/.env ]; then
    echo "📄 .env not found — creating..."
    cp /var/www/.env.example /var/www/.env
fi

# 2. Composer install, если vendor пуст
if [ ! -d /var/www/vendor ]; then
    echo "📦 Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# 3. Генерация ключа, если его нет
if ! grep -q "APP_KEY=base64:" /var/www/.env; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate
fi

# 4. Выполняем миграции (если таблиц нет)
echo "🗄 Running migrations..."
php artisan migrate --force

echo "🚀 Laravel ready!"

# Запуск PHP-FPM
exec php-fpm
