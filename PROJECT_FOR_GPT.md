# DMS Backend (Laravel 12) — подробное описание проекта (для GPT)

Это backend DMS (управление общежитиями) на Laravel 12. Проект модульный: API и бизнес-логика живут в модулях `dms/Modules/*` (пакет `nwidart/laravel-modules`). Файл `dms/routes/api.php` пустой: роутинг подключается из модулей.

Документы по структуре данных:
- ERD: `dms/ERD.md` (собрано по миграциям).
- API endpoints: `dms/API_ENDPOINTS.md`.

## Стек
- PHP: `^8.2` по `dms/composer.json`.
- Laravel: `^12.0`.
- Auth: Laravel Sanctum (`personal_access_tokens`).
- Платежи: Stripe (`stripe/stripe-php`).
- Модули: `nwidart/laravel-modules`.

Важно про Docker: `dms/Dockerfile` использует `php:8.4-fpm` (это выше, чем заявлено в `composer.json`, но совместимо с `^8.2`).

## Репозиторий и каталоги
- Корень репо: содержит папку `dms/` (это Laravel-приложение).
- `dms/app`: базовый код приложения (helpers, middleware, exceptions, base модели).
- `dms/Modules/*`: доменные модули (Controllers/Models/Services/Routes/Migrations).
- `dms/database/migrations`: базовые миграции Laravel + `news`, кастомные поля `users`, таблица Sanctum.
- `dms/bootstrap`: входная конфигурация приложения.
- `dms/config`: конфиги Laravel и `config/modules.php` для nwidart.

## Как приложение загружается (bootstrapping)
Главные точки:
- `dms/bootstrap/app.php`
  - подключает helper `result()` (через `require_once`).
  - регистрирует alias middleware `role` -> `App\Http\Middleware\RoleMiddleware`.
  - задаёт кастомный рендер исключений (BusinessException, AuthenticationException, NotFoundHttpException/ModelNotFoundException) и возвращает JSON в едином формате.
- `dms/bootstrap/providers.php`
  - включает `Nwidart\Modules\LaravelModulesServiceProvider::class`, который отвечает за загрузку модулей.

Дополнительно есть `dms/app/Exceptions/Handler.php` с похожими renderable-правилами. В Laravel 12 фактически важнее то, что задано в `bootstrap/app.php` через `->withExceptions(...)` (возможна логическая “дубль-настройка”).

## Модули: как устроены и как подключают API
Модуль = папка в `dms/Modules/<ModuleName>` со стандартной структурой:
- `app/Http/Controllers/*Controller.php`
- `app/Models/*`
- `app/Services/*` (если вынесена бизнес-логика)
- `database/migrations/*`
- `routes/api.php` (и `routes/web.php`, если нужно)
- `module.json` (провайдеры модуля)
- `app/Providers/RouteServiceProvider.php`

Роуты модулей подключаются через `RouteServiceProvider` каждого модуля. Он делает:
- `Route::middleware('api')->prefix('api')->name('api.')->group(module_path(..., '/routes/api.php'))`

То есть все модульные API-роуты имеют общий префикс:
- **все endpoint’ы начинаются с `/api/...`**

Статусы включения модулей лежат в `dms/modules_statuses.json` (FileActivator). Там есть запись `Settlment` (опечатка) рядом с `Settlement`.

## Общий формат JSON-ответов
Есть helper `result()` в `dms/app/Helpers/result.php` (также подключён в autoload `"files"` в `dms/composer.json`):
```json
{
  "status_code": 200,
  "message": "...",
  "data": {}
}
```
Если message не передан, он выбирается по `trans('messages.http_errors.<code>')` (200/201/202/400/401/403/404/429/500).

Не все контроллеры строго используют `result()` (например, `RequestChangeRoomController` часто возвращает `response()->json(...)` напрямую). Поэтому на практике ответы местами не унифицированы.

## Ошибки и исключения
### BusinessException
Кастомная бизнес-ошибка: `dms/app/Exceptions/BusinessException.php`.
- содержит `status_code` (по умолчанию 422),
- перехватывается глобально и превращается в `result(null, status_code, message)`.

### Типовые исключения
В `dms/bootstrap/app.php` настроены ответы:
- `AuthenticationException` -> `401` с сообщением “Неавторизованный пользователь”
- `ModelNotFoundException` -> `404` “Объект не найден”
- `NotFoundHttpException` -> `404` “Маршрут не найден”

## Auth (Sanctum) и роли
### Токены Sanctum
Модуль `Auth` создаёт токены через `$user->createToken('auth_token')->plainTextToken`.
Клиент должен передавать:
- `Authorization: Bearer <token>`

Таблица: `personal_access_tokens` (миграция `dms/database/migrations/...create_personal_access_tokens_table.php`).

### Роли
Роль хранится в `users.role` (строка, default `student`).
Проверка ролей делается middleware `role` (`dms/app/Http/Middleware/RoleMiddleware.php`):
- сверяет `$request->user()->role` с разрешёнными ролями из параметров `role:admin,manager` и т.д.

