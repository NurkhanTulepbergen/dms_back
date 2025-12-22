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

# 3. Генерация ключа
if ! grep -q "APP_KEY=base64:" /var/www/.env; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate
fi

# ⚠ ДАЁМ ПРАВА ПЕРЕД МИГРАЦИЯМИ
echo "🔐 Fixing permissions..."
chmod -R 777 storage bootstrap/cache
chmod -R 777 database || true

# создаём SQLite файл если нет
if [ ! -f /var/www/database/database.sqlite ]; then
    echo "📄 Creating SQLite database file..."
    touch /var/www/database/database.sqlite
fi

chmod 666 /var/www/database/database.sqlite

# 4. Теперь миграции будут работать
echo "🗄 Running migrations..."
php artisan migrate --force

echo "🚀 Laravel ready!"

# Запуск PHP-FPM
exec php-fpm
