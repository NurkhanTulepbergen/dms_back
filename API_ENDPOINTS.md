# DMS API Endpoints

Базовый префикс для всех модульных API-роутов: **`/api`** (все модули мапятся с `prefix('api')` в `RouteServiceProvider`).

Формат ответа (стандарт проекта):
```json
{
  "status_code": 200,
  "message": "...",
  "data": {}
}
```

Аутентификация:
- Laravel Sanctum (Bearer token)
- Заголовок: `Authorization: Bearer <token>`

Роли:
- Middleware `role` проверяет `users.role`
- Примеры: `role:student`, `role:admin,manager`, `role:manager,admin`

## Доменная логика (актуально)
- Проживание: **только через `settlements`**
  - Активное проживание: `settlements.user_id = users.id` и `settlements.end_at IS NULL`
  - История проживания хранится в `settlements` (отдельный settlement_history не используется в домене)
- `dorm_students` = профиль (например `warning_count`), без хранения комнаты/статуса проживания
- Вместимость: источник истины `rooms.capacity`, занятость вычисляется по активным `settlements`
- Пол:
  - `users.gender`: `male|female` (обязателен при approve заселения/переселения)
  - `floors.gender_policy`: `male|female|mixed` (mixed разрешает всех)
- Комната должна быть активна: `rooms.is_active=true` (проверяется в SettlementService)
- Этаж должен быть активен: `floors.is_active=true` (проверяется в SettlementService)

## Как читать документ
Ниже перечислены **фактические routes из файлов** `dms/Modules/*/routes/api.php`.

Где указано "v1", это означает префикс **`/api/v1/...`**.

## Auth
Файл: `dms/Modules/Auth/routes/api.php`

**v1 (`/api/v1/...`)**
- POST `/api/v1/register`
  - Body:
    ```json
    {
      "role": "student",
      "email": "user@example.com",
      "password": "password12345",
      "phone_number": "+7...",
      "lastname": "Ivanov",
      "name": "Ivan",
      "middlename": "Ivanovich",
      "uni_id": "U12345",
      "gender": "male"
    }
    ```
- POST `/api/v1/login`
  - Body:
    ```json
    {
      "email": "user@example.com",
      "password": "password12345"
    }
    ```
- POST `/api/v1/logout` (auth:sanctum)
- POST `/api/v1/reset-password` (auth:sanctum)
  - Body:
    ```json
    {
      "old_password": "old",
      "new_password": "newpassword12345",
      "confirm_password": "newpassword12345"
    }
    ```

**Default (без версии)**
- POST `/api/register`
  - Body: как `/api/v1/register`
- POST `/api/login`
  - Body: как `/api/v1/login`
- POST `/api/logout` (auth:sanctum)
- POST `/api/reset-password` (auth:sanctum)
  - Body: как `/api/v1/reset-password`

Примечание: `POST /login` при неверных кредах возвращает `401` не через `result()` (в текущем контроллере).

## User
Файл: `dms/Modules/User/routes/api.php`

**v1 (`/api/v1/...`)** (auth:sanctum)
- GET `/api/v1/me`
- GET `/api/v1/users`
- GET `/api/v1/users/{user}`

Body: отсутствует.

## News
Файл: `dms/Modules/News/routes/api.php`

**v1 (`/api/v1/...`)** (auth:sanctum)
- GET `/api/v1/news`
- GET `/api/v1/news/{news}`
- POST `/api/v1/news` (role:admin,manager)
  - Body:
    ```json
    {
      "title": "Заголовок",
      "description": "Текст",
      "photo": "https://..."
    }
    ```
- PUT/PATCH `/api/v1/news/{news}` (role:admin,manager)
  - Body:
    ```json
    {
      "title": "Заголовок",
      "description": "Текст",
      "photo": "https://..."
    }
    ```
- DELETE `/api/v1/news/{news}` (role:admin,manager)
  - Body: отсутствует.

## Dormitory
Файл: `dms/Modules/Dormitory/routes/api.php`

**v1 (`/api/v1/...`)** (auth:sanctum)
- Buildings (read):
  - GET `/api/v1/buildings`
  - GET `/api/v1/buildings/{building}`
- Buildings (write, role:admin,manager):
  - POST `/api/v1/buildings`
    - Body:
      ```json
      {
        "address": "Адрес",
        "total_floors": 9
      }
      ```
  - PUT/PATCH `/api/v1/buildings/{building}`
    - Body:
      ```json
      {
        "address": "Адрес",
        "total_floors": 9
      }
      ```
  - DELETE `/api/v1/buildings/{building}`
    - Body: отсутствует.
- Floors (read):
  - GET `/api/v1/floors`
  - GET `/api/v1/floors/{floor}`