В проекте также есть `Modules\User\Http\Middleware\RoleMiddleware`, но alias `role` указывает на `App\Http\Middleware\RoleMiddleware` (модульный, судя по текущей конфигурации, не используется).

## База данных (фактическая схема)
Схема собирается из миграций. Основные доменные таблицы:
- `buildings`, `floors`, `rooms` (иерархия общежитий)
- `dorm_students` (доп. сущность студента в общежитии, `user_id` = PK)
- `request_lives`, `request_change_rooms` (заявки)
- `settlements` (актуальное проживание + история)
- `news`
- `charges`, `payments` (финансы)
- `room_types`, `room_types_id` (финансовые справочники; в текущих миграциях есть дублирование)

Подробно: `dms/ERD.md`.

Важно: **источник истины по проживанию** = таблица `settlements`. Студент живёт в комнате тогда и только тогда, когда есть **ровно одна** запись `settlements` с `settlements.user_id = users.id` и `end_at IS NULL`.

## Модули и их API
Важное про префиксы (итоговые URL):
- Все модульные роуты подключаются с префиксом **`/api`** (см. `RouteServiceProvider` модулей).
- Почти все API-роуты находятся под **`/api/v1/...`** (внутри модулей используется `Route::prefix('v1')` или `prefix('v1/<module>')`).
- Исключение: модуль **Auth** дополнительно дублирует основные маршруты **без версии** (то есть есть и `/api/v1/*`, и `/api/*`).

### Auth (`dms/Modules/Auth`)
Routes: `dms/Modules/Auth/routes/api.php`
- `POST /api/v1/register` и `POST /api/register`
  - валидация: `role` in `admin|student|manager|employee`, `email` unique, `password` min 8, `phone_number`, ФИО, `uni_id` unique
  - создаёт пользователя `Modules\User\Models\User`
  - возвращает `result({ token, user }, 201, ...)`
- `POST /api/v1/login` и `POST /api/login`
  - возвращает токен (Sanctum)
  - при неверных кредах возвращает `{"message":"Invalid Credentials"}` с `401` (не через `result()`)
- `POST /api/v1/logout` и `POST /api/logout` (middleware `auth:sanctum`)
  - удаляет текущий токен, `result(null, 204)`
- `POST /api/v1/reset-password` и `POST /api/reset-password` (middleware `auth:sanctum`)
  - проверяет `old_password`, обновляет пароль

Код: `dms/Modules/Auth/app/Http/Controllers/AuthController.php`.

### User (`dms/Modules/User`)
Routes: `dms/Modules/User/routes/api.php` (middleware `auth:sanctum`, только `v1`)
- `GET /api/v1/me` — текущий пользователь
- `GET /api/v1/users` — список пользователей
- `GET /api/v1/users/{user}` — пользователь по id

Код: `dms/Modules/User/app/Http/Controllers/UserController.php`.

### News (`dms/Modules/News`)
Routes: `dms/Modules/News/routes/api.php`
- Только `v1` (auth:sanctum):
  - `GET /api/v1/news`, `GET /api/v1/news/{news}`
  - `POST /api/v1/news`, `PUT/PATCH /api/v1/news/{news}`, `DELETE /api/v1/news/{news}` (дополнительно `role:admin,manager`)

Код: `dms/Modules/News/app/Http/Controllers/NewsController.php`.

### Dormitory (`dms/Modules/Dormitory`)
Routes: `dms/Modules/Dormitory/routes/api.php` (middleware `auth:sanctum`, только `v1`)
- CRUD (read-only для всех; write только `role:admin,manager`):
  - Buildings: `GET /api/v1/buildings`, `GET /api/v1/buildings/{building}`, `POST/PUT/PATCH/DELETE /api/v1/buildings/...` (admin/manager)
  - Floors: `GET /api/v1/floors`, `GET /api/v1/floors/{floor}`, `POST/PUT/PATCH/DELETE /api/v1/floors/...` (admin/manager)
  - Rooms: `GET /api/v1/rooms`, `GET /api/v1/rooms/{room}`, `POST/PUT/PATCH/DELETE /api/v1/rooms/...` (admin/manager)
- Иерархия (read-only):
  - `GET /api/v1/buildings/{building}/floors`
  - `GET /api/v1/floors/{floor}/rooms`

Бизнес-правила вынесены в сервисы:
- `dms/Modules/Dormitory/app/Services/BuildingService.php`
  - `total_floors` нельзя уменьшить ниже текущего количества этажей
- `dms/Modules/Dormitory/app/Services/FloorService.php`
  - `floor_number` <= `building.total_floors`
  - `floor_number` уникален в рамках здания
  - при запросе этажей/комнат и пустом результате кидает `ModelNotFoundException` (уходит в 404)
- `dms/Modules/Dormitory/app/Services/RoomService.php`
  - `live_cap` <= `capacity`
  - `room_number` уникален в рамках этажа

### Requests (`dms/Modules/Requests`)
Routes: `dms/Modules/Requests/routes/api.php`

