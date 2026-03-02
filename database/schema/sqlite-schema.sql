CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "herds"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "name" varchar not null,
  "code" varchar,
  "description" text,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "feeding_records"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "feeding_date" datetime not null,
  "feeding_time" varchar check("feeding_time" in('morning', 'evening')) not null,
  "animal_id" integer,
  "group_id" integer,
  "user_id" integer not null,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "cost" numeric,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("animal_id") references "animals"("id") on delete set null,
  foreign key("group_id") references "herds"("id") on delete set null,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "staff_profiles"(
  "id" integer primary key autoincrement not null,
  "user_id" integer,
  "first_name" varchar not null,
  "last_name" varchar,
  "phone" varchar,
  "email" varchar,
  "position" varchar,
  "farm_id" integer,
  "hired_at" date,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("user_id") references "users"("id") on delete set null,
  foreign key("farm_id") references "farms"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "roles"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null default 'web',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "roles_name_unique" on "roles"("name");
CREATE TABLE IF NOT EXISTS "role_user"(
  "id" integer primary key autoincrement not null,
  "role_id" integer not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("role_id") references "roles"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "role_user_role_id_user_id_unique" on "role_user"(
  "role_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "permissions"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "guard_name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "permissions_name_guard_name_unique" on "permissions"(
  "name",
  "guard_name"
);
CREATE TABLE IF NOT EXISTS "model_has_permissions"(
  "permission_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  primary key("permission_id", "model_id", "model_type")
);
CREATE INDEX "model_has_permissions_model_id_model_type_index" on "model_has_permissions"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "model_has_roles"(
  "role_id" integer not null,
  "model_type" varchar not null,
  "model_id" integer not null,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("role_id", "model_id", "model_type")
);
CREATE INDEX "model_has_roles_model_id_model_type_index" on "model_has_roles"(
  "model_id",
  "model_type"
);
CREATE TABLE IF NOT EXISTS "role_has_permissions"(
  "permission_id" integer not null,
  "role_id" integer not null,
  foreign key("permission_id") references "permissions"("id") on delete cascade,
  foreign key("role_id") references "roles"("id") on delete cascade,
  primary key("permission_id", "role_id")
);
CREATE TABLE IF NOT EXISTS "farms"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar,
  "address" text,
  "latitude" numeric,
  "longitude" numeric,
  "contact_name" varchar,
  "contact_phone" varchar,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "demo_data_seeded" tinyint(1) not null default('0'),
  "user_id" integer,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "farms_code_unique" on "farms"("code");
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  foreign key("farm_id") references "farms"("id") on delete cascade
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "breeds"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "code" varchar,
  "description" text,
  "characteristics" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  "origin" varchar check("origin" in('local', 'exotic', 'cross')) not null default 'local',
  "animal_type" varchar check("animal_type" in('cow', 'bull')) not null default 'cow',
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "breeds_name_farm_id_unique" on "breeds"(
  "name",
  "farm_id"
);
CREATE TABLE IF NOT EXISTS "calves"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "mother_id" integer not null,
  "father_id" integer,
  "tag_number" varchar not null,
  "gender" varchar not null,
  "birth_date" date not null,
  "birth_weight" numeric,
  "health_status" varchar not null default('healthy'),
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  foreign key("father_id") references animals("id") on delete set null on update no action,
  foreign key("mother_id") references animals("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "calving_records"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "pregnancy_id" integer not null,
  "calving_date" date not null,
  "calving_type" varchar not null,
  "calves_count" integer not null default('1'),
  "calf_gender" varchar,
  "calving_outcome" varchar not null,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  foreign key("pregnancy_id") references pregnancies("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "disease_treatment_medications"(
  "id" integer primary key autoincrement not null,
  "disease_treatment_id" integer not null,
  "medicine_id" integer not null,
  "farm_id" integer not null,
  "dose" varchar,
  "frequency" varchar,
  "duration_days" integer,
  "status" varchar not null default('planned'),
  "started_at" date,
  "ended_at" date,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "qty" integer,
  "user_id" integer,
  "unit_cost" numeric,
  "total_cost" numeric,
  foreign key("disease_treatment_id") references disease_treatments("id") on delete cascade on update no action,
  foreign key("medicine_id") references medicines("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "disease_treatments"(
  "id" integer primary key autoincrement not null,
  "health_issue_id" integer not null,
  "treatment_id" integer not null,
  "farm_id" integer not null,
  "description" text,
  "medication" varchar,
  "dosage" varchar,
  "frequency" varchar,
  "started_at" date,
  "ended_at" date,
  "administered_by" integer,
  "status" varchar not null default('ongoing'),
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "health_event_id" integer not null,
  "user_id" integer,
  foreign key("health_event_id") references health_events("id") on delete cascade on update no action,
  foreign key("health_issue_id") references health_issues("id") on delete cascade on update no action,
  foreign key("treatment_id") references treatments("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("administered_by") references staff_profiles("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "event_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "expenses"(
  "id" integer primary key autoincrement not null,
  "title" varchar not null,
  "amount" numeric not null,
  "incurred_on" date,
  "farm_id" integer,
  "staff_id" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  foreign key("staff_id") references staff_profiles("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "feed_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "unit" varchar not null default('kg'),
  "description" text,
  "nutrient_info" text,
  "created_at" datetime,
  "updated_at" datetime,
  "category" varchar not null default('other'),
  "unit_cost" numeric,
  "farm_id" integer,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "health_events"(
  "id" integer primary key autoincrement not null,
  "animal_id" integer not null,
  "event_type_id" integer not null,
  "title" varchar,
  "description" text,
  "occurred_at" date,
  "resolved_at" date,
  "cost" numeric,
  "treated_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "health_issue_id" integer,
  "user_id" integer,
  "vet_fee" numeric,
  "lab_cost" numeric,
  "other_cost" numeric,
  foreign key("health_issue_id") references health_issues("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("animal_id") references animals("id") on delete cascade on update no action,
  foreign key("event_type_id") references event_types("id") on delete cascade on update no action,
  foreign key("treated_by") references staff_profiles("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "inventory_categories"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "name" varchar not null,
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "inventory_items"(
  "id" integer primary key autoincrement not null,
  "sku" varchar,
  "name" varchar not null,
  "category" varchar,
  "quantity" numeric not null default('0'),
  "unit" varchar not null default('unit'),
  "min_quantity" numeric,
  "supplier_id" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "unit_cost" numeric,
  "farm_id" integer,
  "inventory_category_id" integer,
  "user_id" integer,
  foreign key("inventory_category_id") references inventory_categories("id") on delete set null on update no action,
  foreign key("supplier_id") references suppliers("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "logistics"(
  "id" integer primary key autoincrement not null,
  "reference" varchar,
  "vehicle" varchar,
  "driver" varchar,
  "purpose" varchar,
  "from_location" varchar,
  "to_location" varchar,
  "departure_at" datetime,
  "arrival_at" datetime,
  "animals_count" integer,
  "animal_ids" text,
  "cost" numeric,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "medications"(
  "id" integer primary key autoincrement not null,
  "treatment_id" integer not null,
  "medicine_id" integer not null,
  "dose" varchar not null,
  "frequency" varchar not null,
  "duration_days" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  foreign key("medicine_id") references medicines("id") on delete cascade on update no action,
  foreign key("treatment_id") references treatments("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "medicine_groups"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "medicines"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "name" varchar not null,
  "description" text,
  "medicine_group" varchar,
  "supplier_id" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "quantity" integer not null default('0'),
  "unit" varchar,
  "min_quantity" integer not null default('0'),
  "unit_cost" numeric not null default('0'),
  "inventory_category_id" integer,
  "sku" varchar,
  "medicine_group_id" integer,
  "user_id" integer,
  foreign key("medicine_group_id") references medicine_groups("id") on delete cascade on update no action,
  foreign key("supplier_id") references suppliers("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("inventory_category_id") references inventory_categories("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "milk_records"(
  "id" integer primary key autoincrement not null,
  "animal_id" integer not null,
  "date" date not null,
  "quantity_liters" numeric not null default('0'),
  "staff_id" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("animal_id") references animals("id") on delete cascade on update no action,
  foreign key("staff_id") references staff_profiles("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "milk_sales"(
  "id" integer primary key autoincrement not null,
  "customer_id" integer not null,
  "sale_date" date not null,
  "quantity" numeric not null,
  "unit" varchar not null default('liters'),
  "unit_price" numeric not null,
  "total_price" numeric not null,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer not null,
  "user_id" integer,
  "invoice_number" varchar,
  "paid_amount" numeric not null default '0',
  "status" varchar not null default 'unpaid',
  "sale_transaction_source_type" varchar,
  "sale_transaction_source_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("customer_id") references suppliers("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "pregnancies"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "animal_id" integer not null,
  "reproduction_record_id" integer not null,
  "pregnancy_confirmed_date" date not null,
  "expected_gestation_days" integer not null default('283'),
  "expected_calving_date" date not null,
  "pregnancy_status" varchar not null default('ongoing'),
  "health_notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "user_id" integer,
  foreign key("reproduction_record_id") references reproduction_records("id") on delete cascade on update no action,
  foreign key("animal_id") references animals("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "pregnancies_farm_id_animal_id_index" on "pregnancies"(
  "farm_id",
  "animal_id"
);
CREATE TABLE IF NOT EXISTS "purchase_items"(
  "id" integer primary key autoincrement not null,
  "purchase_id" integer not null,
  "item_type" varchar not null,
  "item_id" integer not null,
  "quantity" float not null,
  "unit_price" numeric not null,
  "sub_total" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "batch_no" varchar,
  "expiry_date" date,
  foreign key("purchase_id") references purchases("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "purchase_items_item_type_item_id_index" on "purchase_items"(
  "item_type",
  "item_id"
);
CREATE TABLE IF NOT EXISTS "purchases"(
  "id" integer primary key autoincrement not null,
  "supplier_id" integer,
  "invoice_number" varchar not null,
  "total_amount" numeric not null,
  "discount" numeric,
  "discount_type" varchar,
  "tax" numeric,
  "tax_percentage" numeric,
  "purchased_at" date not null,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  "paid_amount" numeric default '0',
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("supplier_id") references suppliers("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "reproduction_records"(
  "id" integer primary key autoincrement not null,
  "animal_id" integer not null,
  "event" varchar not null,
  "partner_id" integer,
  "event_date" date,
  "outcome" varchar,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "heat_stage" varchar,
  "performed_by" integer,
  "artificial_insemination_id" integer,
  "user_id" integer,
  foreign key("artificial_insemination_id") references artificial_inseminations("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("animal_id") references animals("id") on delete cascade on update no action,
  foreign key("partner_id") references animals("id") on delete set null on update no action,
  foreign key("performed_by") references users("id") on delete set null on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "reproduction_records_farm_id_animal_id_index" on "reproduction_records"(
  "farm_id",
  "animal_id"
);
CREATE TABLE IF NOT EXISTS "suppliers"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "contact_name" varchar,
  "phone" varchar,
  "email" varchar,
  "address" text,
  "metadata" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "treatments"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "instructions" text,
  "farm_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "vaccine_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "manufacturer" varchar,
  "dose" varchar,
  "doses_per_animal" integer,
  "route" varchar,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "pregnancy_checkups"(
  "id" integer primary key autoincrement not null,
  "pregnancy_id" integer not null,
  "checkup_date" datetime not null,
  "checkup_result" varchar not null,
  "observations" text,
  "checked_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "farm_id" integer not null,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("checked_by") references users("id") on delete set null on update no action,
  foreign key("pregnancy_id") references pregnancies("id") on delete cascade on update no action,
  foreign key("farm_id") references "farms"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "stock_movements"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "item_type" varchar not null,
  "item_id" integer not null,
  "movement_type" varchar check("movement_type" in('in', 'out', 'adjustment')) not null,
  "source_event_type" varchar check("source_event_type" in('purchase', 'consumption', 'adjustment', 'transfer', 'loss', 'expired')) not null,
  "source_type" varchar,
  "source_id" integer,
  "quantity" numeric not null,
  "unit_cost" numeric,
  "batch_no" varchar,
  "expiry_date" date,
  "movement_date" date not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "stock_movements_item_type_item_id_index" on "stock_movements"(
  "item_type",
  "item_id"
);
CREATE INDEX "stock_movements_source_type_source_id_index" on "stock_movements"(
  "source_type",
  "source_id"
);
CREATE TABLE IF NOT EXISTS "feeding_items"(
  "id" integer primary key autoincrement not null,
  "feeding_record_id" integer not null,
  "item_id" integer not null,
  "quantity" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("feeding_record_id") references "feeding_records"("id") on delete cascade,
  foreign key("item_id") references "inventory_items"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "settings"(
  "id" integer primary key autoincrement not null,
  "app_title" varchar not null default 'AgroSass',
  "currency" varchar not null default 'USD',
  "timezone" varchar not null default 'UTC',
  "created_at" datetime,
  "updated_at" datetime,
  "inventory_consumption_type" varchar not null default 'FIFO',
  "logo_path" varchar,
  "site_title" varchar,
  "site_description" text,
  "website_currency" varchar,
  "super_admin_mail_mailer" varchar,
  "super_admin_mail_host" varchar,
  "super_admin_mail_port" integer,
  "super_admin_mail_username" varchar,
  "super_admin_mail_password" text,
  "super_admin_mail_encryption" varchar,
  "super_admin_mail_from_address" varchar,
  "super_admin_mail_from_name" varchar,
  "website_logo_path" varchar
);
CREATE TABLE IF NOT EXISTS "artificial_inseminations"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "reproduction_record_id" integer not null,
  "semen_batch_no" varchar not null,
  "semen_company" varchar,
  "insemination_date" date not null,
  "vet_id" integer,
  "cost" numeric not null default('0'),
  "remarks" text,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer not null,
  "breed_id" integer,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("reproduction_record_id") references reproduction_records("id") on delete cascade on update no action,
  foreign key("vet_id") references users("id") on delete set null on update no action,
  foreign key("breed_id") references "breeds"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "diseases"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "farm_id" integer not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "description" text,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "health_issues"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "description" text,
  "farm_id" integer not null,
  "animal_id" integer not null,
  "diagnosed_at" date,
  "severity" varchar,
  "symptoms" text,
  "diagnosis" text,
  "status" varchar not null default('active'),
  "recovered_at" date,
  "diagnosed_by" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "user_id" integer,
  "disease_id" integer,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("animal_id") references animals("id") on delete cascade on update no action,
  foreign key("diagnosed_by") references staff_profiles("id") on delete set null on update no action,
  foreign key("disease_id") references "diseases"("id") on delete set null
);
CREATE UNIQUE INDEX "event_types_name_farm_id_unique" on "event_types"(
  "name",
  "farm_id"
);
CREATE TABLE IF NOT EXISTS "vaccination_medications"(
  "id" integer primary key autoincrement not null,
  "vaccination_record_id" integer not null,
  "medicine_id" integer not null,
  "quantity" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  "dose" varchar,
  foreign key("vaccination_record_id") references "vaccination_records"("id") on delete cascade,
  foreign key("medicine_id") references "medicines"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "vaccination_records"(
  "id" integer primary key autoincrement not null,
  "animal_id" integer not null,
  "administered_at" date,
  "next_due_at" date,
  "staff_id" integer,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "farm_id" integer,
  "user_id" integer,
  "disease_id" integer,
  foreign key("disease_id") references diseases("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("animal_id") references animals("id") on delete cascade on update no action,
  foreign key("staff_id") references staff_profiles("id") on delete set null on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action
);
CREATE TABLE IF NOT EXISTS "departments"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "name" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "departments_farm_id_name_unique" on "departments"(
  "farm_id",
  "name"
);
CREATE TABLE IF NOT EXISTS "designations"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "level" integer not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "designations_name_farm_id_unique" on "designations"(
  "name",
  "farm_id"
);
CREATE TABLE IF NOT EXISTS "employee_documents"(
  "id" integer primary key autoincrement not null,
  "employee_id" integer not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "document_type" varchar not null,
  "document_number" varchar,
  "expiry_date" date,
  "file_path" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "shifts"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "start_time" time not null,
  "end_time" time not null,
  "grace_minutes" integer not null default '0',
  "user_id" integer not null,
  "farm_id" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "employee_shifts"(
  "id" integer primary key autoincrement not null,
  "employee_id" integer not null,
  "shift_id" integer not null,
  "user_id" integer not null,
  "farm_id" integer not null,
  "effective_from" date not null,
  "effective_to" date,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  foreign key("shift_id") references "shifts"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "attendances"(
  "id" integer primary key autoincrement not null,
  "employee_id" integer not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "date" date not null,
  "check_in" time,
  "check_out" time,
  "working_minutes" integer,
  "overtime_minutes" integer,
  "status" varchar check("status" in('present', 'absent', 'leave', 'late')) not null,
  "source" varchar check("source" in('manual', 'biometric', 'mobile')) not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "leave_types"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "paid" tinyint(1) not null default '0',
  "max_days_per_year" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "leave_requests"(
  "id" integer primary key autoincrement not null,
  "employee_id" integer not null,
  "leave_type_id" integer not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "start_date" date not null,
  "end_date" date not null,
  "total_days" integer not null,
  "reason" text,
  "status" varchar check("status" in('pending', 'approved', 'rejected')) not null default 'pending',
  "approved_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  foreign key("leave_type_id") references "leave_types"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("approved_by") references "employees"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "salary_structures"(
  "id" integer primary key autoincrement not null,
  "employee_id" integer not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "basic_salary" numeric not null,
  "house_allowance" numeric not null default '0',
  "medical_allowance" numeric not null default '0',
  "transport_allowance" numeric not null default '0',
  "overtime_rate" numeric not null default '0',
  "effective_from" date not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("employee_id") references "employees"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "payroll_runs"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "month" varchar not null,
  "year" integer not null,
  "status" varchar not null default 'draft',
  "generated_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "payroll_items"(
  "id" integer primary key autoincrement not null,
  "payroll_run_id" integer not null,
  "employee_id" integer not null,
  "basic_salary" numeric not null default '0',
  "house_allowance" numeric not null default '0',
  "medical_allowance" numeric not null default '0',
  "transport_allowance" numeric not null default '0',
  "overtime_hours" numeric not null default '0',
  "overtime_rate" numeric not null default '0',
  "overtime_amount" numeric not null default '0',
  "gross_salary" numeric not null default '0',
  "deductions" numeric not null default '0',
  "net_salary" numeric not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "working_days" integer not null default '0',
  "paid_leave_days" integer not null default '0',
  "unpaid_leave_days" integer not null default '0',
  "leave_deduction" numeric not null default '0',
  "bonus" numeric not null default '0',
  "festival_bonus" numeric not null default '0',
  "performance_incentive" numeric not null default '0',
  "tax_amount" numeric not null default '0',
  "loan_deduction" numeric not null default '0',
  "other_deductions" numeric not null default '0',
  foreign key("payroll_run_id") references "payroll_runs"("id") on delete cascade,
  foreign key("employee_id") references "employees"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "chart_of_accounts"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer,
  "user_id" integer not null,
  "code" varchar not null,
  "name" varchar not null,
  "type" varchar check("type" in('asset', 'liability', 'equity', 'income', 'expense')) not null,
  "parent_id" integer,
  "is_system" tinyint(1) not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("parent_id") references "chart_of_accounts"("id") on delete cascade
);
CREATE UNIQUE INDEX "chart_of_accounts_farm_id_code_unique" on "chart_of_accounts"(
  "farm_id",
  "code"
);
CREATE TABLE IF NOT EXISTS "journal_entries"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "entry_date" date not null,
  "reference_type" varchar not null,
  "reference_id" integer,
  "description" text,
  "status" varchar not null default 'draft',
  "created_by" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("created_by") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "journal_entry_lines"(
  "id" integer primary key autoincrement not null,
  "journal_entry_id" integer not null,
  "account_id" integer not null,
  "debit_amount" numeric not null default '0',
  "credit_amount" numeric not null default '0',
  "narration" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("journal_entry_id") references "journal_entries"("id") on delete cascade,
  foreign key("account_id") references "chart_of_accounts"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "customers"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "user_id" integer,
  "name" varchar not null,
  "phone" varchar,
  "email" varchar,
  "address" varchar,
  "type" varchar check("type" in('milk_buyer', 'animal_buyer', 'wholesaler')) not null,
  "created_at" datetime,
  "updated_at" datetime,
  "contact_person" varchar,
  "notes" text,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sales"(
  "id" integer primary key autoincrement not null,
  "customer_id" integer not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "invoice_date" date not null,
  "total_amount" numeric not null default '0',
  "paid_amount" numeric not null default '0',
  "status" varchar not null default 'unpaid',
  "created_at" datetime,
  "updated_at" datetime,
  "invoice_number" varchar,
  foreign key("customer_id") references "customers"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "sales_items"(
  "id" integer primary key autoincrement not null,
  "sale_id" integer not null,
  "quantity" integer not null,
  "unit_price" numeric not null,
  "total_price" numeric not null,
  "created_at" datetime,
  "updated_at" datetime,
  "item_type" varchar,
  "item_id" integer,
  foreign key("sale_id") references sales("id") on delete cascade on update no action
);
CREATE INDEX "sales_items_item_type_item_id_index" on "sales_items"(
  "item_type",
  "item_id"
);
CREATE TABLE IF NOT EXISTS "animals"(
  "id" integer primary key autoincrement not null,
  "tag" varchar,
  "name" varchar,
  "sex" varchar not null default('unknown'),
  "dob" date,
  "breed_id" integer,
  "farm_id" integer,
  "herd_id" integer,
  "status" varchar not null default('active'),
  "current_weight_kg" numeric,
  "color" varchar,
  "acquired_at" date,
  "attributes" text,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  "animal_type" varchar,
  "purchase_price" numeric,
  "image" varchar,
  "user_id" integer,
  "supplier_id" integer,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("breed_id") references breeds("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete set null on update no action,
  foreign key("herd_id") references herds("id") on delete set null on update no action,
  foreign key("supplier_id") references "suppliers"("id") on delete set null
);
CREATE INDEX "ms_sts_type_id_idx" on "milk_sales"(
  "sale_transaction_source_type",
  "sale_transaction_source_id"
);
CREATE TABLE IF NOT EXISTS "sale_transactions"(
  "id" integer primary key autoincrement not null,
  "customer_id" integer not null,
  "transaction_date" date not null,
  "amount" numeric not null,
  "payment_method" varchar not null,
  "reference_number" varchar,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "sale_id" integer,
  "farm_id" integer,
  "user_id" integer,
  "sale_transaction_source_type" varchar,
  "sale_transaction_source_id" integer,
  foreign key("user_id") references users("id") on delete set null on update no action,
  foreign key("farm_id") references farms("id") on delete set null on update no action,
  foreign key("customer_id") references customers("id") on delete cascade on update no action,
  foreign key("sale_id") references sales("id") on delete set null on update no action
);
CREATE INDEX "st_sts_type_id_idx" on "sale_transactions"(
  "sale_transaction_source_type",
  "sale_transaction_source_id"
);
CREATE TABLE IF NOT EXISTS "supplier_payments"(
  "id" integer primary key autoincrement not null,
  "supplier_id" integer not null,
  "purchase_source_type" varchar,
  "purchase_source_id" integer,
  "payment_date" date not null,
  "amount" numeric not null,
  "payment_method" varchar check("payment_method" in('Cash', 'Bank Transfer', 'Mobile Banking', 'Cheque')) not null,
  "reference_number" varchar,
  "notes" text,
  "farm_id" integer,
  "user_id" integer,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("supplier_id") references "suppliers"("id") on delete cascade,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE INDEX "supplier_payments_purchase_source_type_purchase_source_id_index" on "supplier_payments"(
  "purchase_source_type",
  "purchase_source_id"
);
CREATE TABLE IF NOT EXISTS "cash_accounts"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer,
  "user_id" integer not null,
  "account_id" integer,
  "name" varchar not null,
  "type" varchar check("type" in('cash', 'bank', 'mobile')) not null,
  "account_number" varchar,
  "bank_name" varchar,
  "branch_name" varchar,
  "opening_balance" numeric not null default '0',
  "current_balance" numeric not null default '0',
  "is_active" tinyint(1) not null default '1',
  "description" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("account_id") references "chart_of_accounts"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "cash_transactions"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer,
  "user_id" integer not null,
  "cash_account_id" integer not null,
  "transaction_date" date not null,
  "amount" numeric not null,
  "direction" varchar check("direction" in('in', 'out')) not null,
  "reference_type" varchar,
  "reference_id" integer,
  "description" varchar,
  "payment_method" varchar,
  "balance_after" numeric not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("cash_account_id") references "cash_accounts"("id") on delete cascade
);
CREATE INDEX "cash_transactions_reference_type_reference_id_index" on "cash_transactions"(
  "reference_type",
  "reference_id"
);
CREATE TABLE IF NOT EXISTS "fixed_assets"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer,
  "user_id" integer,
  "name" varchar not null,
  "asset_type" varchar check("asset_type" in('machinery', 'shed', 'vehicle', 'equipment', 'land', 'building', 'other')) not null default 'machinery',
  "purchase_value" numeric not null,
  "purchase_date" date not null,
  "useful_life_years" integer not null,
  "depreciation_method" varchar check("depreciation_method" in('straight_line')) not null default 'straight_line',
  "status" varchar check("status" in('active', 'disposed', 'under_maintenance', 'sold')) not null default 'active',
  "location" varchar,
  "serial_number" varchar,
  "notes" text,
  "created_at" datetime,
  "updated_at" datetime,
  "deleted_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete set null,
  foreign key("user_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "subscription_plans"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "slug" varchar not null,
  "monthly_price_cents" integer not null default '0',
  "yearly_discount_percent" integer not null default '15',
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "subscription_plans_slug_unique" on "subscription_plans"(
  "slug"
);
CREATE TABLE IF NOT EXISTS "farm_subscriptions"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "subscription_plan_id" integer not null,
  "billing_period" varchar not null,
  "starts_on" date not null,
  "ends_on" date not null,
  "next_billing_on" date,
  "cancelled_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("subscription_plan_id") references "subscription_plans"("id") on delete restrict
);
CREATE INDEX "farm_subscriptions_farm_id_ends_on_index" on "farm_subscriptions"(
  "farm_id",
  "ends_on"
);
CREATE INDEX "farm_subscriptions_next_billing_on_index" on "farm_subscriptions"(
  "next_billing_on"
);
CREATE TABLE IF NOT EXISTS "subscription_features"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "key" varchar not null,
  "description" text,
  "is_active" tinyint(1) not null default '1',
  "sort_order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "subscription_features_key_unique" on "subscription_features"(
  "key"
);
CREATE TABLE IF NOT EXISTS "subscription_invoices"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "farm_subscription_id" integer not null,
  "invoice_number" varchar not null,
  "invoice_date" date not null,
  "subtotal_cents" integer not null,
  "discount_cents" integer not null default '0',
  "total_cents" integer not null,
  "currency" varchar not null default 'BDT',
  "status" varchar not null default 'unpaid',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("farm_subscription_id") references "farm_subscriptions"("id") on delete cascade
);
CREATE INDEX "subscription_invoices_farm_id_invoice_date_index" on "subscription_invoices"(
  "farm_id",
  "invoice_date"
);
CREATE INDEX "subscription_invoices_farm_subscription_id_status_index" on "subscription_invoices"(
  "farm_subscription_id",
  "status"
);
CREATE UNIQUE INDEX "subscription_invoices_invoice_number_unique" on "subscription_invoices"(
  "invoice_number"
);
CREATE TABLE IF NOT EXISTS "subscription_payments"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "subscription_invoice_id" integer not null,
  "gateway" varchar not null,
  "amount_cents" integer not null,
  "currency" varchar not null default 'BDT',
  "status" varchar not null default 'initiated',
  "provider_payment_id" varchar,
  "provider_payload" text,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("subscription_invoice_id") references "subscription_invoices"("id") on delete cascade
);
CREATE INDEX "subscription_payments_farm_id_created_at_index" on "subscription_payments"(
  "farm_id",
  "created_at"
);
CREATE INDEX "subscription_payments_gateway_status_index" on "subscription_payments"(
  "gateway",
  "status"
);
CREATE INDEX "subscription_payments_provider_payment_id_index" on "subscription_payments"(
  "provider_payment_id"
);
CREATE TABLE IF NOT EXISTS "subscription_plan_features"(
  "id" integer primary key autoincrement not null,
  "subscription_plan_id" integer not null,
  "subscription_feature_id" integer not null,
  "is_enabled" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("subscription_plan_id") references "subscription_plans"("id") on delete cascade,
  foreign key("subscription_feature_id") references "subscription_features"("id") on delete cascade
);
CREATE UNIQUE INDEX "plan_feature_unique" on "subscription_plan_features"(
  "subscription_plan_id",
  "subscription_feature_id"
);
CREATE TABLE IF NOT EXISTS "payment_gateway_configs"(
  "id" integer primary key autoincrement not null,
  "gateway" varchar not null,
  "is_enabled" tinyint(1) not null default('0'),
  "is_default" tinyint(1) not null default('0'),
  "config" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "payment_gateway_configs_gateway_unique" on "payment_gateway_configs"(
  "gateway"
);
CREATE INDEX "payment_gateway_configs_is_default_index" on "payment_gateway_configs"(
  "is_default"
);
CREATE TABLE IF NOT EXISTS "farm_notifications"(
  "id" integer primary key autoincrement not null,
  "farm_id" integer not null,
  "sent_by_user_id" integer,
  "message" text not null,
  "sent_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("farm_id") references "farms"("id") on delete cascade,
  foreign key("sent_by_user_id") references "users"("id") on delete set null
);
CREATE TABLE IF NOT EXISTS "demo_requests"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "phone" varchar,
  "company" varchar,
  "country" varchar,
  "preferred_date" varchar,
  "preferred_time" varchar,
  "timezone" varchar,
  "message" text,
  "status" varchar not null default 'new',
  "emailed_at" datetime,
  "scheduled_at" datetime,
  "meta" text,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "employees"(
  "id" integer primary key autoincrement not null,
  "employee_code" varchar not null,
  "farm_id" integer not null,
  "user_id" integer not null,
  "department_id" integer not null,
  "designation_id" integer not null,
  "first_name" varchar not null,
  "last_name" varchar not null,
  "gender" varchar not null,
  "date_of_birth" date,
  "phone" varchar,
  "email" varchar,
  "address" varchar,
  "join_date" date not null,
  "employment_type" varchar not null,
  "salary_type" varchar not null,
  "status" varchar not null default('active'),
  "user_email" varchar not null,
  "password" varchar not null,
  "created_at" datetime,
  "updated_at" datetime,
  "bonus" numeric,
  "festival_bonus" numeric,
  "performance_incentive" numeric,
  "tax_amount" numeric,
  "loan_deduction" numeric,
  "other_deductions" numeric,
  "employee_user_id" integer,
  foreign key("designation_id") references designations("id") on delete cascade on update no action,
  foreign key("department_id") references departments("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action,
  foreign key("farm_id") references farms("id") on delete cascade on update no action,
  foreign key("employee_user_id") references "users"("id") on delete set null
);
CREATE UNIQUE INDEX "calves_farm_id_tag_number_unique" on "calves"(
  "farm_id",
  "tag_number"
);
CREATE UNIQUE INDEX "animals_farm_id_tag_unique" on "animals"(
  "farm_id",
  "tag"
);
CREATE UNIQUE INDEX "inventory_categories_farm_id_name_unique" on "inventory_categories"(
  "farm_id",
  "name"
);
CREATE UNIQUE INDEX "inventory_items_farm_id_sku_unique" on "inventory_items"(
  "farm_id",
  "sku"
);
CREATE UNIQUE INDEX "medicines_farm_id_sku_unique" on "medicines"(
  "farm_id",
  "sku"
);
CREATE UNIQUE INDEX "milk_records_farm_id_animal_id_date_unique" on "milk_records"(
  "farm_id",
  "animal_id",
  "date"
);
CREATE UNIQUE INDEX "purchases_farm_id_invoice_number_unique" on "purchases"(
  "farm_id",
  "invoice_number"
);
CREATE UNIQUE INDEX "milk_sales_farm_id_invoice_number_unique" on "milk_sales"(
  "farm_id",
  "invoice_number"
);
CREATE UNIQUE INDEX "employees_farm_id_employee_code_unique" on "employees"(
  "farm_id",
  "employee_code"
);
CREATE UNIQUE INDEX "employees_farm_id_user_email_unique" on "employees"(
  "farm_id",
  "user_email"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_01_11_000101_create_farms_table',1);
INSERT INTO migrations VALUES(5,'2026_01_11_000102_create_herds_table',1);
INSERT INTO migrations VALUES(6,'2026_01_11_000103_create_breeds_table',1);
INSERT INTO migrations VALUES(7,'2026_01_11_000104_create_animals_table',1);
INSERT INTO migrations VALUES(8,'2026_01_11_000105_create_feed_types_table',1);
INSERT INTO migrations VALUES(9,'2026_01_11_000106_create_feeding_records_table',1);
INSERT INTO migrations VALUES(10,'2026_01_11_000107_create_vaccine_types_table',1);
INSERT INTO migrations VALUES(11,'2026_01_11_000108_create_vaccination_records_table',1);
INSERT INTO migrations VALUES(12,'2026_01_11_000109_create_health_events_table',1);
INSERT INTO migrations VALUES(13,'2026_01_11_000111_create_reproduction_records_table',1);
INSERT INTO migrations VALUES(14,'2026_01_11_000112_create_milk_records_table',1);
INSERT INTO migrations VALUES(15,'2026_01_11_000113_create_staff_profiles_table',1);
INSERT INTO migrations VALUES(16,'2026_01_11_000114_create_roles_table',1);
INSERT INTO migrations VALUES(17,'2026_01_11_000116_create_inventory_items_table',1);
INSERT INTO migrations VALUES(18,'2026_01_11_000117_create_suppliers_table',1);
INSERT INTO migrations VALUES(19,'2026_01_11_000118_create_purchases_table',1);
INSERT INTO migrations VALUES(20,'2026_01_11_000119_create_logistics_table',1);
INSERT INTO migrations VALUES(21,'2026_01_11_000121_create_expenses_table',1);
INSERT INTO migrations VALUES(22,'2026_01_12_073318_create_permission_tables',1);
INSERT INTO migrations VALUES(23,'2026_01_13_082008_add_unit_cost_to_inventory_items_table',1);
INSERT INTO migrations VALUES(24,'2026_01_13_111913_create_health_issues_table',1);
INSERT INTO migrations VALUES(25,'2026_01_13_112006_create_disease_treatments_table',1);
INSERT INTO migrations VALUES(26,'2026_01_13_115540_add_missing_fields_to_animals_table',1);
INSERT INTO migrations VALUES(27,'2026_01_13_135350_add_category_and_cost_to_feed_types_table',1);
INSERT INTO migrations VALUES(28,'2026_01_13_135407_add_cost_to_feeding_records_table',1);
INSERT INTO migrations VALUES(29,'2026_01_13_200948_create_milk_sales_table',1);
INSERT INTO migrations VALUES(30,'2026_01_30_054102_add_demo_data_seeded_to_farms_table',1);
INSERT INTO migrations VALUES(31,'2026_01_30_054401_add_user_id_to_farms_table',1);
INSERT INTO migrations VALUES(32,'2026_01_30_061735_add_farm_id_to_breeds_table',1);
INSERT INTO migrations VALUES(33,'2026_01_30_063539_add_farm_id_to_feed_types_table',1);
INSERT INTO migrations VALUES(34,'2026_01_30_064206_add_farm_id_to_vaccine_types_table',1);
INSERT INTO migrations VALUES(35,'2026_01_30_064801_add_farm_id_to_reproduction_records_table',1);
INSERT INTO migrations VALUES(36,'2026_01_30_064958_add_farm_id_to_milk_records_table',1);
INSERT INTO migrations VALUES(37,'2026_01_30_065516_add_farm_id_to_suppliers_table',1);
INSERT INTO migrations VALUES(38,'2026_01_30_065739_add_farm_id_to_inventory_items_table',1);
INSERT INTO migrations VALUES(39,'2026_01_30_065949_add_farm_id_to_purchases_table',1);
INSERT INTO migrations VALUES(40,'2026_01_30_070201_add_farm_id_to_logistics_table',1);
INSERT INTO migrations VALUES(41,'2026_01_30_080602_add_farm_id_to_vaccination_records_table',1);
INSERT INTO migrations VALUES(42,'2026_01_30_082530_add_farm_id_and_health_issue_id_to_health_events_table',1);
INSERT INTO migrations VALUES(43,'2026_01_30_085601_add_health_event_id_to_disease_treatments',1);
INSERT INTO migrations VALUES(44,'2026_01_30_093344_add_farm_id_to_milk_sales_table',1);
INSERT INTO migrations VALUES(45,'2026_01_30_100011_add_farm_id_to_users_table',1);
INSERT INTO migrations VALUES(46,'2026_01_30_150909_add_unique_name_farm_id_to_breeds_table',1);
INSERT INTO migrations VALUES(47,'2026_01_31_064851_add_heat_stage_performed_by_and_indexes_to_reproduction_records_table',1);
INSERT INTO migrations VALUES(48,'2026_01_31_065830_create_artificial_inseminations_table',1);
INSERT INTO migrations VALUES(49,'2026_01_31_070006_add_artificial_insemination_id_to_reproduction_records_table',1);
INSERT INTO migrations VALUES(50,'2026_01_31_110926_create_pregnancies_table',1);
INSERT INTO migrations VALUES(51,'2026_01_31_112616_create_pregnancy_checkups_table',1);
INSERT INTO migrations VALUES(52,'2026_01_31_121121_add_time_to_pregnancy_checkups_checkup_date_column',1);
INSERT INTO migrations VALUES(53,'2026_01_31_124430_create_calving_records_table',1);
INSERT INTO migrations VALUES(54,'2026_01_31_143619_create_calves_table',1);
INSERT INTO migrations VALUES(55,'2026_01_31_190357_create_inventory_categories_table',1);
INSERT INTO migrations VALUES(56,'2026_01_31_190413_add_category_id_to_inventory_items_table',1);
INSERT INTO migrations VALUES(57,'2026_02_01_130406_create_event_types_table',1);
INSERT INTO migrations VALUES(58,'2026_02_02_082716_create_treatments_table',1);
INSERT INTO migrations VALUES(59,'2026_02_02_082733_create_medicines_table',1);
INSERT INTO migrations VALUES(60,'2026_02_02_082841_create_medications_table',1);
INSERT INTO migrations VALUES(61,'2026_02_02_142544_create_disease_treatment_medications_table',1);
INSERT INTO migrations VALUES(62,'2026_02_02_174304_add_unit_price_and_qty_to_disease_treatment_medications_table',1);
INSERT INTO migrations VALUES(63,'2026_02_02_174814_remove_administered_by_from_disease_treatment_medications_table',1);
INSERT INTO migrations VALUES(64,'2026_02_03_111442_add_farm_id_to_event_types_table',1);
INSERT INTO migrations VALUES(65,'2026_02_03_160238_add_inventory_fields_to_medicines_table',1);
INSERT INTO migrations VALUES(66,'2026_02_03_190143_create_medicine_groups_table',1);
INSERT INTO migrations VALUES(67,'2026_02_03_190258_add_medicine_group_id_to_medicines_table',1);
INSERT INTO migrations VALUES(68,'2026_02_03_195743_update_purchases_table_for_invoice_structure',1);
INSERT INTO migrations VALUES(69,'2026_02_04_195340_add_user_id_to_animals_table',1);
INSERT INTO migrations VALUES(70,'2026_02_04_195351_add_user_id_to_breeds_table',1);
INSERT INTO migrations VALUES(71,'2026_02_04_200645_add_user_id_to_artificial_inseminations_table',1);
INSERT INTO migrations VALUES(72,'2026_02_04_201423_add_user_id_to_calves_table',1);
INSERT INTO migrations VALUES(73,'2026_02_04_201713_add_user_id_to_calving_records_table',1);
INSERT INTO migrations VALUES(74,'2026_02_04_201745_add_user_id_to_disease_treatment_medications_table',1);
INSERT INTO migrations VALUES(75,'2026_02_04_201817_add_user_id_to_disease_treatments_table',1);
INSERT INTO migrations VALUES(76,'2026_02_04_201848_add_user_id_to_event_types_table',1);
INSERT INTO migrations VALUES(77,'2026_02_04_201928_add_user_id_to_expenses_table',1);
INSERT INTO migrations VALUES(78,'2026_02_05_063714_add_user_id_to_feed_types_table',1);
INSERT INTO migrations VALUES(79,'2026_02_05_063835_add_user_id_to_health_events_table',1);
INSERT INTO migrations VALUES(80,'2026_02_05_063911_add_user_id_to_health_issues_table',1);
INSERT INTO migrations VALUES(81,'2026_02_05_064103_add_user_id_to_inventory_categories_table',1);
INSERT INTO migrations VALUES(82,'2026_02_05_064242_add_user_id_to_inventory_items_table',1);
INSERT INTO migrations VALUES(83,'2026_02_05_064320_add_user_id_to_logistics_table',1);
INSERT INTO migrations VALUES(84,'2026_02_05_064407_add_user_id_to_medications_table',1);
INSERT INTO migrations VALUES(85,'2026_02_05_064459_add_user_id_to_medicine_groups_table',1);
INSERT INTO migrations VALUES(86,'2026_02_05_064543_add_user_id_to_medicines_table',1);
INSERT INTO migrations VALUES(87,'2026_02_05_064636_add_user_id_to_milk_records_table',1);
INSERT INTO migrations VALUES(88,'2026_02_05_064734_add_user_id_to_milk_sales_table',1);
INSERT INTO migrations VALUES(89,'2026_02_05_064825_add_user_id_to_pregnancies_table',1);
INSERT INTO migrations VALUES(90,'2026_02_05_064914_add_user_id_to_pregnancy_checkups_table',1);
INSERT INTO migrations VALUES(91,'2026_02_05_065111_add_user_id_to_purchase_items_table',1);
INSERT INTO migrations VALUES(92,'2026_02_05_065203_add_user_id_to_purchases_table',1);
INSERT INTO migrations VALUES(93,'2026_02_05_065302_add_user_id_to_reproduction_records_table',1);
INSERT INTO migrations VALUES(94,'2026_02_05_065615_add_user_id_to_suppliers_table',1);
INSERT INTO migrations VALUES(95,'2026_02_05_065712_add_user_id_to_treatments_table',1);
INSERT INTO migrations VALUES(96,'2026_02_05_065818_add_user_id_to_vaccination_records_table',1);
INSERT INTO migrations VALUES(97,'2026_02_05_065931_add_user_id_to_vaccine_types_table',1);
INSERT INTO migrations VALUES(98,'2026_02_05_071425_add_farm_id_to_pregnancy_checkups_table',1);
INSERT INTO migrations VALUES(99,'2026_02_05_141205_add_batch_no_and_expiry_date_to_purchase_items_table',1);
INSERT INTO migrations VALUES(100,'2026_02_05_143829_create_stock_movements_table',1);
INSERT INTO migrations VALUES(101,'2026_02_06_140514_create_feeding_items_table',1);
INSERT INTO migrations VALUES(102,'2026_02_06_191127_remove_cost_from_disease_treatments_table',1);
INSERT INTO migrations VALUES(103,'2026_02_06_191133_remove_cost_and_unit_price_from_disease_treatment_medications_table',1);
INSERT INTO migrations VALUES(104,'2026_02_06_193754_create_settings_table',1);
INSERT INTO migrations VALUES(105,'2026_02_06_203753_add_inventory_consumption_type_and_remove_logo_to_settings_table',1);
INSERT INTO migrations VALUES(106,'2026_02_06_205107_add_logo_path_to_settings_table',1);
INSERT INTO migrations VALUES(107,'2026_02_07_062753_add_origin_and_animal_type_to_breeds_table',1);
INSERT INTO migrations VALUES(108,'2026_02_07_074505_replace_bull_breed_with_breed_id_in_artificial_inseminations_table',1);
INSERT INTO migrations VALUES(109,'2026_02_08_182822_create_diseases_table',1);
INSERT INTO migrations VALUES(110,'2026_02_08_191755_add_description_to_diseases_table',1);
INSERT INTO migrations VALUES(111,'2026_02_08_194740_add_disease_id_to_health_issues_table',1);
INSERT INTO migrations VALUES(112,'2026_02_08_200918_add_unique_constraint_to_event_types_table',1);
INSERT INTO migrations VALUES(113,'2026_02_08_201058_drop_name_unique_from_event_types_table',1);
INSERT INTO migrations VALUES(114,'2026_02_09_064549_add_disease_id_to_vaccination_records_table',1);
INSERT INTO migrations VALUES(115,'2026_02_09_073756_create_vaccination_medications_table',1);
INSERT INTO migrations VALUES(116,'2026_02_09_075713_remove_batch_number_from_vaccination_records_table',1);
INSERT INTO migrations VALUES(117,'2026_02_09_094929_add_dose_to_vaccination_medications_table',1);
INSERT INTO migrations VALUES(118,'2026_02_09_104855_remove_vaccine_type_id_from_vaccination_records_table',1);
INSERT INTO migrations VALUES(119,'2026_02_09_175901_create_departments_table',1);
INSERT INTO migrations VALUES(120,'2026_02_09_184500_create_designations_table',1);
INSERT INTO migrations VALUES(121,'2026_02_09_191711_create_employees_table',1);
INSERT INTO migrations VALUES(122,'2026_02_10_060145_create_employee_documents_table',1);
INSERT INTO migrations VALUES(123,'2026_02_10_100556_create_shifts_table',1);
INSERT INTO migrations VALUES(124,'2026_02_10_105531_create_employee_shifts_table',1);
INSERT INTO migrations VALUES(125,'2026_02_10_133740_create_attendances_table',1);
INSERT INTO migrations VALUES(126,'2026_02_11_061234_create_leave_types_table',1);
INSERT INTO migrations VALUES(127,'2026_02_11_080419_create_leave_requests_table',1);
INSERT INTO migrations VALUES(128,'2026_02_11_114232_create_salary_structures_table',1);
INSERT INTO migrations VALUES(129,'2026_02_11_141057_create_payroll_runs_table',1);
INSERT INTO migrations VALUES(130,'2026_02_11_143822_create_payroll_items_table',1);
INSERT INTO migrations VALUES(131,'2026_02_12_190138_add_payroll_fields_to_employees_table',1);
INSERT INTO migrations VALUES(132,'2026_02_12_190927_add_new_fields_to_payroll_items_table',1);
INSERT INTO migrations VALUES(133,'2026_02_13_115948_create_chart_of_accounts_table',1);
INSERT INTO migrations VALUES(134,'2026_02_13_140417_create_journal_entries_table',1);
INSERT INTO migrations VALUES(135,'2026_02_13_140422_create_journal_entry_lines_table',1);
INSERT INTO migrations VALUES(136,'2026_02_13_174423_create_customers_table',1);
INSERT INTO migrations VALUES(137,'2026_02_13_181955_add_contact_person_and_notes_to_customers_table',1);
INSERT INTO migrations VALUES(138,'2026_02_13_183129_create_sales_table',1);
INSERT INTO migrations VALUES(139,'2026_02_13_183134_create_sales_items_table',1);
INSERT INTO migrations VALUES(140,'2026_02_13_190617_create_sale_transactions_table',1);
INSERT INTO migrations VALUES(141,'2026_02_13_192908_add_sale_id_to_sale_transactions_table',1);
INSERT INTO migrations VALUES(142,'2026_02_13_201429_add_invoice_number_to_sales_table',1);
INSERT INTO migrations VALUES(143,'2026_02_13_203247_add_sale_id_to_sale_transactions_table',1);
INSERT INTO migrations VALUES(144,'2026_02_13_203802_add_farm_and_user_to_sale_transactions_table',1);
INSERT INTO migrations VALUES(145,'2026_02_14_105652_add_polymorphic_item_to_sales_items_table',1);
INSERT INTO migrations VALUES(146,'2026_02_14_153228_add_supplier_id_to_animals_table',1);
INSERT INTO migrations VALUES(147,'2026_02_15_070140_add_invoice_details_to_milk_sales_table',1);
INSERT INTO migrations VALUES(148,'2026_02_15_070259_add_polymorphic_source_to_sale_transactions_table',1);
INSERT INTO migrations VALUES(149,'2026_02_16_074312_remove_reference_from_milk_sales_table',1);
INSERT INTO migrations VALUES(150,'2026_02_16_143238_add_paid_amount_to_purchases_table',1);
INSERT INTO migrations VALUES(151,'2026_02_16_144009_create_supplier_payments_table',1);
INSERT INTO migrations VALUES(152,'2026_02_17_200000_create_cash_accounts_table',1);
INSERT INTO migrations VALUES(153,'2026_02_17_200100_create_cash_transactions_table',1);
INSERT INTO migrations VALUES(154,'2026_02_18_000001_create_fixed_assets_table',1);
INSERT INTO migrations VALUES(155,'2026_02_18_110327_add_event_date_to_health_events_table',1);
INSERT INTO migrations VALUES(156,'2026_02_18_110335_add_treatment_date_to_disease_treatments_table',1);
INSERT INTO migrations VALUES(157,'2026_02_20_061930_add_unit_and_total_cost_to_disease_treatment_medications_table',1);
INSERT INTO migrations VALUES(158,'2026_02_20_095320_add_cost_breakdown_to_health_events_table',1);
INSERT INTO migrations VALUES(159,'2026_02_21_090902_create_subscription_plans_table',1);
INSERT INTO migrations VALUES(160,'2026_02_21_090903_create_farm_subscriptions_table',1);
INSERT INTO migrations VALUES(161,'2026_02_21_090903_create_payment_gateway_configs_table',1);
INSERT INTO migrations VALUES(162,'2026_02_21_090903_create_subscription_features_table',1);
INSERT INTO migrations VALUES(163,'2026_02_21_090903_create_subscription_invoices_table',1);
INSERT INTO migrations VALUES(164,'2026_02_21_090903_create_subscription_payments_table',1);
INSERT INTO migrations VALUES(165,'2026_02_21_090903_create_subscription_plan_features_table',1);
INSERT INTO migrations VALUES(166,'2026_02_22_000001_make_payment_gateway_configs_global',1);
INSERT INTO migrations VALUES(167,'2026_02_22_100748_create_farm_notifications_table',1);
INSERT INTO migrations VALUES(168,'2026_02_23_155758_add_super_admin_fields_to_settings_table',1);
INSERT INTO migrations VALUES(169,'2026_02_23_190202_create_demo_requests_table',1);
INSERT INTO migrations VALUES(170,'2026_02_23_190555_add_super_admin_email_config_to_settings_table',1);
INSERT INTO migrations VALUES(171,'2026_02_24_000000_add_website_logo_path_to_settings_table',1);
INSERT INTO migrations VALUES(172,'2026_02_24_071313_add_employee_user_id_to_employees_table',1);
INSERT INTO migrations VALUES(173,'2026_02_25_075645_add_farm_scoped_unique_indexes',2);