- Floors (write, role:admin,manager):
  - POST `/api/v1/floors`
    - Body:
      ```json
      {
        "building_id": 1,
        "floor_number": 1
      }
      ```
  - PUT/PATCH `/api/v1/floors/{floor}`
    - Body:
      ```json
      {
        "floor_number": 2
      }
      ```
  - DELETE `/api/v1/floors/{floor}`
    - Body: отсутствует.
- Rooms (read):
  - GET `/api/v1/rooms`
  - GET `/api/v1/rooms/{room}`
- Rooms (write, role:admin,manager):
  - POST `/api/v1/rooms`
    - Body:
      ```json
      {
        "floor_id": 1,
        "room_number": "101",
        "capacity": 3,
        "live_cap": 0
      }
      ```
  - PUT/PATCH `/api/v1/rooms/{room}`
    - Body:
      ```json
      {
        "room_number": "101",
        "capacity": 3,
        "live_cap": 0
      }
      ```
  - DELETE `/api/v1/rooms/{room}`
    - Body: отсутствует.
- Housing hierarchy (read-only):
  - GET `/api/v1/buildings/{building}/floors`
  - GET `/api/v1/floors/{floor}/rooms`

Body: отсутствует.

## Requests
Файл: `dms/Modules/Requests/routes/api.php`

**v1 (`/api/v1/...`)** (auth:sanctum)
- Student (role:student):
  - POST `/api/v1/requests/live`
    - Body:
      ```json
      {
        "preferred_room_id": 1,
        "documents": [
          { "type": "passport", "path": "storage/docs/passport.pdf" }
        ]
      }
      ```
    - Примечание:
      - `documents` опционален
      - каждый документ: объект `{ "type": "...", "path": "..." }`
      - документы сохраняются в таблицу `documents` и привязываются к `request_lives` через `request_id`
  - POST `/api/v1/requests/change-room`
    - Body:
      ```json
      {
        "room_id": 2,
        "description": "Причина/описание"
      }
      ```
- Manager/Admin (role:manager,admin):
  - GET `/api/v1/requests/live`
  - POST `/api/v1/requests/live/{requestLive}/approve`
    - Body: отсутствует.
  - POST `/api/v1/requests/live/{requestLive}/reject`
    - Body: отсутствует.
  - GET `/api/v1/requests/change-room`
  - POST `/api/v1/requests/change-room/{requestChangeRoom}/approve`
    - Body: отсутствует.
  - POST `/api/v1/requests/change-room/{requestChangeRoom}/reject`
    - Body: отсутствует.

Approve rules (фактически проверяются через SettlementService при создании/переселении settlement):
- `users.gender` должен быть `male|female`
- `rooms.is_active=true`
- `floors.is_active=true`
- `floors.gender_policy` совпадает с `users.gender` или `mixed`
- вместимость комнаты не превышена (count активных settlements)
- у пользователя не должно быть активного settlement (для approve проживания)

## Settlement
Файл: `dms/Modules/Settlement/routes/api.php`

**v1 (`/api/v1/...`)** (auth:sanctum)
- GET `/api/v1/settlements`
- POST `/api/v1/settlements`
  - Body:
    ```json
    {
      "user_id": 10,
      "room_id": 5,
      "source": "admin_manual"
    }
    ```
- GET `/api/v1/settlements/{settlement}`
- PUT/PATCH `/api/v1/settlements/{settlement}`
  - Body (один из вариантов):
    ```json
    { "end_reason": "personal" }
    ```
  - или:
    ```json
    { "new_room_id": 7 }
    ```
- DELETE `/api/v1/settlements/{settlement}`

Примечание: `DELETE` не должен использоваться (история хранится в `settlements`), контроллер возвращает `405` через `result()`.

## Finance
Файл: `dms/Modules/Finance/routes/api.php`

**v1 (`/api/v1/...`)**
- POST `/api/v1/finance/checkout/{charge}` (auth:sanctum)
  - Назначение:
    - взять начисление (`charges`) текущего пользователя со статусом `pending`
    - создать Stripe Checkout Session
    - создать `payments` запись со статусом `pending`
    - вернуть URL checkout-сессии
  - Body: отсутствует.
  - Path params:
    - `charge` — ID начисления
  - Response data (пример):
    ```json
    {
      "url": "https://checkout.stripe.com/c/pay/cs_test_..."
    }
    ```

- POST `/api/v1/finance/webhook` (без auth)
  - Назначение:
    - принять webhook от Stripe
    - на событии `checkout.session.completed` обновить платеж и начисление
  - Headers:
    - `Stripe-Signature: <signature>`
  - Body:
    - raw JSON payload от Stripe
  - Response:
    ```json
    {
      "status": "ok"
    }
    ```

Примечания по текущей реализации Finance:
- Для webhook используется секрет `config('services.stripe.webhook_secret')`.
- Для checkout используется `config('services.stripe.secret')`.
- Валюта в Stripe-сессии зашита как `kzt`.
