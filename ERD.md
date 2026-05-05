// DMS / UniNest ERD
// Updated against current migrations and models on 2026-04-25.
// Syntax is compatible with dbdiagram.io-style Table/Ref blocks.
//
// Domain rule:
// - settlements is the source of truth for active dormitory residence.
// - Active residence = settlements.end_at IS NULL.
// - rooms.capacity is the physical capacity source of truth.
// - room_types stores pricing/classification only; capacity was removed.

// =======================
// USERS, AUTH & STUDENTS
// =======================

Table users {
  id bigint [pk]
  name varchar
  lastname varchar [null]
  middlename varchar [null]
  uni_id varchar [null]
  email varchar [unique]
  email_verified_at timestamp [null]
  password varchar
  phone_number varchar [null]
  role varchar [default: 'student', note: 'student / manager / dorm-admin / employee / admin']
  gender varchar [null, note: 'male / female']
  discipline_limit int [default: 10, note: 'Maximum active penalty points before discipline action']
  remember_token varchar [null]
  created_at timestamp
  updated_at timestamp
}

Table dorm_students {
  user_id bigint [pk]
  warning_count int [default: 0]
  created_at timestamp
  updated_at timestamp
}

Table personal_access_tokens {
  id bigint [pk]
  tokenable_type varchar
  tokenable_id bigint
  name text
  token varchar [unique]
  abilities text [null]
  last_used_at timestamp [null]
  expires_at timestamp [null]
  created_at timestamp
  updated_at timestamp
}

Table password_reset_tokens {
  email varchar [pk]
  token varchar
  created_at timestamp [null]
}

Table sessions {
  id varchar [pk]
  user_id bigint [null]
  ip_address varchar [null]
  user_agent text [null]
  payload longtext
  last_activity int
}


// =======================
// DORM STRUCTURE
// =======================

Table buildings {
  id bigint [pk]
  name varchar [null]
  address varchar
  latitude decimal [null]
  longitude decimal [null]
  total_floors int
  created_at timestamp
  updated_at timestamp
}

Table floors {
  id bigint [pk]
  building_id bigint
  floor_number int
  gender_policy varchar [default: 'mixed', note: 'male / female / mixed']
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table room_types {
  id bigint [pk]
  name varchar
  semester_price decimal
  created_at timestamp
  updated_at timestamp
}

Table rooms {
  id bigint [pk]
  floor_id bigint
  room_type_id bigint [null]
  room_number int
  capacity int [note: 'Physical room capacity']
  live_cap int [note: 'Current configured living limit; must be <= capacity']
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}


// =======================
// SETTLEMENTS (CORE)
// =======================

Table settlements {
  id bigint [pk]
  user_id bigint
  room_id bigint
  start_at date
  end_at date [null, note: 'NULL means active residence']
  status varchar [note: 'active / finished / cancelled']
  source varchar [note: 'request_live / admin_manual / relocation']
  end_reason varchar [null, note: 'graduation / eviction / relocation / personal / discipline']
  created_at timestamp
  updated_at timestamp

  indexes {
    (room_id, end_at)
    (user_id, end_at)
  }
}


// =======================
// REQUESTS & DOCUMENTS
// =======================

Table request_lives {
  id bigint [pk]
  user_id bigint
  preferred_room_id bigint [null]
  status varchar [default: 'pending', note: 'pending / approved / rejected']
  created_at timestamp
  updated_at timestamp
}

Table documents {
  id bigint [pk]
  request_id bigint
  type varchar
  path varchar
  created_at timestamp
  updated_at timestamp
}

Table request_change_rooms {
  id bigint [pk]
  student_id bigint [note: 'FK to dorm_students.user_id']
  room_id bigint [null, note: 'Requested target room']
  status varchar [default: 'pending', note: 'pending / approved / rejected']
  description text [null]
  created_at timestamp
  updated_at timestamp
}

Table repair_requests {
  id bigint [pk]
  user_id bigint [note: 'Student who created the request']
  room_id bigint
  handled_by_id bigint [null, note: 'Employee/admin handling request']
  category varchar
  title varchar
  description text
  status varchar [default: 'pending', note: 'pending / in_progress / resolved']
  employee_comment text [null]
  started_at timestamp [null]
  resolved_at timestamp [null]
  created_at timestamp
  updated_at timestamp

  indexes {
    (status, created_at)
    (user_id, created_at)
  }
}

Table repair_request_attachments {
  id bigint [pk]
  repair_request_id bigint
  file_path varchar
  created_at timestamp
  updated_at timestamp
}


// =======================
// FINANCE & PAYMENTS
// =======================

Table charges {
  id bigint [pk]
  user_id bigint
  settlement_id bigint [null, note: 'Nullable for non-settlement charges such as gym memberships']
  gym_plan_id bigint [null]
  amount decimal
  currency varchar [default: 'KZT']
  type varchar [default: 'semester_rent', note: 'semester_rent / penalty / gym_membership']
  period_start date
  period_end date
  status varchar [default: 'pending', note: 'pending / paid / cancelled']
  created_at timestamp
  updated_at timestamp

  indexes {
    (settlement_id, type) [unique, name: 'charges_settlement_type_unique']
  }
}

Table payments {
  id bigint [pk]
  charge_id bigint
  stripe_session_id varchar
  stripe_payment_intent_id varchar [null]
  amount decimal
  status varchar [default: 'pending', note: 'pending / succeeded / failed']
  paid_at timestamp [null]
  raw_payload json [null]
  created_at timestamp
  updated_at timestamp
}


// =======================
// PENALTY (DISCIPLINE)
// =======================

Table penalty_rules {
  id bigint [pk]
  code varchar
  title varchar
  default_points int
  redeemable boolean [default: true]
  creates_financial_charge boolean [default: false]
  financial_amount decimal [null]
  created_at timestamp
  updated_at timestamp
}

Table penalties {
  id bigint [pk]
  user_id bigint
  settlement_id bigint
  rule_id bigint
  created_by bigint
  points int [note: 'Snapshot from penalty rule or request']
  description text [null]
  status varchar [default: 'active', note: 'active / resolved / cancelled']
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, status)
  }
}

