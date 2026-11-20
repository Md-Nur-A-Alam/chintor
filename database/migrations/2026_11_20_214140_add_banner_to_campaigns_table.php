<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A campaign can take over the whole homepage hero with a wide background
 * banner (1920×900, optional 1080×1350 phone version), with its text placed
 * left, centre or right over a black-to-transparent overlay.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->text('banner_url')->nullable()->after('image_url');
            $table->text('banner_mobile_url')->nullable()->after('banner_url');
            $table->string('banner_align', 10)->default('left')->after('banner_mobile_url');
            $table->unsignedTinyInteger('banner_overlay')->default(55)->after('banner_align');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn(['banner_url', 'banner_mobile_url', 'banner_align', 'banner_overlay']);
        });
    }
};
