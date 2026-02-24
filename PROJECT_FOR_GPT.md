# DMS Backend (Laravel 12) — актуальный контекст проекта для GPT

Обновлено по текущему коду на **24 февраля 2026**.

## Коротко о проекте
- Это backend системы управления общежитиями (DMS).
- Архитектура: модульная (`nwidart/laravel-modules`).
- Основная доменная логика и API находятся в `Modules/*`.
- Точка истины по факту проживания: таблица `settlements` (`end_at IS NULL` = активное проживание).

Связанные документы:
- `ERD.md`
- `API_ENDPOINTS.md`

## Стек
- PHP: `^8.2`
- Laravel: `^12.0`
- Filament: `^5.2` (админ-панель)
- Auth API: Sanctum
- Модули: `nwidart/laravel-modules`
- Платежи: Stripe (`stripe/stripe-php`)

## Структура репозитория
- `app/` — глобальные части приложения (helpers, middleware, модели, providers)
- `bootstrap/app.php` — bootstrap + глобальная обработка исключений
- `Modules/*` — модули (контроллеры, сервисы, модели, роуты, миграции, Filament-ресурсы)
- `config/` — конфиги (`modules.php`, `services.php` и т.д.)
- `database/` — глобальные миграции/seeders
- `routes/api.php` — пустой (основной API загружается из модулей)
- `routes/web.php` — web/отладочные маршруты + welcome

## Глобальное поведение (bootstrap)
Файл: `bootstrap/app.php`
- Подключает helper `result()` через `require_once app/Helpers/result.php`.
- Регистрирует middleware alias: `role` => `App\Http\Middleware\RoleMiddleware`.
- Кастомно рендерит JSON-ошибки для API (`api/*`):
  - `BusinessException`
  - `AuthenticationException`
  - `NotFoundHttpException` (включая `ModelNotFoundException`)
- Для web/Filament маршрутов оставляет стандартное Laravel-поведение.

## Формат API-ответов
Helper: `app/Helpers/result.php`

Стандартный ответ:
```json
{
  "status_code": 200,
  "message": "...",
  "data": {}
}
```

Важно: часть контроллеров может возвращать `response()->json()`/`response()->noContent()` напрямую, поэтому формат не везде строго унифицирован.

## Роутинг
- Каждый модульный `RouteServiceProvider` регистрирует API с:
  - middleware: `api`
  - prefix: `api`
  - name prefix: `api.`
- Практически весь продуктовый API расположен под `/api/v1/*`.
- В модуле `Auth` есть дубли endpoint-ов без версии (`/api/login`, `/api/register`, ...).

Проверка на текущий момент:
- `php artisan route:list --path=api` => **55** API-маршрутов.

## Auth, роли и доступ
### Sanctum (API)
- Токены создаются в `Modules/Auth/app/Http/Controllers/AuthController.php`.
- Клиент использует `Authorization: Bearer <token>`.

### Роли
- Роль хранится в `users.role`.
- API-проверки ролей идут через middleware `role`.

### Filament (админка)
- Панель: `admin`
- URL: `/admin`
- Guard: `web`
- Доступ к панели: только `admin` (см. `app/Models/User.php::canAccessPanel()`).
- Ресурсы подгружаются из модулей: Dormitory, News, User, Settlement, Requests, Finance, Penalty.

## Доменные модули (API)
### Auth (`Modules/Auth`)
- `register/login/logout/reset-password`
- Есть `v1` и default-роуты без версии.

### User (`Modules/User`)
- `GET /api/v1/me`
- `GET /api/v1/users`
- `GET /api/v1/users/{user}`

### News (`Modules/News`)
- Read: любой авторизованный (`auth:sanctum`)
- Write: `role:admin,manager`

### Dormitory (`Modules/Dormitory`)
- CRUD для `buildings`, `floors`, `rooms`
- Write-операции: `role:admin,manager`
- Иерархические read-роуты:
  - `GET /api/v1/buildings/{building}/floors`
  - `GET /api/v1/floors/{floor}/rooms`

Ключевые проверки:
- `BuildingService`: нельзя уменьшить `total_floors` ниже фактического числа этажей.
- `FloorService`: этаж не выше `building.total_floors`, и уникален в пределах здания.
- Filament форма этажа (`FloorForm`): при создании/редактировании `floor_number` не может быть больше `building.total_floors`.
- `RoomService`: `live_cap <= capacity`, `room_number` уникален в пределах этажа, `room_type_id` обязателен.

### Requests (`Modules/Requests`)
- Student:
  - `POST /api/v1/requests/live`
  - `POST /api/v1/requests/change-room`
- Manager/Admin:
  - список + approve/reject для обоих типов заявок.

