<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['ACTIVE', 'SUSPENDED', 'DELETED'])->default('ACTIVE');
            $table->dateTime('suspended_at')->nullable();
            $table->string('suspension_reason', 255)->nullable();
            $table->dateTime('last_active_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status', 'idx_users_status');
            $table->index('last_active_at', 'idx_users_last_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
