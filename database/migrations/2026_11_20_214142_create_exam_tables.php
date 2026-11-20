<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Exam Arena: free MCQ practice for students (no login needed).
 *
 * questions          the question bank, tagged by subject, topic and class.
 * exams              ready-made exams set by staff (duration, question count,
 *                    optional result publish time) with their questions.
 * exam_attempts      one sitting: either a ready-made exam or a custom
 *                    practice (student picks subject/topics, count, minutes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('learning_topics')->restrictOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('learning_topics')->nullOnDelete();
            $table->unsignedTinyInteger('class_level')->nullable()->index();
            $table->text('body');
            $table->text('image_url')->nullable();
            $table->json('options');
            $table->unsignedTinyInteger('correct_option');
            $table->text('explanation')->nullable();
            $table->text('explanation_image_url')->nullable();
            $table->string('explanation_pdf_url', 500)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('title');
            $table->json('description')->nullable();
            $table->foreignId('subject_id')->nullable()->constrained('learning_topics')->nullOnDelete();
            $table->unsignedTinyInteger('class_level')->nullable();
            $table->unsignedSmallInteger('duration_minutes');
            $table->unsignedSmallInteger('question_count');
            $table->timestamp('results_at')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('exam_question', function (Blueprint $table) {
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->primary(['exam_id', 'question_id']);
        });

        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->ulid()->unique();
            $table->foreignId('exam_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('student_name', 100);
            $table->string('phone', 20)->nullable();
            $table->json('settings')->nullable();
            $table->json('question_ids');
            $table->json('answers')->nullable();
            $table->unsignedSmallInteger('duration_minutes');
            $table->dateTime('started_at');
            $table->dateTime('deadline_at');
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedSmallInteger('correct_count')->default(0);
            $table->unsignedSmallInteger('wrong_count')->default(0);
            $table->unsignedSmallInteger('skipped_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_question');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('questions');
    }
};
