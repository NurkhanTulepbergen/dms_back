#!/bin/sh
set -e

cd /var/www

if [ ! -f .env ] && [ -f .env.example ]; then
    cp .env.example .env
fi

if [ -z "${APP_KEY:-}" ] && ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    echo "APP_KEY is not set. Fill APP_KEY in nginx/.env before starting the server."
    exit 1
fi

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views bootstrap/cache database
chmod -R ug+rwx storage bootstrap/cache database || true

mkdir -p storage/app/public/buy-sell

if [ ! -e public/storage ]; then
    php artisan storage:link || true
fi

# The image can contain config cached during build; runtime env must win.
rm -f bootstrap/cache/config.php bootstrap/cache/routes-*.php bootstrap/cache/events.php

if [ "${DB_CONNECTION:-mysql}" = "mysql" ]; then
    echo "Waiting for MySQL at ${DB_HOST:-mysql}:${DB_PORT:-3306}..."
    ATTEMPTS_LEFT=60

    until php -r '
        $host = getenv("DB_HOST") ?: "mysql";
        $port = getenv("DB_PORT") ?: "3306";
        $user = getenv("DB_USERNAME") ?: "root";
        $pass = getenv("DB_PASSWORD") ?: "";

        try {
            new PDO("mysql:host={$host};port={$port}", $user, $pass);
            exit(0);
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            exit(1);
        }
    '; do
        ATTEMPTS_LEFT=$((ATTEMPTS_LEFT - 1))

        if [ "$ATTEMPTS_LEFT" -le 0 ]; then
            echo "MySQL did not become ready in time."
            exit 1
        fi

        sleep 2
    done
fi

php artisan migrate --force
php artisan optimize:clear || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