Ключевая логика:
- `RequestLiveService`:
  - запрет второй активной заявки,
  - запрет подачи при активном settlement,
  - проверка мест по активным `settlements`,
  - approve создаёт settlement через `SettlementService`.
- `RequestChangeRoomService`:
  - проверка pending-заявки,
  - approve вызывает `SettlementService::relocate`.

### Settlement (`Modules/Settlement`)
- `Route::apiResource('settlements', ...)` под `/api/v1/settlements`.
- `DELETE` отключен логически (возвращает `405`).

`SettlementService`:
- создаёт settlement с проверками пола/активности/политики этажа/вместимости;
- закрывает settlement (`end_reason`);
- переселяет (`close + create`);
- после создания вызывает `ChargeService::createSemesterCharge`.

### Finance (`Modules/Finance`)
- `POST /api/v1/finance/checkout/{charge}` (`auth:sanctum`)
- `POST /api/v1/finance/webhook` (без auth)

Ключевая логика:
- `checkout`: только для начисления текущего пользователя со статусом `pending`; создаёт Stripe Checkout Session + запись в `payments`.
- `webhook`: ожидает `Stripe-Signature`; при `checkout.session.completed` переводит payment в `succeeded`, charge в `paid`.

Stripe-конфиг:
- `config/services.php`
  - `services.stripe.secret`
  - `services.stripe.webhook_secret`

### Penalty (`Modules/Penalty`)
Маршруты под `/api/v1/penalties` (`auth:sanctum`):
- Student:
  - `GET /`
  - `GET /{id}`
  - `POST /{id}/redeem`
- Manager/Admin:
  - `POST /`
  - `POST /{id}/cancel`
  - `POST /redemptions/{id}/approve`
  - `POST /redemptions/{id}/reject`

Ключевая логика:
- `PenaltyService`:
  - создаёт штраф по `penalty_rules`,
  - привязывает к активному `settlement`,
  - добавляет `penalty_evidences`,
  - при `creates_financial_charge=true` вызывает `ChargeService::createPenaltyCharge(...)`,
  - запускает `DisciplinePolicyService`.
- `DisciplinePolicyService`:
  - считает активные баллы,
  - при превышении `users.discipline_limit` закрывает активное заселение (`end_reason=discipline`).
- `RedemptionService`:
  - создаёт redemption (`pending`),
  - approve => redemption `approved`, penalty `resolved`,
  - reject => redemption `rejected`.

## База данных (смысловые таблицы)
- Пользователи: `users`, `dorm_students`
- Общежитие: `buildings`, `floors`, `rooms`
- Заявки: `request_lives`, `documents`, `request_change_rooms`
- Проживание: `settlements`
- Новости: `news`
- Финансы: `room_types`, `charges`, `payments`
- Дисциплина: `penalty_rules`, `penalties`, `penalty_evidences`, `penalty_redemptions`

Важно:
- Историческая миграция `create_room_types_id_tables` есть, но таблица удаляется миграцией `drop_room_types_id_table`.

## Статус модулей
`modules_statuses.json`:
- `User`, `Auth`, `News`, `Dormitory`, `Requests`, `Settlement`, `Finance`, `Penalty` = `true`

## Web-часть и отладочные роуты
`routes/web.php` содержит:
- `/` => `welcome`
- debug-роуты (`/_debug/logtest`, `/_debug/filament`, `/_debug/gates`, `/_debug/buildings-access`)

Важно: debug-маршруты лучше отключать в production.

## Docker и запуск
- `Dockerfile` собирает PHP 8.4 CLI образ, ставит зависимости, запускает `/entrypoint.sh`.
- `entrypoint.sh`:
  - чистит/кеширует конфиг,
  - выполняет `php artisan migrate --force`,
  - поднимает `php artisan serve --host=0.0.0.0 --port=${PORT:-8080}`.
- `docker-compose.yml`:
  - `app` (build из текущего проекта)
  - `nginx` (`8000:80`)

Примечание: в `docker-compose.yml` для `app` указан запуск `php-fpm`, а Dockerfile/entrypoint ориентирован на `php artisan serve`; стоит держать это согласованным в дальнейшем.

## Что менять при развитии проекта
- API-роуты: `Modules/*/routes/api.php`
- Web-роуты: `routes/web.php`, `Modules/*/routes/web.php`
- Контроллеры: `Modules/*/app/Http/Controllers`
- Бизнес-логика: `Modules/*/app/Services`
- Миграции: `Modules/*/database/migrations`, `database/migrations`
- Админка Filament: `app/Providers/Filament/AdminPanelProvider.php`, `Modules/*/app/Filament/Resources`
- Глобальные ответы/исключения/middleware: `bootstrap/app.php`, `app/Helpers/result.php`, `app/Http/Middleware/*`
