<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. group_members
        Schema::create('group_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('role', ['OWNER', 'ADMIN', 'MODERATOR', 'MEMBER'])
                ->default('MEMBER');
            $table->enum('status', ['ACTIVE', 'SUSPENDED', 'LEFT', 'REMOVED'])
                ->default('ACTIVE');
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->string('leave_reason', 255)->nullable();
            $table->foreignId('invited_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            // Unique constraint prevents duplicate membership record for the same user in the group
            $table->unique(['group_id', 'user_id'], 'uq_group_members_group_user');
            $table->index(['user_id', 'status'], 'idx_group_members_user_status');
            $table->index(['group_id', 'role', 'status'], 'idx_group_members_group_role_status');
        });

        // Add virtual generated column and unique index to enforce exactly one active OWNER per group at DB level
        DB::statement("ALTER TABLE `group_members` ADD `is_active_owner` TINYINT GENERATED ALWAYS AS (CASE WHEN `role` = 'OWNER' AND `status` = 'ACTIVE' THEN 1 ELSE NULL END) VIRTUAL");
        DB::statement("ALTER TABLE `group_members` ADD UNIQUE KEY `uq_group_single_active_owner` (`group_id`, `is_active_owner`)");

        // 2. group_member_history
        Schema::create('group_member_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_member_id')
                ->constrained('group_members')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('action', [
                'JOINED',
                'ROLE_CHANGED',
                'SUSPENDED',
                'UNSUSPENDED',
                'LEFT',
                'REMOVED',
                'REJOINED',
                'OWNERSHIP_TRANSFERRED'
            ]);
            $table->string('old_role', 20)->nullable();
            $table->string('new_role', 20)->nullable();
            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20)->nullable();
            $table->string('reason', 255)->nullable();
            $table->foreignId('performed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('created_at');

            $table->index(['group_id', 'user_id', 'created_at'], 'idx_member_history_lookup');
            $table->index('group_member_id', 'idx_member_history_member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_member_history');
        Schema::dropIfExists('group_members');
    }
};
