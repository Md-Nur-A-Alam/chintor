<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catalog: programs → courses (with pricing variations and live batches),
 * teachers, books and authors.
 *
 * Translatable columns are JSON shaped {"bn": "…", "en": "…"}.
 * Image columns hold a URL from the media library (local, ImgBB or R2).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('tagline')->nullable();
            $table->json('description')->nullable();
            $table->string('icon', 30)->nullable();
            $table->string('accent', 20)->default('teal');
            $table->text('image_url')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('designation')->nullable();
            $table->text('photo_url')->nullable();
            $table->json('summary')->nullable();
            $table->string('details_mode', 10)->default('rich');
            $table->json('details_rich')->nullable();
            $table->longText('details_html')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('summary')->nullable();
            $table->json('description')->nullable();
            $table->unsignedTinyInteger('class_from')->nullable();
            $table->unsignedTinyInteger('class_to')->nullable();
            $table->unsignedTinyInteger('age_from')->nullable();
            $table->unsignedTinyInteger('age_to')->nullable();
            $table->unsignedSmallInteger('live_class_count')->nullable();
            $table->unsignedSmallInteger('class_duration_minutes')->nullable();
            $table->unsignedTinyInteger('duration_months')->nullable();
            $table->json('learning_outcomes')->nullable();
            $table->json('highlights')->nullable();
            $table->json('syllabus')->nullable();
            $table->string('explainer_video_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->json('refund_policy')->nullable();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('course_teacher', function (Blueprint $table) {
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->primary(['course_id', 'teacher_id']);
        });

        Schema::create('course_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->json('label');
            $table->unsignedTinyInteger('duration_months')->nullable();
            $table->string('billing_type', 20)->default('one_time');
            $table->unsignedInteger('regular_price');
            $table->unsignedInteger('discounted_price')->nullable();
            $table->json('badge')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('course_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30)->unique();
            $table->json('name')->nullable();
            $table->json('class_days')->nullable();
            $table->time('class_start_time')->nullable();
            $table->time('class_end_time')->nullable();
            $table->dateTime('orientation_at')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->date('enrollment_deadline')->nullable();
            $table->unsignedSmallInteger('seat_limit')->nullable();
            $table->unsignedSmallInteger('seats_taken')->default(0);
            $table->string('status', 20)->default('upcoming')->index();
            $table->string('meeting_url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('bio')->nullable();
            $table->text('photo_url')->nullable();
            $table->foreignId('teacher_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('isbn', 20)->nullable();
            $table->foreignId('program_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->json('description')->nullable();
            $table->json('table_of_contents')->nullable();
            $table->unsignedTinyInteger('class_from')->nullable();
            $table->unsignedTinyInteger('class_to')->nullable();
            $table->text('cover_url')->nullable();
            $table->json('sample_pages')->nullable();
            $table->unsignedSmallInteger('pages')->nullable();
            $table->string('edition', 50)->nullable();
            $table->unsignedSmallInteger('published_year')->nullable();
            $table->string('language', 5)->default('bn');
            $table->unsignedInteger('regular_price');
            $table->unsignedInteger('discounted_price')->nullable();
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('author_book', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained()->cascadeOnDelete();
            $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->primary(['author_id', 'book_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('author_book');
        Schema::dropIfExists('books');
        Schema::dropIfExists('authors');
        Schema::dropIfExists('course_batches');
        Schema::dropIfExists('course_variations');
        Schema::dropIfExists('course_teacher');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('teachers');
        Schema::dropIfExists('programs');
    }
};
