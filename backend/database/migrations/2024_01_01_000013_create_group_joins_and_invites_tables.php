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
        // 1. group_join_requests
        Schema::create('group_join_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'CANCELLED'])->default('PENDING');
            $table->text('request_message')->nullable();
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('reviewed_at')->nullable();
            $table->string('rejection_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['group_id', 'user_id', 'status'], 'idx_join_requests_group_user_status');
            $table->index(['user_id', 'status'], 'idx_join_requests_user_status');
        });

        // 2. group_invites
        Schema::create('group_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('created_by_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('invite_code_hash', 64)->unique()->comment('SHA-256 hash of random invite token');
            $table->string('invite_code_prefix', 8)->comment('First 8 chars of token for admin preview/reference');
            $table->enum('invite_type', ['LINK', 'QR_CODE', 'DIRECT_CODE'])->default('LINK');
            $table->unsignedInteger('max_uses')->nullable()->comment('Null = unlimited usage');
            $table->unsignedInteger('used_count')->default(0);
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->foreignId('revoked_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['group_id', 'is_revoked', 'expires_at'], 'idx_invites_group_validity');
            $table->index('invite_code_prefix', 'idx_invites_prefix');
        });

        // 3. group_invite_redemptions
        Schema::create('group_invite_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invite_id')
                ->constrained('group_invites')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('redeemed_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['invite_id', 'user_id'], 'uq_invite_redemption_user');
            $table->index('user_id', 'idx_invite_redemptions_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_invite_redemptions');
        Schema::dropIfExists('group_invites');
        Schema::dropIfExists('group_join_requests');
    }
};
