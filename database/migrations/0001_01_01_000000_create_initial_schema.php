<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * LivestockPro ERP – initial database schema.
 * Works with MySQL (primary) and SQLite (fallback).
 *
 * NOTE: When using SQLite, the database/schema/sqlite-schema.sql is used
 * automatically by `php artisan migrate --schema-path`. This migration is
 * primarily for MySQL installations (e.g. cPanel / shared hosting buyers).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // ── Core Laravel Tables ────────────────────────────────────────────

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // ── Settings ───────────────────────────────────────────────────────

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('app_title')->default('LivestockPro ERP');
            $table->string('currency')->default('USD');
            $table->string('timezone')->default('UTC');
            $table->timestamps();
            $table->string('inventory_consumption_type')->default('FIFO');
            $table->string('logo_path')->nullable();
            $table->string('site_title')->nullable();
            $table->text('site_description')->nullable();
            $table->string('website_currency')->nullable();
            $table->string('super_admin_mail_mailer')->nullable();
            $table->string('super_admin_mail_host')->nullable();
            $table->integer('super_admin_mail_port')->nullable();
            $table->string('super_admin_mail_username')->nullable();
            $table->text('super_admin_mail_password')->nullable();
            $table->string('super_admin_mail_encryption')->nullable();
            $table->string('super_admin_mail_from_address')->nullable();
            $table->string('super_admin_mail_from_name')->nullable();
            $table->string('website_logo_path')->nullable();
        });

        // ── Spatie Permission Tables ───────────────────────────────────────

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
        });

        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_id', 'model_type']);
            $table->index(['model_id', 'model_type']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');
            $table->primary(['permission_id', 'role_id']);
            $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->unique(['role_id', 'user_id']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Subscription / Billing Tables ──────────────────────────────────

        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('monthly_price_cents')->default(0);
            $table->integer('yearly_discount_percent')->default(15);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscription_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('subscription_plan_features', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_plan_id');
            $table->unsignedBigInteger('subscription_feature_id');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
            $table->unique(['subscription_plan_id', 'subscription_feature_id'], 'plan_feature_unique');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('cascade');
            $table->foreign('subscription_feature_id')->references('id')->on('subscription_features')->onDelete('cascade');
        });

        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway')->unique();
            $table->boolean('is_enabled')->default(false);
            $table->boolean('is_default')->default(false)->index();
            $table->text('config')->nullable();
            $table->timestamps();
        });

        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('country')->nullable();
            $table->string('preferred_date')->nullable();
            $table->string('preferred_time')->nullable();
            $table->string('timezone')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->default('new');
            $table->timestamp('emailed_at')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->text('meta')->nullable();
            $table->timestamps();
        });

        // ── Farm & Core Domain Tables ──────────────────────────────────────

        Schema::create('farms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('demo_data_seeded')->default(false);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Add farm_id FK to users (now that farms table exists)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });

        Schema::create('farm_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('subscription_plan_id');
            $table->string('billing_period');
            $table->date('starts_on');
            $table->date('ends_on');
            $table->date('next_billing_on')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['farm_id', 'ends_on']);
            $table->index('next_billing_on');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->onDelete('restrict');
        });

        Schema::create('subscription_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('farm_subscription_id');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->integer('subtotal_cents');
            $table->integer('discount_cents')->default(0);
            $table->integer('total_cents');
            $table->string('currency')->default('BDT');
            $table->string('status')->default('unpaid');
            $table->timestamps();
            $table->index(['farm_id', 'invoice_date']);
            $table->index(['farm_subscription_id', 'status']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('farm_subscription_id')->references('id')->on('farm_subscriptions')->onDelete('cascade');
        });

        Schema::create('subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('subscription_invoice_id');
            $table->string('gateway');
            $table->integer('amount_cents');
            $table->string('currency')->default('BDT');
            $table->string('status')->default('initiated');
            $table->string('provider_payment_id')->nullable()->index();
            $table->text('provider_payload')->nullable();
            $table->timestamps();
            $table->index(['farm_id', 'created_at']);
            $table->index(['gateway', 'status']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('subscription_invoice_id')->references('id')->on('subscription_invoices')->onDelete('cascade');
        });

        Schema::create('farm_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('sent_by_user_id')->nullable();
            $table->text('message');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('sent_by_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // ── Livestock Domain ───────────────────────────────────────────────

        Schema::create('herds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->text('characteristics')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('origin')->default('local');
            $table->string('animal_type')->default('cow');
            $table->unique(['name', 'farm_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->string('tag')->nullable();
            $table->string('name')->nullable();
            $table->string('sex')->default('unknown');
            $table->date('dob')->nullable();
            $table->unsignedBigInteger('breed_id')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('herd_id')->nullable();
            $table->string('status')->default('active');
            $table->decimal('current_weight_kg', 8, 2)->nullable();
            $table->string('color')->nullable();
            $table->date('acquired_at')->nullable();
            $table->text('attributes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('animal_type')->nullable();
            $table->decimal('purchase_price', 15, 2)->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unique(['farm_id', 'tag']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('breed_id')->references('id')->on('breeds')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('set null');
            $table->foreign('herd_id')->references('id')->on('herds')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });

        Schema::create('calves', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('mother_id');
            $table->unsignedBigInteger('father_id')->nullable();
            $table->string('tag_number');
            $table->string('gender');
            $table->date('birth_date');
            $table->decimal('birth_weight', 8, 2)->nullable();
            $table->string('health_status')->default('healthy');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['farm_id', 'tag_number']);
            $table->foreign('father_id')->references('id')->on('animals')->onDelete('set null');
            $table->foreign('mother_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('reproduction_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('animal_id');
            $table->string('event');
            $table->unsignedBigInteger('partner_id')->nullable();
            $table->date('event_date')->nullable();
            $table->string('outcome')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->string('heat_stage')->nullable();
            $table->unsignedBigInteger('performed_by')->nullable();
            $table->unsignedBigInteger('artificial_insemination_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index(['farm_id', 'animal_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('partner_id')->references('id')->on('animals')->onDelete('set null');
            $table->foreign('performed_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('artificial_inseminations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('reproduction_record_id');
            $table->string('semen_batch_no');
            $table->string('semen_company')->nullable();
            $table->date('insemination_date');
            $table->unsignedBigInteger('vet_id')->nullable();
            $table->decimal('cost', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('breed_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('reproduction_record_id')->references('id')->on('reproduction_records')->onDelete('cascade');
            $table->foreign('vet_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('breed_id')->references('id')->on('breeds')->onDelete('set null');
        });

        // Add AI FK to reproduction_records now that artificial_inseminations exists
        Schema::table('reproduction_records', function (Blueprint $table) {
            $table->foreign('artificial_insemination_id')->references('id')->on('artificial_inseminations')->onDelete('set null');
        });

        Schema::create('pregnancies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('animal_id');
            $table->unsignedBigInteger('reproduction_record_id');
            $table->date('pregnancy_confirmed_date');
            $table->integer('expected_gestation_days')->default(283);
            $table->date('expected_calving_date');
            $table->string('pregnancy_status')->default('ongoing');
            $table->text('health_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index(['farm_id', 'animal_id']);
            $table->foreign('reproduction_record_id')->references('id')->on('reproduction_records')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('calving_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('pregnancy_id');
            $table->date('calving_date');
            $table->string('calving_type');
            $table->integer('calves_count')->default(1);
            $table->string('calf_gender')->nullable();
            $table->string('calving_outcome');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('pregnancy_id')->references('id')->on('pregnancies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('pregnancy_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregnancy_id');
            $table->dateTime('checkup_date');
            $table->string('checkup_result');
            $table->text('observations')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('farm_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('checked_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('pregnancy_id')->references('id')->on('pregnancies')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });

        // ── Health Domain ──────────────────────────────────────────────────

        Schema::create('event_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['name', 'farm_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('position')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->date('hired_at')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('set null');
        });

        Schema::create('diseases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('farm_id');
            $table->string('name');
            $table->timestamps();
            $table->text('description')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });

        Schema::create('health_issues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('animal_id');
            $table->date('diagnosed_at')->nullable();
            $table->string('severity')->nullable();
            $table->text('symptoms')->nullable();
            $table->text('diagnosis')->nullable();
            $table->string('status')->default('active');
            $table->date('recovered_at')->nullable();
            $table->unsignedBigInteger('diagnosed_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('disease_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('diagnosed_by')->references('id')->on('staff_profiles')->onDelete('set null');
            $table->foreign('disease_id')->references('id')->on('diseases')->onDelete('set null');
        });

        Schema::create('health_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('animal_id');
            $table->unsignedBigInteger('event_type_id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->date('occurred_at')->nullable();
            $table->date('resolved_at')->nullable();
            $table->decimal('cost', 15, 2)->nullable();
            $table->unsignedBigInteger('treated_by')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('health_issue_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('vet_fee', 15, 2)->nullable();
            $table->decimal('lab_cost', 15, 2)->nullable();
            $table->decimal('other_cost', 15, 2)->nullable();
            $table->foreign('health_issue_id')->references('id')->on('health_issues')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('event_type_id')->references('id')->on('event_types')->onDelete('cascade');
            $table->foreign('treated_by')->references('id')->on('staff_profiles')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('instructions')->nullable();
            $table->unsignedBigInteger('farm_id');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('disease_treatments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('health_issue_id');
            $table->unsignedBigInteger('treatment_id');
            $table->unsignedBigInteger('farm_id');
            $table->text('description')->nullable();
            $table->string('medication')->nullable();
            $table->string('dosage')->nullable();
            $table->string('frequency')->nullable();
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->unsignedBigInteger('administered_by')->nullable();
            $table->string('status')->default('ongoing');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('health_event_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('health_event_id')->references('id')->on('health_events')->onDelete('cascade');
            $table->foreign('health_issue_id')->references('id')->on('health_issues')->onDelete('cascade');
            $table->foreign('treatment_id')->references('id')->on('treatments')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('administered_by')->references('id')->on('staff_profiles')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Vaccination Domain ─────────────────────────────────────────────

        Schema::create('vaccine_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('manufacturer')->nullable();
            $table->string('dose')->nullable();
            $table->integer('doses_per_animal')->nullable();
            $table->string('route')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('vaccination_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('animal_id');
            $table->date('administered_at')->nullable();
            $table->date('next_due_at')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('disease_id')->nullable();
            $table->foreign('disease_id')->references('id')->on('diseases')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff_profiles')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Inventory Domain ───────────────────────────────────────────────

        Schema::create('inventory_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['farm_id', 'name']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('category')->nullable();
            $table->decimal('quantity', 15, 4)->default(0);
            $table->string('unit')->default('unit');
            $table->decimal('min_quantity', 15, 4)->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('inventory_category_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['farm_id', 'sku']);
            $table->foreign('inventory_category_id')->references('id')->on('inventory_categories')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('medicine_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->string('name');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('medicine_group')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->integer('quantity')->default(0);
            $table->string('unit')->nullable();
            $table->integer('min_quantity')->default(0);
            $table->decimal('unit_cost', 15, 4)->default(0);
            $table->unsignedBigInteger('inventory_category_id')->nullable();
            $table->string('sku')->nullable();
            $table->unsignedBigInteger('medicine_group_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['farm_id', 'sku']);
            $table->foreign('medicine_group_id')->references('id')->on('medicine_groups')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('inventory_category_id')->references('id')->on('inventory_categories')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('treatment_id');
            $table->unsignedBigInteger('medicine_id');
            $table->string('dose');
            $table->string('frequency');
            $table->integer('duration_days');
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('treatment_id')->references('id')->on('treatments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('disease_treatment_medications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disease_treatment_id');
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('farm_id');
            $table->string('dose')->nullable();
            $table->string('frequency')->nullable();
            $table->integer('duration_days')->nullable();
            $table->string('status')->default('planned');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->integer('qty')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->decimal('total_cost', 15, 4)->nullable();
            $table->foreign('disease_treatment_id')->references('id')->on('disease_treatments')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('vaccination_medications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vaccination_record_id');
            $table->unsignedBigInteger('medicine_id');
            $table->decimal('quantity', 15, 4);
            $table->timestamps();
            $table->string('dose')->nullable();
            $table->foreign('vaccination_record_id')->references('id')->on('vaccination_records')->onDelete('cascade');
            $table->foreign('medicine_id')->references('id')->on('medicines')->onDelete('cascade');
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->string('movement_type');
            $table->string('source_event_type');
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('movement_date');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->index(['item_type', 'item_id']);
            $table->index(['source_type', 'source_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Feeding Domain ─────────────────────────────────────────────────

        Schema::create('feed_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('kg');
            $table->text('description')->nullable();
            $table->text('nutrient_info')->nullable();
            $table->timestamps();
            $table->string('category')->default('other');
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('feeding_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->dateTime('feeding_date');
            $table->string('feeding_time');
            $table->unsignedBigInteger('animal_id')->nullable();
            $table->unsignedBigInteger('group_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->decimal('cost', 15, 4)->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('set null');
            $table->foreign('group_id')->references('id')->on('herds')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('feeding_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('feeding_record_id');
            $table->unsignedBigInteger('item_id');
            $table->decimal('quantity', 15, 4);
            $table->timestamps();
            $table->foreign('feeding_record_id')->references('id')->on('feeding_records')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
        });

        // ── Production Domain ──────────────────────────────────────────────

        Schema::create('milk_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('animal_id');
            $table->date('date');
            $table->decimal('quantity_liters', 8, 2)->default(0);
            $table->string('milk_unit', 20)->default('liters');
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unique(['farm_id', 'animal_id', 'date']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('animal_id')->references('id')->on('animals')->onDelete('cascade');
            $table->foreign('staff_id')->references('id')->on('staff_profiles')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── Sales & Customer Domain ────────────────────────────────────────

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('type');
            $table->timestamps();
            $table->string('contact_person')->nullable();
            $table->text('notes')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('milk_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('sale_date');
            $table->decimal('quantity', 15, 4);
            $table->string('unit')->default('liters');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('total_price', 15, 4);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('invoice_number')->nullable();
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->string('status')->default('unpaid');
            $table->string('sale_transaction_source_type')->nullable();
            $table->unsignedBigInteger('sale_transaction_source_id')->nullable();
            $table->unique(['farm_id', 'invoice_number']);
            $table->index(['sale_transaction_source_type', 'sale_transaction_source_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->date('invoice_date');
            $table->decimal('total_amount', 15, 4)->default(0);
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->string('status')->default('unpaid');
            $table->timestamps();
            $table->string('invoice_number')->nullable();
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('sales_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('total_price', 15, 4);
            $table->timestamps();
            $table->string('item_type')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->index(['item_type', 'item_id']);
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
        });

        Schema::create('sale_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->date('transaction_date');
            $table->decimal('amount', 15, 4);
            $table->string('payment_method');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('sale_id')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('sale_transaction_source_type')->nullable();
            $table->unsignedBigInteger('sale_transaction_source_id')->nullable();
            $table->index(['sale_transaction_source_type', 'sale_transaction_source_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('sale_id')->references('id')->on('sales')->onDelete('set null');
        });

        // ── Purchasing Domain ──────────────────────────────────────────────

        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('invoice_number');
            $table->decimal('total_amount', 15, 4);
            $table->decimal('discount', 15, 4)->nullable();
            $table->string('discount_type')->nullable();
            $table->decimal('tax', 15, 4)->nullable();
            $table->decimal('tax_percentage', 15, 4)->nullable();
            $table->date('purchased_at');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('paid_amount', 15, 4)->default(0);
            $table->unique(['farm_id', 'invoice_number']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id');
            $table->string('item_type');
            $table->unsignedBigInteger('item_id');
            $table->float('quantity');
            $table->decimal('unit_price', 15, 4);
            $table->decimal('sub_total', 15, 4);
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('batch_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->index(['item_type', 'item_id']);
            $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('purchase_source_type')->nullable();
            $table->unsignedBigInteger('purchase_source_id')->nullable();
            $table->date('payment_date');
            $table->decimal('amount', 15, 4);
            $table->string('payment_method');
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->index(['purchase_source_type', 'purchase_source_id']);
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // ── Expenses & Logistics ───────────────────────────────────────────

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->decimal('amount', 15, 4);
            $table->date('incurred_on')->nullable();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('staff_id')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('staff_id')->references('id')->on('staff_profiles')->onDelete('set null');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('logistics', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('vehicle')->nullable();
            $table->string('driver')->nullable();
            $table->string('purpose')->nullable();
            $table->string('from_location')->nullable();
            $table->string('to_location')->nullable();
            $table->dateTime('departure_at')->nullable();
            $table->dateTime('arrival_at')->nullable();
            $table->integer('animals_count')->nullable();
            $table->text('animal_ids')->nullable();
            $table->decimal('cost', 15, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // ── HR Domain ─────────────────────────────────────────────────────

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->timestamps();
            $table->unique(['farm_id', 'name']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('level');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->unique(['name', 'farm_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_code');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('designation_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->date('date_of_birth')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->date('join_date');
            $table->string('employment_type');
            $table->string('salary_type');
            $table->string('status')->default('active');
            $table->string('user_email');
            $table->string('password');
            $table->timestamps();
            $table->decimal('bonus', 15, 4)->nullable();
            $table->decimal('festival_bonus', 15, 4)->nullable();
            $table->decimal('performance_incentive', 15, 4)->nullable();
            $table->decimal('tax_amount', 15, 4)->nullable();
            $table->decimal('loan_deduction', 15, 4)->nullable();
            $table->decimal('other_deductions', 15, 4)->nullable();
            $table->unsignedBigInteger('employee_user_id')->nullable();
            $table->unique(['farm_id', 'employee_code']);
            $table->unique(['farm_id', 'user_email']);
            $table->foreign('designation_id')->references('id')->on('designations')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('employee_user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('employee_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->string('document_type');
            $table->string('document_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('file_path');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('grace_minutes')->default(0);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('farm_id');
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });

        Schema::create('employee_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('shift_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('farm_id');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->integer('working_minutes')->nullable();
            $table->integer('overtime_minutes')->nullable();
            $table->string('status');
            $table->string('source');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('paid')->default(false);
            $table->integer('max_days_per_year')->default(0);
            $table->timestamps();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('leave_type_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('total_days');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('employees')->onDelete('set null');
        });

        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->decimal('basic_salary', 15, 4);
            $table->decimal('house_allowance', 15, 4)->default(0);
            $table->decimal('medical_allowance', 15, 4)->default(0);
            $table->decimal('transport_allowance', 15, 4)->default(0);
            $table->decimal('overtime_rate', 15, 4)->default(0);
            $table->date('effective_from');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->string('month');
            $table->integer('year');
            $table->string('status')->default('draft');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_run_id');
            $table->unsignedBigInteger('employee_id');
            $table->decimal('basic_salary', 15, 4)->default(0);
            $table->decimal('house_allowance', 15, 4)->default(0);
            $table->decimal('medical_allowance', 15, 4)->default(0);
            $table->decimal('transport_allowance', 15, 4)->default(0);
            $table->decimal('overtime_hours', 15, 4)->default(0);
            $table->decimal('overtime_rate', 15, 4)->default(0);
            $table->decimal('overtime_amount', 15, 4)->default(0);
            $table->decimal('gross_salary', 15, 4)->default(0);
            $table->decimal('deductions', 15, 4)->default(0);
            $table->decimal('net_salary', 15, 4)->default(0);
            $table->timestamps();
            $table->integer('working_days')->default(0);
            $table->integer('paid_leave_days')->default(0);
            $table->integer('unpaid_leave_days')->default(0);
            $table->decimal('leave_deduction', 15, 4)->default(0);
            $table->decimal('bonus', 15, 4)->default(0);
            $table->decimal('festival_bonus', 15, 4)->default(0);
            $table->decimal('performance_incentive', 15, 4)->default(0);
            $table->decimal('tax_amount', 15, 4)->default(0);
            $table->decimal('loan_deduction', 15, 4)->default(0);
            $table->decimal('other_deductions', 15, 4)->default(0);
            $table->foreign('payroll_run_id')->references('id')->on('payroll_runs')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });

        // ── Accounting Domain ──────────────────────────────────────────────

        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('code');
            $table->string('name');
            $table->string('type');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['farm_id', 'code']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id');
            $table->unsignedBigInteger('user_id');
            $table->date('entry_date');
            $table->string('reference_type');
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('journal_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_entry_id');
            $table->unsignedBigInteger('account_id');
            $table->decimal('debit_amount', 15, 4)->default(0);
            $table->decimal('credit_amount', 15, 4)->default(0);
            $table->text('narration')->nullable();
            $table->timestamps();
            $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('cascade');
        });

        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('name');
            $table->string('type');
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->decimal('opening_balance', 15, 4)->default(0);
            $table->decimal('current_balance', 15, 4)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('chart_of_accounts')->onDelete('set null');
        });

        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cash_account_id');
            $table->date('transaction_date');
            $table->decimal('amount', 15, 4);
            $table->string('direction');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('balance_after', 15, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['reference_type', 'reference_id']);
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('cash_account_id')->references('id')->on('cash_accounts')->onDelete('cascade');
        });

        Schema::create('fixed_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('farm_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->string('asset_type')->default('machinery');
            $table->decimal('purchase_value', 15, 4);
            $table->date('purchase_date');
            $table->integer('useful_life_years');
            $table->string('depreciation_method')->default('straight_line');
            $table->string('status')->default('active');
            $table->string('location')->nullable();
            $table->string('serial_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('farm_id')->references('id')->on('farms')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        $tables = [
            'fixed_assets', 'cash_transactions', 'cash_accounts', 'journal_entry_lines',
            'journal_entries', 'chart_of_accounts', 'payroll_items', 'payroll_runs',
            'salary_structures', 'leave_requests', 'leave_types', 'attendances',
            'employee_shifts', 'shifts', 'employee_documents', 'employees',
            'designations', 'departments', 'logistics', 'expenses',
            'supplier_payments', 'purchase_items', 'purchases',
            'sale_transactions', 'sales_items', 'sales', 'milk_sales',
            'customers', 'milk_records', 'feeding_items', 'feeding_records',
            'feed_types', 'stock_movements', 'vaccination_medications',
            'vaccination_records', 'disease_treatment_medications', 'medications',
            'medicines', 'medicine_groups', 'inventory_items', 'inventory_categories',
            'treatments', 'disease_treatments', 'health_issues', 'health_events',
            'diseases', 'vaccine_types', 'pregnancy_checkups', 'calving_records',
            'pregnancies', 'artificial_inseminations', 'reproduction_records',
            'calves', 'animals', 'herds', 'breeds', 'suppliers',
            'farm_notifications', 'subscription_payments', 'subscription_invoices',
            'farm_subscriptions', 'users', 'farms',
            'subscription_plan_features', 'subscription_features', 'subscription_plans',
            'payment_gateway_configs', 'demo_requests', 'settings',
            'role_has_permissions', 'model_has_roles', 'model_has_permissions',
            'role_user', 'roles', 'permissions',
            'fixed_assets', 'failed_jobs', 'job_batches', 'jobs',
            'cache_locks', 'cache', 'sessions', 'password_reset_tokens',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }
};
