# DMS Backend

Backend для Dormitory Management System. Приложение построено на Laravel 12, Laravel Sanctum, Filament и `nwidart/laravel-modules`. Основная зона ответственности backend: пользователи и роли, общежития, заселение, заявки, новости, финансы, спортзал, штрафы и campus buy-sell.

## Стек

- PHP 8.2+; Docker-образ использует PHP 8.4 CLI.
- Laravel 12.
- MySQL 8.0.
- Redis.
- Laravel Sanctum для API-токенов.
- Filament для admin panel.
- `nwidart/laravel-modules` для модульной структуры.
- Stripe PHP SDK для платежей.
- Vite/Tailwind для Laravel assets.

## Структура проекта

```text
dms/
├── app/                    # базовый Laravel-код, middleware, helpers, providers
├── bootstrap/              # bootstrap Laravel 12
├── config/                 # конфигурация Laravel
├── database/               # общие миграции, фабрики, сидеры
├── Modules/                # бизнес-модули backend
├── routes/                 # общие маршруты
├── tests/                  # PHPUnit feature/unit tests
├── Dockerfile              # backend container
└── entrypoint.sh           # запуск контейнера, migrate, artisan serve
```

## Модули

| Модуль | Назначение |
| --- | --- |
| `Auth` | регистрация, вход, выход, смена пароля |
| `User` | пользователи, текущий профиль, уведомления |
| `Dormitory` | корпуса, этажи, комнаты |
| `Settlement` | заселение и статус проживания |
| `Requests` | заявки на проживание, смену комнаты, ремонт |
| `News` | новости |
| `Finance` | начисления, платежи, Stripe checkout/webhook |
| `Gym` | тарифы спортзала, абонемент, check-in/check-out |
| `Penalty` | правила, штрафы, погашение штрафов |
| `BuySell` | объявления campus marketplace |

## Роли

API использует поле `role` пользователя и middleware `role:*`.

Доступные роли:

- `admin`
- `student`
- `manager`
- `dorm-admin`
- `employee`

Admin также имеет глобальное право в `Modules\Auth\Providers\AuthServiceProvider` через `Gate::before`.

## Быстрый запуск через Docker

Docker Compose лежит на уровень выше backend в папке `nginx`.

```bash
cd ../nginx
docker compose up -d --build
```

После запуска:

- Backend API: `http://localhost:8000`
- Frontend dev server: `http://localhost:5173`
- Nginx: `http://localhost`
- MySQL container: `dms_mysql`
- Redis container: `dms_redis`

Backend container при старте:

1. Создает `.env` из `.env.example`, если файла нет.
2. Устанавливает Composer зависимости, если нет `vendor/autoload.php`.
3. Генерирует `APP_KEY`, если он не задан.
4. Ждет MySQL.
5. Выполняет `php artisan migrate --force`.
6. Запускает `php artisan serve --host=0.0.0.0 --port=8000`.

## Локальный запуск без Docker

Требования:

- PHP 8.2+
- Composer 2
- Node.js/npm
- MySQL
- Redis, если используется очередь/кеш Redis

Установка:

```bash
cd dms
composer install
cp .env.example .env
php artisan key:generate
npm install
```

Настройте `.env` под локальную БД:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dms
DB_USERNAME=dms_user
DB_PASSWORD=dms_password

FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:5173,localhost:8000,127.0.0.1,127.0.0.1:5173,127.0.0.1:8000
```

Миграции и сидеры:

```bash
php artisan migrate
php artisan db:seed
```

Запуск API:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

Запуск Vite assets:

```bash
npm run dev
```

Альтернативно можно использовать готовый Composer script:

```bash
composer run dev
```

Он одновременно запускает Laravel server, queue listener, Laravel Pail logs и Vite.

## Переменные окружения

Минимально важные переменные:

| Переменная | Назначение |
| --- | --- |
| `APP_URL` | URL backend, по умолчанию `http://localhost:8000` |
| `FRONTEND_URL` | URL frontend, по умолчанию `http://localhost:5173` |
| `DB_*` | подключение к MySQL |
| `REDIS_*` | подключение к Redis |
| `QUEUE_CONNECTION` | драйвер очередей, в `.env.example` стоит `database` |
| `SESSION_DRIVER` | драйвер сессий, в `.env.example` стоит `database` |
| `SANCTUM_STATEFUL_DOMAINS` | домены для Sanctum |
| `MAIL_*` | mail config, локально используется `log` |

