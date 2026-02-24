#!/bin/sh
set -e

php artisan config:clear || true
php artisan cache:clear || true
php artisan config:cache || true
php artisan optimize:clear || true

php artisan migrate --force || true

php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
