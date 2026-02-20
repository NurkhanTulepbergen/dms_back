# DMS Backend (Laravel 12) — актуальный контекст проекта для GPT

Это backend системы управления общежитиями (DMS) на Laravel 12.

## Коротко о проекте
- Приложение: `dms/`
- Архитектура: модульная (`nwidart/laravel-modules`)
- Доменная логика и API: `dms/Modules/*`
- Точка истины по проживанию: `settlements` (`end_at IS NULL` = активное проживание)

Связанные документы:
- `dms/ERD.md`
- `dms/API_ENDPOINTS.md`

## Стек
- PHP: `^8.2` (`dms/composer.json`)
- Laravel: `^12.0`
- Auth: Sanctum
- Модули: `nwidart/laravel-modules`
- Платежи: Stripe (`stripe/stripe-php`)

## Структура репозитория
- Корень репо содержит папку `dms/` (Laravel app)
- `dms/app` — глобальные части приложения (helpers, middleware, exceptions)
- `dms/Modules/*` — модульные контроллеры/сервисы/модели/роуты/миграции
- `dms/config` — конфиги, включая `modules.php`, `services.php`
- `dms/database/migrations` — базовые миграции Laravel
- `dms/routes/api.php` — фактически пустой (API загружается из модулей)

## Bootstrap и глобальное поведение
Файл: `dms/bootstrap/app.php`
- Подключает helper `result()` (`require_once`)
- Регистрирует middleware alias `role` => `App\Http\Middleware\RoleMiddleware`
- Кастомно рендерит исключения в JSON:
  - `BusinessException`
  - `AuthenticationException`
  - `NotFoundHttpException` (+ `ModelNotFoundException`)

## Формат ответов
Helper: `dms/app/Helpers/result.php`

Ожидаемый формат:
```json
{
  "status_code": 200,
  "message": "...",
  "data": {}
}
```

Важно: часть контроллеров местами возвращает `response()->json()`/`response()->noContent()` напрямую, поэтому унификация не 100%.

## Роутинг
Все модульные `RouteServiceProvider` мапят API одинаково:
- middleware: `api`
- prefix: `api`
- name prefix: `api.`

Итог:
- Все endpoint’ы начинаются с `/api/*`
- Большинство под `/api/v1/*`
- Auth дублирует часть endpoint’ов без версии (`/api/login`, `/api/register`, ...)

Проверка на момент обновления:
- `php artisan route:list --path=api` => 55 API-маршрутов

## Auth и роли
### Sanctum
- Токены создаются в `Modules/Auth/Http/Controllers/AuthController.php`
- Клиент использует `Authorization: Bearer <token>`

### Роли
- Хранятся в `users.role`
- Проверяются middleware `role` (`dms/app/Http/Middleware/RoleMiddleware.php`)

## Доменные модули
### Auth (`dms/Modules/Auth`)
- register/login/logout/reset-password
- есть v1 и default-роуты

### User (`dms/Modules/User`)
- `GET /api/v1/me`
- `GET /api/v1/users`
- `GET /api/v1/users/{user}`

### News (`dms/Modules/News`)
- Read: любой авторизованный
- Write: `role:admin,manager`

### Dormitory (`dms/Modules/Dormitory`)
- CRUD по `buildings`, `floors`, `rooms`
- Write-операции: `role:admin,manager`
- Доп. роуты иерархии:
  - `buildings/{building}/floors`
  - `floors/{floor}/rooms`

Ключевые проверки в сервисах:
- `BuildingService`: нельзя уменьшить `total_floors` ниже фактического количества этажей
- `FloorService`: этаж не выше `building.total_floors`, уникален в пределах здания
- `RoomService`: `live_cap <= capacity`, уникальность `room_number` в пределах этажа, `room_type_id` обязателен

### Requests (`dms/Modules/Requests`)
- Student:
  - `POST /api/v1/requests/live`
  - `POST /api/v1/requests/change-room`
- Manager/Admin:
  - список + approve/reject для обоих типов заявок

