<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Free learning content.
 *
 * learning_topics  a two-level list shared by videos and exam questions:
 *                  subjects (parent_id null, e.g. Maths) and their topics
 *                  (e.g. Geometry), so filters always match.
 * learning_videos  short YouTube explainers, filterable by subject/topic;
 *                  up to six can be featured on the homepage.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('learning_topics')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->json('name');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('learning_videos', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('youtube_url');
            $table->string('orientation', 12)->default('vertical');
            $table->foreignId('subject_id')->constrained('learning_topics')->restrictOnDelete();
            $table->foreignId('topic_id')->nullable()->constrained('learning_topics')->nullOnDelete();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_videos');
        Schema::dropIfExists('learning_topics');
    }
};