Если используются Stripe-платежи, добавьте ключи в `.env` согласно реализации сервисов `Modules/Finance/app/Services/StripeService.php`.

### Локализация новостей и уведомлений

Новости заполняются вручную на трёх языках: русский, казахский и английский. Русский текст хранится в основных полях `title` и `description`, а казахский/английский текст хранится в JSON-поле `translations`.

Frontend отправляет выбранный язык через `Accept-Language`, а API отдаёт локализованные `title` и `description`. Русский оригинал также остаётся в `title_ru` и `description_ru`, чтобы менеджер мог редактировать исходную русскую версию.

Глобальные уведомления работают по той же схеме: русский текст хранится в `title` и `message`, казахский/английский текст хранится в `translations`, а API отдаёт локализованные `title` и `message`.

Перед использованием выполните миграцию:

```bash
php artisan migrate
```

## API

Большинство маршрутов находятся под `/api/v1` и требуют заголовок:

```http
Authorization: Bearer <access_token>
Accept: application/json
```

Auth также доступен в двух вариантах: `/api/*` и `/api/v1/*`.

### Регистрация

```http
POST /api/v1/register
```

```json
{
  "role": "student",
  "email": "student@example.com",
  "password": "password123",
  "phone_number": "+77000000000",
  "lastname": "Ivanov",
  "name": "Ivan",
  "middlename": "Ivanovich",
  "uni_id": "UNI-001",
  "gender": "male"
}
```

Успешный ответ содержит `data.token.access_token`.

### Вход

```http
POST /api/v1/login
```

```json
{
  "email": "student@example.com",
  "password": "password123"
}
```

### Выход

```http
POST /api/v1/logout
Authorization: Bearer <access_token>
```

### Смена пароля

```http
POST /api/v1/reset-password
Authorization: Bearer <access_token>
```

```json
{
  "old_password": "password123",
  "new_password": "newpassword123",
  "confirm_password": "newpassword123"
}
```

## Основные маршруты

| Область | Маршруты |
| --- | --- |
| Auth | `POST /api/v1/register`, `POST /api/v1/login`, `POST /api/v1/logout`, `POST /api/v1/reset-password` |
| Current user | `GET /api/v1/me` |
| Users | `GET/POST /api/v1/users`, `GET/PUT/PATCH/DELETE /api/v1/users/{user}` |
| Notifications | `GET /api/v1/notifications`, `POST /api/v1/notifications/read-all`, `POST /api/v1/notifications/{id}/read` |
| Broadcast notifications | `GET/POST /api/v1/notifications/broadcasts` |
| Dormitory buildings | `GET/POST /api/v1/buildings`, `GET/PUT/PATCH/DELETE /api/v1/buildings/{building}` |
| Dormitory floors | `GET/POST /api/v1/floors`, `GET/PUT/PATCH/DELETE /api/v1/floors/{floor}` |
| Dormitory rooms | `GET/POST /api/v1/rooms`, `GET/PUT/PATCH/DELETE /api/v1/rooms/{room}` |
| Dormitory hierarchy | `GET /api/v1/buildings/{building}/floors`, `GET /api/v1/floors/{floor}/rooms` |
| Settlement | `GET/POST /api/v1/settlements`, `GET/PUT/PATCH/DELETE /api/v1/settlements/{settlement}` |
| Settlement status | `GET /api/v1/settlements/is-living/{userId}`, `GET /api/v1/showStatus/{userId}` |
| Live requests | `GET /api/v1/requests/live`, `GET /api/v1/requests/live/my`, `POST /api/v1/requests/live`, `POST /api/v1/requests/live/{id}/approve`, `POST /api/v1/requests/live/{id}/reject` |
| Change room requests | `GET /api/v1/requests/change-room`, `GET /api/v1/requests/change-room/my`, `POST /api/v1/requests/change-room`, `POST /api/v1/requests/change-room/{id}/approve`, `POST /api/v1/requests/change-room/{id}/reject` |
| Repair requests | `GET /api/v1/repair-requests`, `GET /api/v1/repair-requests/my`, `POST /api/v1/repair-requests`, `POST /api/v1/repair-requests/{id}/start`, `POST /api/v1/repair-requests/{id}/resolve` |
| News | `GET/POST /api/v1/news`, `GET/PUT/PATCH/DELETE /api/v1/news/{news}` |
| Finance | `GET /api/v1/finance/charges`, `POST /api/v1/finance/checkout/{charge}`, `POST /api/v1/finance/checkout/confirm`, `POST /api/v1/finance/webhook` |
| Gym | `GET /api/v1/gym/plans`, `GET /api/v1/gym/membership`, `POST /api/v1/gym/checkout/{plan}`, `POST /api/v1/gym/check-in`, `POST /api/v1/gym/check-out`, `GET /api/v1/gym/stats` |
| Penalties | `GET/POST /api/v1/penalties`, `GET /api/v1/penalties/{id}`, `POST /api/v1/penalties/{id}/redeem`, `POST /api/v1/penalties/{id}/cancel` |
| Penalty management | `GET /api/v1/penalties/manage`, `GET /api/v1/penalties/rules`, `GET /api/v1/penalties/targets`, `GET /api/v1/penalties/rooms`, `POST /api/v1/penalties/redemptions/{id}/approve`, `POST /api/v1/penalties/redemptions/{id}/reject` |
| Buy-sell | `GET /api/v1/buy-sell/meta`, `GET/POST /api/v1/buy-sell/listings`, `GET /api/v1/buy-sell/listings/mine`, `GET/PUT/DELETE /api/v1/buy-sell/listings/{listing}` |

