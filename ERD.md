// =======================
// USERS & STUDENTS
// =======================

Table users {
id bigint [pk]
name varchar
lastname varchar [null]
middlename varchar [null]
email varchar [unique]
password varchar
role varchar [default: 'student']
phone_number varchar [null]
uni_id varchar [null]
gender varchar [note: 'male / female', null]
discipline_limit int [default: 5]   // максимум допустимых баллов
created_at timestamp
updated_at timestamp
}

Table dorm_students {
user_id bigint [pk]
warning_count int [default: 0]
created_at timestamp
updated_at timestamp
}


// =======================
// DORM STRUCTURE
// =======================

Table buildings {
id bigint [pk]
address varchar
total_floors int
created_at timestamp
updated_at timestamp
}

Table floors {
id bigint [pk]
building_id bigint
floor_number int
gender_policy varchar [note: 'male / female / mixed']
is_active boolean
created_at timestamp
updated_at timestamp
}

Table room_types {
id bigint [pk]
name varchar                   // 2-bed standard
semester_price decimal
created_at timestamp
updated_at timestamp
}

Table rooms {
id bigint [pk]
floor_id bigint
room_type_id bigint
room_number int
capacity int                   // физическая вместимость комнаты (source of truth)
live_cap int                   // текущий лимит заселения (<= capacity)
is_active boolean
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
end_at date [null]
status varchar [note: 'active / finished / cancelled']
source varchar                 // request_live / admin_manual / relocation
end_reason varchar [null]      // graduation / eviction / relocation / personal / discipline
created_at timestamp
updated_at timestamp
}


// =======================
// REQUESTS
// =======================

Table request_lives {
id bigint [pk]
user_id bigint
preferred_room_id bigint [null]
status varchar [default: 'pending']
created_at timestamp
updated_at timestamp
}

Table request_change_rooms {
id bigint [pk]
student_id bigint
room_id bigint [null]
status varchar [default: 'pending']
description text [null]
created_at timestamp
updated_at timestamp
}


// =======================
// DOCUMENTS
// =======================

Table documents {
id bigint [pk]
request_id bigint
type varchar
path varchar
created_at timestamp
}


// =======================
// FINANCE
// =======================

Table charges {
id bigint [pk]
user_id bigint
settlement_id bigint
amount decimal
currency varchar [default: 'KZT']
type varchar                   // semester_rent / penalty
period_start date
period_end date
status varchar [default: 'pending']  // pending / paid / cancelled
created_at timestamp
updated_at timestamp
}

Table payments {
id bigint [pk]
charge_id bigint
stripe_session_id varchar
stripe_payment_intent_id varchar [null]
amount decimal
status varchar [default: 'pending']  // pending / succeeded / failed
paid_at timestamp [null]
raw_payload text [null]
created_at timestamp
updated_at timestamp
}


// =======================
// PENALTY (DISCIPLINE)
// =======================

Table penalty_rules {
id bigint [pk]
code varchar                   // trash / noise / damage / etc
title varchar
default_points int             // +1 / +2 ...
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
created_by bigint              // manager/admin
points int                     // snapshot from rule
description text [null]
status varchar [default: 'active'] // active / resolved / cancelled
created_at timestamp
updated_at timestamp
}

Table penalty_evidences {
id bigint [pk]
penalty_id bigint
file_path varchar
created_at timestamp
}

Table penalty_redemptions {
id bigint [pk]
penalty_id bigint
user_id bigint
event_type varchar             // субботник / уборка / помощь / etc
description text
file_path varchar [null]
status varchar [default: 'pending'] // pending / approved / rejected
reviewed_by bigint [null]
reviewed_at timestamp [null]
created_at timestamp
updated_at timestamp
}


// =======================
// NEWS
// =======================

Table news {
id bigint [pk]
title varchar
description text
photo varchar [null]
created_at timestamp
updated_at timestamp
}


// =======================
// RELATIONS
// =======================

Ref: dorm_students.user_id > users.id

Ref: floors.building_id > buildings.id
Ref: rooms.floor_id > floors.id
Ref: rooms.room_type_id > room_types.id

Ref: settlements.user_id > users.id
Ref: settlements.room_id > rooms.id

Ref: request_lives.user_id > users.id
Ref: request_lives.preferred_room_id > rooms.id

Ref: request_change_rooms.student_id > dorm_students.user_id
Ref: request_change_rooms.room_id > rooms.id

Ref: documents.request_id > request_lives.id

Ref: charges.user_id > users.id
Ref: charges.settlement_id > settlements.id

Ref: payments.charge_id > charges.id

Ref: penalties.user_id > users.id
Ref: penalties.settlement_id > settlements.id
Ref: penalties.rule_id > penalty_rules.id
Ref: penalties.created_by > users.id

Ref: penalty_evidences.penalty_id > penalties.id

Ref: penalty_redemptions.penalty_id > penalties.id
Ref: penalty_redemptions.user_id > users.id
Ref: penalty_redemptions.reviewed_by > users.id
