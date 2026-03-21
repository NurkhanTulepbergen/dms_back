# DMS API Endpoints

Документ синхронизирован с текущим кодом проекта на **10 марта 2026**.
Проверка: `php artisan route:list --path=api` => **60** маршрутов.

## Базовая информация
- Общий префикс модульных роутов: `/api`
- Основная версия API: `/api/v1/*`
- Исключение: Auth имеет дубли без версии (`/api/login`, `/api/register`, ...)

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
- Middleware `role` проверяет `users.role`
- Используется как `role:student` или `role:admin,manager`

## Доменные правила
- Источник истины по проживанию: `settlements`
- Активное проживание: `end_at IS NULL`
- Для заселения/переселения проверяются:
  - пол пользователя (`users.gender = male|female`)
  - активность комнаты (`rooms.is_active=true`)
  - активность этажа (`floors.is_active=true`)
  - политика этажа (`floors.gender_policy = mixed|male|female`)
  - вместимость (`rooms.capacity` по числу активных `settlements`)
- При создании settlement автоматически создается начисление `semester_rent` в `charges`
- Для дисциплины:
  - активные баллы считаются по `penalties.status = active`
  - при `active_points >= users.discipline_limit` активное заселение закрывается (`end_reason = discipline`)

Важно по типам комнат:
- `room_types` хранит только `name` и `semester_price`
- `room_types.capacity` удален миграцией `2026_02_24_120000_drop_capacity_from_room_types_table`
- Вместимость берется только из `rooms.capacity`

Важно по Gym:
- Абонемент создается после успешной оплаты `charge.type = gym_membership`
- Тренировка списывается через `POST /api/v1/gym/use-session`

## Auth
Файл: `Modules/Auth/routes/api.php`

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
`POST /api/v1/register`:
```json
{
  "role": "student",
  "email": "user@example.com",
  "password": "password12345",
  "phone_number": "+77770000000",
  "lastname": "Ivanov",
  "name": "Ivan",
  "middlename": "Ivanovich",
  "uni_id": "U12345",
  "gender": "male"
}
```

`POST /api/v1/login`:
```json
{
  "email": "user@example.com",
  "password": "password12345"
}
```

`POST /api/v1/reset-password`:
```json
{
  "old_password": "old_password",
  "new_password": "new_password123",
  "confirm_password": "new_password123"
}
```

Примечание: при неверных credentials login возвращает `401` с `{"message":"Invalid Credentials"}`.

## User
Файл: `Modules/User/routes/api.php`

Под `auth:sanctum`:
- `GET /api/v1/me`
- `GET /api/v1/users`
- `GET /api/v1/users/{user}`

## News
Файл: `Modules/News/routes/api.php`

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
Файл: `Modules/Dormitory/routes/api.php`

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
  "address": "Ислама Каримова, 70 к1",
  "total_floors": 7
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

Примечания:
- API Floors принимает только `building_id`, `floor_number`
- `gender_policy` и `is_active` есть в БД, но в API `store/update` Floors сейчас не валидируются

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

Примечания:
- `room_type_id` обязателен
- фактическая вместимость комнаты определяется только `rooms.capacity`

### Иерархия
- `GET /api/v1/buildings/{building}/floors`
- `GET /api/v1/floors/{floor}/rooms`

## Requests
Файл: `Modules/Requests/routes/api.php`

Все endpoint’ы под `auth:sanctum`.

### Student (`role:student`)
- `GET /api/v1/requests/live/my`
- `POST /api/v1/requests/live`
- `GET /api/v1/requests/change-room/my`
- `POST /api/v1/requests/change-room`
- `GET /api/v1/repair-requests/my`
- `POST /api/v1/repair-requests`

Body `POST /api/v1/requests/live`:
```json
{
  "preferred_room_id": 1,
  "documents": [
    { "type": "passport", "path": "storage/docs/passport.pdf" }
  ]
}
```

Body `POST /api/v1/requests/change-room`:
```json
{
  "room_id": 2,
  "description": "Причина"
}
```

Body `POST /api/v1/repair-requests`:
```json
{
  "category": "plumbing",
  "title": "Сломался кран",
  "description": "Вода течет без остановки"
}
```

### Manager/Admin (`role:manager,admin`)
- `GET /api/v1/requests/live`
- `POST /api/v1/requests/live/{requestLive}/approve`
- `POST /api/v1/requests/live/{requestLive}/reject`
- `GET /api/v1/requests/change-room`
- `POST /api/v1/requests/change-room/{requestChangeRoom}/approve`
- `POST /api/v1/requests/change-room/{requestChangeRoom}/reject`

### Employee/Admin (`role:employee,admin`)
- `GET /api/v1/repair-requests`
- `POST /api/v1/repair-requests/{repairRequest}/start`
- `POST /api/v1/repair-requests/{repairRequest}/resolve`

## Settlement
Файл: `Modules/Settlement/routes/api.php`