Table penalty_evidences {
  id bigint [pk]
  penalty_id bigint
  file_path varchar
  created_at timestamp
  updated_at timestamp
}

Table penalty_redemptions {
  id bigint [pk]
  penalty_id bigint
  user_id bigint
  event_type varchar
  description text
  file_path varchar [null]
  status varchar [default: 'pending', note: 'pending / approved / rejected']
  reviewed_by bigint [null]
  reviewed_at timestamp [null]
  created_at timestamp
  updated_at timestamp

  indexes {
    (penalty_id, status)
  }
}


// =======================
// GYM
// =======================

Table gym_plans {
  id bigint [pk]
  name varchar
  total_sessions int
  price decimal
  duration_days int
  is_active boolean [default: true]
  created_at timestamp
  updated_at timestamp
}

Table gym_memberships {
  id bigint [pk]
  user_id bigint
  plan_id bigint
  charge_id bigint [unique]
  total_sessions int
  remaining_sessions int
  started_at date
  expires_at date
  status varchar [default: 'active', note: 'active / exhausted / expired / cancelled']
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, status)
  }
}

Table gym_visits {
  id bigint [pk]
  membership_id bigint
  user_id bigint
  visit_date date
  check_in_at timestamp
  check_out_at timestamp [null]
  duration_minutes int [null]
  sessions_used int [default: 1]
  status varchar [default: 'completed', note: 'active / completed / cancelled / auto_closed']
  created_at timestamp
  updated_at timestamp

  indexes {
    (user_id, status)
    (membership_id, visit_date)
  }
}


// =======================
// NEWS & NOTIFICATIONS
// =======================

Table news {
  id bigint [pk]
  title varchar
  description text
  photo varchar [null]
  created_at timestamp
  updated_at timestamp
}

Table notifications {
  id uuid [pk]
  type varchar
  notifiable_type varchar
  notifiable_id bigint
  data text
  read_at timestamp [null]
  created_at timestamp
  updated_at timestamp

  indexes {
    (notifiable_type, notifiable_id)
  }
}

Table system_notifications {
  id bigint [pk]
  title varchar
  message text
  action_url varchar [null]
  created_by bigint [null]
  created_at timestamp
  updated_at timestamp
}