Студент (middleware `auth:sanctum` + `role:student`):
- `POST /api/v1/requests/live`
- `POST /api/v1/requests/change-room`

Менеджер/админ (middleware `auth:sanctum` + `role:manager,admin`):
- `GET /api/v1/requests/live`
- `POST /api/v1/requests/live/{id}/approve`
- `POST /api/v1/requests/live/{id}/reject`
- `GET /api/v1/requests/change-room`
- `POST /api/v1/requests/change-room/{id}/approve`
- `POST /api/v1/requests/change-room/{id}/reject`

#### RequestLive (заявка на проживание)
- Контроллер: `dms/Modules/Requests/app/Http/Controllers/RequestLiveController.php`
- Сервис: `dms/Modules/Requests/app/Services/RequestLiveService.php`
  - запрет второй активной заявки (`pending` или `accepted`)
  - проверка свободных мест в комнате через **активные `settlements`** (считает `settlements` с `room_id` и `end_at IS NULL`)
  - approve: создаёт запись в `settlements` через `SettlementService::createSettlement(..., source=request_live)`, затем ставит заявке `accepted`
  - reject: ставит `rejected`
  - ошибки бросает через `BusinessException` => единый `result()`

#### RequestChangeRoom (заявка на смену комнаты)
- Контроллер: `dms/Modules/Requests/app/Http/Controllers/RequestChangeRoomController.php`
  - использует сервис `dms/Modules/Requests/app/Services/RequestChangeRoomService.php`
  - approve делает переселение через `SettlementService`:
    - закрывает активное заселение (`end_reason=relocation`)
    - создаёт новое заселение (`source=relocation`)
  - `dorm_students` используется только как профиль (без room_id)

Сервис: `dms/Modules/Requests/app/Services/RequestChangeRoomService.php` (создание заявки, approve/reject; заселение/переселение делается через `SettlementService`).

### Settlement (`dms/Modules/Settlement`)
Routes: `dms/Modules/Settlement/routes/api.php`:
- `/api/v1/settlements` (auth:sanctum)

`SettlementController` реализован как API (JSON через `result()`):
- `dms/Modules/Settlement/app/Http/Controllers/SettlementController.php`.

### Finance (`dms/Modules/Finance`)
Routes: `dms/Modules/Finance/routes/api.php`
- `POST /api/v1/finance/checkout/{charge}` (middleware `auth:sanctum`)
  - создаёт Stripe Checkout Session для `charges.id={charge}` текущего пользователя (`status=pending`)
  - создаёт запись в `payments` (`pending`)
  - возвращает URL для редиректа на Stripe Checkout
- `POST /api/v1/finance/webhook` (без auth)
  - обрабатывает `checkout.session.completed`
  - помечает платеж `payments.status=succeeded`, проставляет `paid_at`
  - обновляет начисление `charges.status=paid`

Код:
- `dms/Modules/Finance/app/Http/Controllers/FinanceController.php`
- `dms/Modules/Finance/app/Services/StripeService.php`
- `dms/Modules/Finance/app/Services/ChargeService.php`

## Запуск в Docker (как сейчас устроено)
Файлы:
- `dms/docker-compose.yml`: сервисы `app` (php-fpm) + `nginx` (порт `8000:80`)
- `dms/docker/entrypoint.sh`: bootstrap (создание `.env`, `composer install`, `key:generate`, SQLite файл, `migrate --force`)

Нюанс: в `docker-compose.yml` у `app` задан `command`, который тоже делает `composer install` и `php artisan migrate --force`, а `ENTRYPOINT` в Dockerfile делает это же. В итоге bootstrap может выполняться дважды (стоит привести к одному источнику истины).

DB по умолчанию: SQLite (`DB_CONNECTION=sqlite` в `dms/.env.example`), файл `dms/database/database.sqlite` создаётся entrypoint’ом.

## Где искать/править логику
- Роуты модуля: `dms/Modules/<Module>/routes/api.php`
- Контроллеры: `dms/Modules/<Module>/app/Http/Controllers/*`
- Сервисы (где есть): `dms/Modules/<Module>/app/Services/*`
- Модели: `dms/Modules/<Module>/app/Models/*`
- Миграции модуля: `dms/Modules/<Module>/database/migrations/*`
- Глобальные middleware/exceptions/helpers: `dms/app/*`

## Текущее состояние и “острые углы” (важно для GPT/ревью)
- Ответы API местами не унифицированы: часть кода использует `result()`, часть возвращает `response()->json()` напрямую.
- `rooms.live_cap` присутствует в модели/валидации CRUD, но фактическая занятость/наличие мест проверяется через активные `settlements` (поэтому `live_cap` может быть просто “ручным полем” и легко стать неактуальным).
- Residence + history хранятся в `settlements` (отдельного `SettlementHistory` модуля нет).
- В Finance модуле есть технические несостыковки (например дублирующая таблица `room_types_id`, неполная связность `Room` <-> `RoomType`, отсутствие `stripe` секции в `config/services.php`) — перед продом нужен отдельный рефакторинг/проверка.
