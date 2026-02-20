# DMS API Endpoints

Документ синхронизирован с текущим кодом проекта (`php artisan route:list --path=api`, 55 маршрутов).

## Базовая информация
- Общий префикс модульных роутов: `/api`
- Основная версия API: `/api/v1/*`
- Исключение: Auth дополнительно имеет дубли без версии (`/api/login`, `/api/register`, ...)

Формат ответа (большинство endpoint’ов):
```json
{
  "status_code": 200,
  "message": "...",
  "data": {}
}
```

Аутентификация:
- Laravel Sanctum
- Заголовок: `Authorization: Bearer <token>`

Роли:
- Middleware `role` проверяет поле `users.role`
- Примеры: `role:student`, `role:admin,manager`

## Доменные правила
- Источник истины по проживанию: `settlements`
- Активное проживание: `end_at IS NULL`
- Для заселения/переселения проверяются:
  - пол пользователя (`users.gender` = `male|female`)
  - активность комнаты (`rooms.is_active=true`)
  - активность этажа (`floors.is_active=true`)
  - политика этажа (`floors.gender_policy` = `mixed|male|female`)
  - вместимость (`rooms.capacity` по числу активных `settlements`)
- При создании settlement автоматически создается начисление (`charges`, тип `semester_rent`)
- Для дисциплины:
  - активные штрафные баллы считаются по `penalties.status = active`
  - при `active_points >= users.discipline_limit` активное заселение закрывается с `end_reason = discipline`

## Auth
Файл: `dms/Modules/Auth/routes/api.php`

### v1
- `POST /api/v1/register`
- `POST /api/v1/login`
- `POST /api/v1/logout` (`auth:sanctum`)
- `POST /api/v1/reset-password` (`auth:sanctum`)

