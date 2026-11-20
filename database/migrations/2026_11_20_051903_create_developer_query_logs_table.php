<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Audit trail of every statement run from the developer SQL console.
     */
    public function up(): void
    {
        Schema::create('developer_query_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->text('statement');
            $table->boolean('is_write')->default(false);
            $table->integer('affected_rows')->nullable();
            $table->unsignedInteger('duration_ms')->default(0);
            $table->text('error')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('developer_query_logs');
    }
};