Под `auth:sanctum`:
- `GET /api/v1/settlements/is-living/{userId}`
- `GET /api/v1/settlements`
- `POST /api/v1/settlements`
- `GET /api/v1/settlements/{settlement}`
- `PUT/PATCH /api/v1/settlements/{settlement}`
- `DELETE /api/v1/settlements/{settlement}`
- `GET /api/v1/showStatus/{userId}`

Body `POST /api/v1/settlements`:
```json
{
  "user_id": 10,
  "room_id": 5,
  "source": "admin_manual"
}
```

Body `PUT/PATCH /api/v1/settlements/{id}`:
- закрыть проживание:
```json
{ "end_reason": "personal" }
```
- переселить:
```json
{ "new_room_id": 7 }
```

Примечание: `DELETE` возвращает `405` (история хранится в `settlements`).

`GET /api/v1/settlements/is-living/{userId}`:
```json
{
  "status_code": 200,
  "message": "Student living status",
  "data": {
    "user_id": 10,
    "is_living": true
  }
}
```

## Finance
Файл: `Modules/Finance/routes/api.php`

- `POST /api/v1/finance/checkout/{charge}` (`auth:sanctum`)
- `POST /api/v1/finance/webhook` (без auth)

`POST /api/v1/finance/checkout/{charge}`:
- берет начисление текущего пользователя (`charges.status = pending`)
- создает Stripe Checkout Session
- создает запись `payments` со статусом `pending`
- возвращает URL оплаты

Пример ответа:
```json
{
  "status_code": 200,
  "message": "OK",
  "data": {
    "url": "https://checkout.stripe.com/c/pay/cs_test_..."
  }
}
```

`POST /api/v1/finance/webhook`:
- ожидает заголовок `Stripe-Signature`
- обрабатывает `checkout.session.completed`
- обновляет `payments.status = succeeded`, `charges.status = paid`
- если `charge.type = gym_membership`, активирует абонемент (`gym_memberships`)

## Gym
Файл: `Modules/Gym/routes/api.php`

Все endpoint’ы под `auth:sanctum`.

- `GET /api/v1/gym/plans`
- `GET /api/v1/gym/membership`
- `POST /api/v1/gym/checkout/{plan}`
- `POST /api/v1/gym/check-in`
- `POST /api/v1/gym/check-out`
- `GET /api/v1/gym/stats`

`GET /api/v1/gym/plans`:
- отдает список активных абонементов, доступных студенту для покупки
- источник данных: таблица `gym_plans`

`GET /api/v1/gym/membership`:
- если активного абонемента нет:
```json
{
  "status_code": 200,
  "message": "Gym membership",
  "data": {
    "has_membership": false,
    "membership": null,
    "available_plans": [
      {
        "id": 1,
        "name": "Пробный абонемент",
        "total_sessions": 4,
        "duration_days": 14,
        "price": 4000,
        "is_active": true
      }
    ]
  }
}
```
- если абонемент есть:
```json
{
  "status_code": 200,
  "message": "Gym membership",
  "data": {
    "has_membership": true,
    "membership": {
      "id": 5,
      "plan": {
        "id": 2,
        "name": "Месячный абонемент",
        "total_sessions": 12,
        "duration_days": 30,
        "price": 10000,
        "is_active": true
      },
      "total_sessions": 12,
      "remaining_sessions": 10,
      "started_at": "2026-03-01",
      "expires_at": "2026-03-31",
      "status": "active"
    },
    "available_plans": [
      {
        "id": 1,
        "name": "Пробный абонемент",
        "total_sessions": 4,
        "duration_days": 14,
        "price": 4000,
        "is_active": true
      }
    ]
  }
}
```

`POST /api/v1/gym/checkout/{plan}`:
- создает `charges` запись типа `gym_membership` со статусом `pending`
- создает Stripe checkout session и `payments` запись
- возвращает `checkout_url` для редиректа на Stripe Checkout и данные выбранного тарифа

`POST /api/v1/gym/check-in`:
- требует активный и не истекший абонемент
- уменьшает `remaining_sessions`
- если сессии закончились, переводит абонемент в `exhausted`

`POST /api/v1/gym/check-out`:
- завершает активное посещение и фиксирует длительность

## Penalty
Файл: `Modules/Penalty/routes/api.php`

Все endpoint’ы под `auth:sanctum`.

### Student (`role:student`)
- `GET /api/v1/penalties`
- `GET /api/v1/penalties/{id}`
- `POST /api/v1/penalties/{id}/redeem`

Body `POST /api/v1/penalties/{id}/redeem`:
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

Body `POST /api/v1/penalties`:
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

Body `POST /api/v1/penalties/{id}/cancel`:
```json
{
  "description": "Отмена по решению администрации"
}
```

Примечания:
- `approve/reject` redemption не требуют body
- штраф привязывается к активному `settlement` студента
- если у правила `creates_financial_charge=true`, создается финансовое начисление