### Без версии
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout` (`auth:sanctum`)
- `POST /api/reset-password` (`auth:sanctum`)

### Body examples
`POST /register`:
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

`POST /login`:
```json
{
  "email": "user@example.com",
  "password": "password12345"
}
```

`POST /reset-password`:
```json
{
  "old_password": "old",
  "new_password": "newpassword12345",
  "confirm_password": "newpassword12345"
}
```

Примечание: при неверных credentials login возвращает `401` с `{"message":"Invalid Credentials"}` (не через `result()`).

## User
Файл: `dms/Modules/User/routes/api.php`

Все endpoint’ы под `auth:sanctum`:
- `GET /api/v1/me`
- `GET /api/v1/users`
- `GET /api/v1/users/{user}`

## News
Файл: `dms/Modules/News/routes/api.php`

Под `auth:sanctum`:
- `GET /api/v1/news`
- `GET /api/v1/news/{news}`

Под `auth:sanctum + role:admin,manager`:
- `POST /api/v1/news`
- `PUT/PATCH /api/v1/news/{news}`
- `DELETE /api/v1/news/{news}`

Body для create/update:
```json
{
  "title": "Заголовок",
  "description": "Текст",
  "photo": "https://..."
}
```

## Dormitory
Файл: `dms/Modules/Dormitory/routes/api.php`

Все endpoint’ы под `auth:sanctum`.

### Buildings
- `GET /api/v1/buildings`
- `GET /api/v1/buildings/{building}`
- `POST /api/v1/buildings` (`role:admin,manager`)
- `PUT/PATCH /api/v1/buildings/{building}` (`role:admin,manager`)
- `DELETE /api/v1/buildings/{building}` (`role:admin,manager`)

Body create/update:
```json
{
  "address": "Адрес",
  "total_floors": 9
}
```

### Floors
- `GET /api/v1/floors`
- `GET /api/v1/floors/{floor}`
- `POST /api/v1/floors` (`role:admin,manager`)
- `PUT/PATCH /api/v1/floors/{floor}` (`role:admin,manager`)
- `DELETE /api/v1/floors/{floor}` (`role:admin,manager`)

Body create:
```json
{
  "building_id": 1,
  "floor_number": 1
}
```

Body update:
```json
{
  "floor_number": 2
}
```

Примечание: поля `gender_policy` и `is_active` есть в БД, но текущий CRUD Floors их не принимает.

### Rooms
- `GET /api/v1/rooms`
- `GET /api/v1/rooms/{room}`
- `POST /api/v1/rooms` (`role:admin,manager`)
- `PUT/PATCH /api/v1/rooms/{room}` (`role:admin,manager`)
- `DELETE /api/v1/rooms/{room}` (`role:admin,manager`)

Body create/update:
```json
{
  "floor_id": 1,
  "room_type_id": 1,
  "room_number": "101",
  "capacity": 3,
  "live_cap": 0
}
```

Примечание: `room_type_id` обязателен.

### Иерархия
- `GET /api/v1/buildings/{building}/floors`
- `GET /api/v1/floors/{floor}/rooms`

## Requests
Файл: `dms/Modules/Requests/routes/api.php`

Все endpoint’ы под `auth:sanctum`.

### Student (`role:student`)
- `POST /api/v1/requests/live`
- `POST /api/v1/requests/change-room`

Body `POST /requests/live`:
```json
{
  "preferred_room_id": 1,
  "documents": [
    { "type": "passport", "path": "storage/docs/passport.pdf" }
  ]
}
```

Body `POST /requests/change-room`:
```json
{
  "room_id": 2,
  "description": "Причина"
}
```

### Manager/Admin (`role:manager,admin`)
- `GET /api/v1/requests/live`
- `POST /api/v1/requests/live/{requestLive}/approve`
- `POST /api/v1/requests/live/{requestLive}/reject`
- `GET /api/v1/requests/change-room`
- `POST /api/v1/requests/change-room/{requestChangeRoom}/approve`
- `POST /api/v1/requests/change-room/{requestChangeRoom}/reject`

## Settlement
Файл: `dms/Modules/Settlement/routes/api.php`

Под `auth:sanctum`:
- `GET /api/v1/settlements`
- `POST /api/v1/settlements`
- `GET /api/v1/settlements/{settlement}`
- `PUT/PATCH /api/v1/settlements/{settlement}`
- `DELETE /api/v1/settlements/{settlement}`

Body `POST /settlements`:
```json
{
  "user_id": 10,
  "room_id": 5,
  "source": "admin_manual"
}
```

Body `PUT/PATCH /settlements/{id}`:
- закрыть проживание:
```json
{ "end_reason": "personal" }
```
- переселить:
```json
{ "new_room_id": 7 }
```

Примечание: `DELETE` возвращает `405` (история хранится в `settlements`, физическое удаление отключено).

## Finance
Файл: `dms/Modules/Finance/routes/api.php`

- `POST /api/v1/finance/checkout/{charge}` (`auth:sanctum`)
- `POST /api/v1/finance/webhook` (без auth)

`POST /checkout/{charge}`:
- берет начисление текущего пользователя (`charges.status = pending`)
- создает Stripe Checkout Session
- создает запись `payments` со статусом `pending`
- возвращает URL оплаты

Пример data:
```json
{
  "url": "https://checkout.stripe.com/c/pay/cs_test_..."
}
```

`POST /webhook`:
- ожидает заголовок `Stripe-Signature`
- обрабатывает событие `checkout.session.completed`
- обновляет `payments.status = succeeded`, `charges.status = paid`

## Penalty
Файл: `dms/Modules/Penalty/routes/api.php`

Все endpoint’ы под `auth:sanctum`.

### Student (`role:student`)
- `GET /api/v1/penalties`
- `GET /api/v1/penalties/{id}`
- `POST /api/v1/penalties/{id}/redeem`

Body `POST /penalties/{id}/redeem`:
```json
{
  "event_type": "community_work",
  "description": "Отработка нарушения",
  "file_path": "storage/redemptions/proof.pdf"
}
```

### Manager/Admin (`role:manager,admin`)
- `POST /api/v1/penalties`
- `POST /api/v1/penalties/{id}/cancel`
- `POST /api/v1/penalties/redemptions/{id}/approve`
- `POST /api/v1/penalties/redemptions/{id}/reject`

Body `POST /penalties`:
```json
{
  "user_id": 10,
  "rule_id": 2,
  "points": 3,
  "description": "Нарушение правил проживания",
  "evidences": [
    "storage/penalties/evidence-1.jpg",
    "storage/penalties/evidence-2.jpg"
  ]
}
```

Body `POST /penalties/{id}/cancel`:
```json
{
  "description": "Отмена по решению администрации"
}
```

Примечания:
- Штраф привязывается к активному `settlement` студента.
- Если у правила `creates_financial_charge=true`, вызывается `ChargeService::createPenaltyCharge(...)`.
- Approve redemption переводит `penalties.status` в `resolved`.