Ключевая логика:
- `RequestLiveService`
  - запрет второй активной заявки
  - запрет подачи при активном settlement
  - проверка мест по активным `settlements`
  - approve создаёт settlement через `SettlementService`
- `RequestChangeRoomService`
  - проверка pending-заявки
  - approve вызывает `SettlementService::relocate`

### Settlement (`dms/Modules/Settlement`)
- Полный `apiResource` под `/api/v1/settlements`
- `DELETE` логически отключен (возвращает `405`)

`SettlementService`:
- создаёт settlement с проверками пола, активности комнаты/этажа, gender policy, вместимости
- закрывает settlement (`end_reason`)
- делает переселение (`close + create`)
- после создания settlement вызывает `ChargeService::createSemesterCharge`

### Finance (`dms/Modules/Finance`)
- `POST /api/v1/finance/checkout/{charge}` (`auth:sanctum`)
- `POST /api/v1/finance/webhook` (без auth)

`checkout`:
- только для начисления текущего пользователя со статусом `pending`
- создаёт Stripe Session и запись в `payments`

`webhook`:
- ожидает `Stripe-Signature`
- при `checkout.session.completed` переводит payment в `succeeded`, charge в `paid`

Конфиг Stripe:
- `dms/config/services.php`
  - `services.stripe.secret`
  - `services.stripe.webhook_secret`

### Penalty (`dms/Modules/Penalty`)
- Маршруты под `/api/v1/penalties` (`auth:sanctum`)
- Student:
  - `GET /api/v1/penalties`
  - `GET /api/v1/penalties/{id}`
  - `POST /api/v1/penalties/{id}/redeem`
- Manager/Admin:
  - `POST /api/v1/penalties`
  - `POST /api/v1/penalties/{id}/cancel`
  - `POST /api/v1/penalties/redemptions/{id}/approve`
  - `POST /api/v1/penalties/redemptions/{id}/reject`

Ключевая логика:
- `PenaltyService`:
  - создаёт штраф по правилу (`penalty_rules`)
  - привязывает к активному `settlement`
  - добавляет `penalty_evidences`
  - при `creates_financial_charge=true` вызывает `ChargeService::createPenaltyCharge(...)`
  - после создания запускает `DisciplinePolicyService`
- `DisciplinePolicyService`:
  - считает сумму активных баллов (`penalties.status=active`)
  - если `active_points >= users.discipline_limit`, закрывает активное заселение (`end_reason=discipline`)
- `RedemptionService`:
  - создаёт redemption (`pending`)
  - approve => redemption `approved`, penalty `resolved`
  - reject => redemption `rejected`

## База данных (смысловые таблицы)
- Пользователи: `users`, `dorm_students`
- Общежитие: `buildings`, `floors`, `rooms`
- Заявки: `request_lives`, `documents`, `request_change_rooms`
- Проживание: `settlements`
- Новости: `news`
- Финансы: `room_types`, `charges`, `payments`
- Дисциплина: `penalty_rules`, `penalties`, `penalty_evidences`, `penalty_redemptions`

Важно:
- Миграция `create_room_types_id_tables` существует исторически, но таблица удаляется миграцией `drop_room_types_id_table`.

## Статус модулей
`dms/modules_statuses.json`:
- `User`, `Auth`, `News`, `Dormitory`, `Requests`, `Settlement`, `Finance`, `Penalty` = `true`

## Docker и запуск
- `dms/docker-compose.yml`:
  - `app` (php-fpm)
  - `nginx` (`8000:80`)
- DB по умолчанию в `.env.example`: SQLite
- В `docker-compose` и `docker/entrypoint.sh` частично дублируется bootstrap (composer install/migrate), это стоит учитывать при поддержке

## Что править при изменениях
- API роуты: `dms/Modules/*/routes/api.php`
- Контроллеры: `dms/Modules/*/app/Http/Controllers`
- Бизнес-логика: `dms/Modules/*/app/Services`
- Миграции: `dms/Modules/*/database/migrations`
- Глобальные ответы/ошибки/middleware: `dms/bootstrap/app.php`, `dms/app/*`
