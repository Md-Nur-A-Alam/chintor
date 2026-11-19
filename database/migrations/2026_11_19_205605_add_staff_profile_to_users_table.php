<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Staff sign in with a phone number, so phone becomes the main identity
     * and email becomes optional. Students, guardians and teachers will reuse
     * this table through the "type" column when the LMS arrives.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();

            $table->string('phone', 20)->nullable()->unique()->after('name');
            $table->string('type', 20)->default('staff')->after('phone');
            $table->string('office_id', 20)->nullable()->unique()->after('type');
            $table->string('designation')->nullable()->after('office_id');
            $table->date('joined_on')->nullable()->after('designation');
            $table->date('date_of_birth')->nullable()->after('joined_on');
            $table->boolean('is_active')->default(true)->after('date_of_birth');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropUnique(['office_id']);
            $table->dropColumn([
                'phone',
                'type',
                'office_id',
                'designation',
                'joined_on',
                'date_of_birth',
                'is_active',
                'last_login_at',
            ]);

            $table->string('email')->nullable(false)->change();
        });
    }
};
