<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campaigns and events (offers, Facebook/YouTube live sessions, contests).
 * Running ones appear in the homepage hero, the announcement bar and the
 * campaigns section, depending on their flags.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->json('summary')->nullable();
            $table->text('image_url')->nullable();
            $table->string('link_url', 500)->nullable();
            $table->json('button_label')->nullable();
            $table->string('channel', 20)->default('website');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('show_in_hero')->default(true);
            $table->boolean('show_in_bar')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
