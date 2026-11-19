<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sales & commerce: guardians/students, the central lead queue, free
 * assessments, course enrollments, book orders and semi-automated payments.
 *
 * Money is stored in whole taka. Phones are stored in E.164 (+8801…).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('name');
            $table->string('phone', 20)->unique();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('country_code', 2)->default('BD');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->foreignId('guardian_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedTinyInteger('class_level')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->string('school_name')->nullable();
            $table->string('assessed_level', 20)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('reference', 30)->unique();
            $table->string('type', 20)->index();
            $table->string('status', 20)->default('new')->index();
            $table->string('student_name')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('phone', 20)->index();
            $table->string('whatsapp', 20)->nullable();
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('class_level')->nullable();
            $table->unsignedTinyInteger('age')->nullable();
            $table->text('message')->nullable();
            $table->foreignId('guardian_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->string('lost_reason')->nullable();
            $table->dateTime('next_follow_up_at')->nullable()->index();
            $table->string('source', 20)->default('website');
            $table->string('landing_page')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('locale', 5)->default('bn');
            $table->timestamp('consent_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('lead_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::create('lead_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 30);
            $table->string('outcome', 30)->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('scheduled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('requested')->index();
            $table->dateTime('scheduled_at')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->string('meeting_url')->nullable();
            $table->string('result_level', 20)->nullable();
            $table->text('teacher_notes')->nullable();
            $table->foreignId('recommended_course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('method', 20);
            $table->string('account_type', 20)->default('personal');
            $table->string('account_name');
            $table->string('account_number', 50);
            $table->string('bank_name')->nullable();
            $table->string('branch')->nullable();
            $table->json('instructions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->json('name');
            $table->unsignedInteger('charge');
            $table->json('districts')->nullable();
            $table->string('estimated_days', 30)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('reference', 30)->unique();
            $table->foreignId('guardian_id')->constrained()->restrictOnDelete();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_batch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('course_variation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sold_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('pending_payment')->index();
            $table->unsignedInteger('list_price');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('payable_amount');
            $table->json('refund_policy')->nullable();
            $table->dateTime('first_class_at')->nullable();
            $table->dateTime('refund_eligible_until')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('reference', 30)->unique();
            $table->foreignId('guardian_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('phone', 20)->index();
            $table->string('alt_phone', 20)->nullable();
            $table->text('shipping_address');
            $table->string('district', 50);
            $table->string('upazila', 50)->nullable();
            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('pending_verification')->index();
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('discount_amount')->default(0);
            $table->unsignedInteger('delivery_charge')->default(0);
            $table->unsignedInteger('total');
            $table->string('courier', 30)->nullable();
            $table->string('tracking_code', 60)->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('staff_notes')->nullable();
            $table->string('locale', 5)->default('bn');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title_snapshot');
            $table->unsignedInteger('unit_price');
            $table->unsignedSmallInteger('quantity');
            $table->unsignedInteger('line_total');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->string('reference', 30)->unique();
            $table->morphs('payable');
            $table->string('method', 20);
            $table->string('provider', 20)->default('manual');
            $table->unsignedInteger('amount');
            $table->string('sender_number', 20)->nullable();
            $table->string('transaction_id', 50)->nullable();
            $table->text('receipt_url')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->string('submitted_by', 20)->default('customer');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // The same mobile-banking transaction can only be claimed once.
            $table->unique(['method', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('delivery_zones');
        Schema::dropIfExists('payment_accounts');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('lead_interests');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('students');
        Schema::dropIfExists('guardians');
    }
};
