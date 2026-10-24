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
        // 1. platform_roles
        Schema::create('platform_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('Display name e.g. Platform Administrator');
            $table->string('slug', 50)->unique()->comment('Slug identifier e.g. platform_admin');
            $table->string('description', 255)->nullable();
            $table->boolean('is_system')->default(true);
            $table->timestamps();
        });

        // 2. user_platform_roles
        Schema::create('user_platform_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('role_id')
                ->constrained('platform_roles')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('assigned_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('assigned_at');
            $table->timestamps();

            $table->unique(['user_id', 'role_id'], 'uq_user_platform_role');
            $table->index('role_id', 'idx_user_platform_roles_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_platform_roles');
        Schema::dropIfExists('platform_roles');
    }
};
