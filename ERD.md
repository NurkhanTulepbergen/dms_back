# ERD (по миграциям проекта)

Ниже 2 диаграммы:
1) **Domain**: основные сущности общежитий и заявок.
2) **Infra**: технические таблицы Laravel (auth/sanctum/sessions/jobs/cache).

> Примечание: ERD собран по миграциям. Источник истины проживания: `settlements` (активное проживание = `end_at IS NULL`).

## Domain ERD

```mermaid
erDiagram
  USERS {
    bigint id PK
    varchar name
    varchar email "UNIQUE"
    timestamp email_verified_at "NULL"
    varchar password
    varchar remember_token "NULL"
    varchar role "default: student"
    varchar lastname "NULL"
    varchar middlename "NULL"
    varchar phone_number "NULL"
    varchar uni_id "NULL"
    varchar gender "NULL (male|female)"
    timestamp created_at
    timestamp updated_at
  }

  DORM_STUDENTS {
    bigint user_id PK, FK
    int warning_count "default: 0"
    timestamp created_at
    timestamp updated_at
  }

  BUILDINGS {
    bigint id PK
    varchar address
    int total_floors
    timestamp created_at
    timestamp updated_at
  }

  FLOORS {
    bigint id PK
    bigint building_id FK
    int floor_number
    varchar gender_policy "default: mixed (male|female|mixed)"
    boolean is_active "default: true"
    timestamp created_at
    timestamp updated_at
  }

  ROOMS {
    bigint id PK
    bigint floor_id FK
    int room_number
    int capacity
    int live_cap
    boolean is_active "default: true"
    timestamp created_at
    timestamp updated_at
  }

  REQUEST_LIVES {
    bigint id PK
    bigint user_id FK
    bigint preferred_room_id FK "NULL"
    varchar status "default: pending"
    timestamp created_at
    timestamp updated_at
  }

  DOCUMENTS {
    bigint id PK
    bigint request_id FK
    varchar type
    varchar path
    timestamp created_at
    timestamp updated_at
  }

  REQUEST_CHANGE_ROOMS {
    bigint id PK
    bigint student_id FK
    bigint room_id FK "NULL"
    varchar status "default: pending"
    text description "NULL"
    timestamp created_at
    timestamp updated_at
  }

  NEWS {
    bigint id PK
    varchar title
    text description
    varchar photo "NULL"
    timestamp created_at
    timestamp updated_at
  }

  SETTLEMENTS {
    bigint id PK
    bigint user_id FK
    bigint room_id FK
    date start_at
    date end_at "NULL"
    varchar status "active|finished|cancelled"
    varchar source "request_live|admin_manual|relocation"
    varchar end_reason "NULL (graduation|eviction|relocation|personal)"
    timestamp created_at
    timestamp updated_at
  }

  BUILDINGS ||--o{ FLOORS : has_many
  FLOORS ||--o{ ROOMS : has_many

  USERS ||--o| DORM_STUDENTS : has_one

  USERS ||--o{ REQUEST_LIVES : submits
  ROOMS ||--o{ REQUEST_LIVES : preferred_room
  REQUEST_LIVES ||--o{ DOCUMENTS : has_many

  DORM_STUDENTS ||--o{ REQUEST_CHANGE_ROOMS : submits
  ROOMS ||--o{ REQUEST_CHANGE_ROOMS : requested_for

  USERS ||--o{ SETTLEMENTS : settles
  ROOMS ||--o{ SETTLEMENTS : occupied_by
```

## Infra ERD

```mermaid
erDiagram
  PERSONAL_ACCESS_TOKENS {
    bigint id PK
    varchar tokenable_type
    bigint tokenable_id
    text name
    varchar token "UNIQUE(64)"
    text abilities "NULL"
    timestamp last_used_at "NULL"
    timestamp expires_at "NULL, INDEX"
    timestamp created_at
    timestamp updated_at
  }

  SESSIONS {
    varchar id PK
    bigint user_id "NULL, INDEX (FK не задан миграцией)"
    varchar ip_address "NULL"
    text user_agent "NULL"
    longtext payload
    int last_activity "INDEX"
  }

  PASSWORD_RESET_TOKENS {
    varchar email PK
    varchar token
    timestamp created_at "NULL"
  }

  JOBS {
    bigint id PK
    varchar queue "INDEX"
    longtext payload
    tinyint attempts
    int reserved_at "NULL"
    int available_at
    int created_at
  }

  JOB_BATCHES {
    varchar id PK
    varchar name
    int total_jobs
    int pending_jobs
    int failed_jobs
    longtext failed_job_ids
    mediumtext options "NULL"
    int cancelled_at "NULL"
    int created_at
    int finished_at "NULL"
  }

  FAILED_JOBS {
    bigint id PK
    varchar uuid "UNIQUE"
    text connection
    text queue
    longtext payload
    longtext exception
    timestamp failed_at "default: current"
  }

  CACHE {
    varchar key PK
    mediumtext value
    int expiration
  }

  CACHE_LOCKS {
    varchar key PK
    varchar owner
    int expiration
  }

  USERS ||--o{ PERSONAL_ACCESS_TOKENS : tokenable
  USERS ||--o{ SESSIONS : has_many
```