Актуальный список маршрутов можно получить командой:

```bash
php artisan route:list --path=api
```

## Формат ответов

Большая часть API использует helper `result()`:

```json
{
  "status_code": 200,
  "message": "Вход выполнен успешно",
  "data": {}
}
```

Для ошибок API в `bootstrap/app.php` настроены JSON-ответы:

- `401` - неавторизованный пользователь.
- `403` - нет нужной роли.
- `404` - маршрут или объект не найден.
- `422` - ошибки валидации Laravel.

## База данных

Миграции разделены между `database/migrations` и `Modules/*/database/migrations`.

Основные сущности:

- `users`, `personal_access_tokens`
- `buildings`, `floors`, `rooms`
- `dorm_students`, `settlements`
- `request_lives`, `request_change_rooms`, `repair_requests`
- `news`
- `room_types`, `charges`, `payments`
- `gym_plans`, `gym_memberships`, `gym_visits`
- `penalty_rules`, `penalties`, `penalty_evidences`, `penalty_redemptions`
- `buy_sell_listings`
- `notifications`, `system_notifications`

Сидеры:

```bash
php artisan db:seed
```

`DatabaseSeeder` запускает:

- `DormitoryStructureSeeder` - корпуса, этажи, комнаты и типы комнат.
- `GymDatabaseSeeder` - сейчас без автозаполнения, тарифы управляются через Filament.
- тестового пользователя `test@example.com` с фабричным паролем `password`.

## Filament admin panel

Admin panel доступна по пути:

```text
/admin
```

Доступ к панели разрешен пользователям с ролью `admin` через `Modules\User\Models\User::canAccessPanel()`.

## Полезные команды

```bash
# Очистить кеши Laravel
php artisan optimize:clear

# Выполнить миграции
php artisan migrate

# Откатить последнюю группу миграций
php artisan migrate:rollback

# Запустить сидеры
php artisan db:seed

# Посмотреть API routes
php artisan route:list --path=api

# Запустить тесты
php artisan test

# Форматирование PHP-кода
./vendor/bin/pint

# Production-like кеширование
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Тесты

Запуск:

```bash
php artisan test
```

Сейчас в проекте есть базовые example tests и feature-тест `PenaltyManagementTest`.

## Разработка нового модуля

Проект использует `nwidart/laravel-modules`. Типичная структура модуля:

```text
Modules/Example/
├── app/
│   ├── Http/Controllers/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   ├── api.php
│   └── web.php
├── composer.json
└── module.json
```

После добавления новых классов или модуля:

```bash
composer dump-autoload
php artisan optimize:clear
```

## Docker troubleshooting

Если контейнеры запущены, но backend не отвечает:

```bash
cd ../nginx
docker compose ps
docker compose logs -f backend
```

Если изменились Composer зависимости:

```bash
cd ../nginx
docker compose exec backend composer install
docker compose exec backend php artisan optimize:clear
```

Если нужно пересобрать backend:

```bash
cd ../nginx
docker compose up -d --build backend
```