// =======================
// BUY / SELL MARKETPLACE
// =======================

Table buy_sell_listings {
  id bigint [pk]
  user_id bigint
  title varchar
  category varchar
  condition varchar
  price decimal
  pickup_location varchar [null]
  contact_phone varchar [null]
  status varchar [default: 'active', note: 'active / sold']
  description text
  image_paths json
  published_at timestamp [null]
  sold_at timestamp [null]
  created_at timestamp
  updated_at timestamp

  indexes {
    (status, category)
    (user_id, status)
  }
}


// =======================
// LARAVEL SYSTEM TABLES
// =======================

Table cache {
  key varchar [pk]
  value mediumtext
  expiration int
}

Table cache_locks {
  key varchar [pk]
  owner varchar
  expiration int
}

Table jobs {
  id bigint [pk]
  queue varchar
  payload longtext
  attempts tinyint
  reserved_at int [null]
  available_at int
  created_at int
}

Table job_batches {
  id varchar [pk]
  name varchar
  total_jobs int
  pending_jobs int
  failed_jobs int
  failed_job_ids longtext
  options mediumtext [null]
  cancelled_at int [null]
  created_at int
  finished_at int [null]
}

Table failed_jobs {
  id bigint [pk]
  uuid varchar [unique]
  connection text
  queue text
  payload longtext
  exception longtext
  failed_at timestamp
}


// =======================
// RELATIONS
// =======================

Ref: dorm_students.user_id > users.id [delete: cascade, update: cascade]

Ref: sessions.user_id > users.id
Ref: personal_access_tokens.tokenable_id > users.id // polymorphic tokenable_type is usually Modules\User\Models\User

Ref: floors.building_id > buildings.id [delete: cascade]
Ref: rooms.floor_id > floors.id [delete: cascade]
Ref: rooms.room_type_id > room_types.id [delete: set null]

Ref: settlements.user_id > users.id [delete: cascade, update: cascade]
Ref: settlements.room_id > rooms.id [delete: restrict, update: cascade]

Ref: request_lives.user_id > users.id [delete: cascade, update: cascade]
Ref: request_lives.preferred_room_id > rooms.id [delete: set null]
Ref: documents.request_id > request_lives.id [delete: cascade, update: cascade]

Ref: request_change_rooms.student_id > dorm_students.user_id [delete: cascade, update: cascade]
Ref: request_change_rooms.room_id > rooms.id [delete: set null]

Ref: repair_requests.user_id > users.id [delete: cascade]
Ref: repair_requests.room_id > rooms.id [delete: cascade]
Ref: repair_requests.handled_by_id > users.id [delete: set null]
Ref: repair_request_attachments.repair_request_id > repair_requests.id [delete: cascade]

Ref: charges.user_id > users.id [delete: cascade]
Ref: charges.settlement_id > settlements.id [delete: set null]
Ref: charges.gym_plan_id > gym_plans.id [delete: set null]
Ref: payments.charge_id > charges.id [delete: cascade]

Ref: penalties.user_id > users.id [delete: cascade]
Ref: penalties.settlement_id > settlements.id [delete: cascade]
Ref: penalties.rule_id > penalty_rules.id [delete: restrict]
Ref: penalties.created_by > users.id [delete: restrict]
Ref: penalty_evidences.penalty_id > penalties.id [delete: cascade]
Ref: penalty_redemptions.penalty_id > penalties.id [delete: cascade]
Ref: penalty_redemptions.user_id > users.id [delete: cascade]
Ref: penalty_redemptions.reviewed_by > users.id [delete: set null]

Ref: gym_memberships.user_id > users.id [delete: cascade]
Ref: gym_memberships.plan_id > gym_plans.id [delete: cascade]
Ref: gym_memberships.charge_id > charges.id [delete: cascade]
Ref: gym_visits.membership_id > gym_memberships.id [delete: cascade]
Ref: gym_visits.user_id > users.id [delete: cascade]

Ref: notifications.notifiable_id > users.id // polymorphic notifiable_type is usually Modules\User\Models\User
Ref: system_notifications.created_by > users.id [delete: set null]

Ref: buy_sell_listings.user_id > users.id [delete: cascade]
