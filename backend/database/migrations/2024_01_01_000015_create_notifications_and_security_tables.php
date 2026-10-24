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
        // 1. notifications (Laravel standard UUID notification schema)
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 191);
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'idx_notifications_notifiable_read');
        });

        // 2. notification_deliveries (Multi-channel delivery tracker)
        Schema::create('notification_deliveries', function (Blueprint $table) {
            $table->id();
            $table->uuid('notification_id');
            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('channel', ['IN_APP', 'LINE_MESSAGING_API', 'LINE_NOTIFY', 'EMAIL', 'WEB_PUSH']);
            $table->string('recipient_address', 191)->comment('LINE User ID, Email, Push token etc.');
            $table->enum('status', ['PENDING', 'SENT', 'FAILED', 'BOUNCED'])->default('PENDING');
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'channel'], 'idx_deliveries_status_channel');
            $table->index('notification_id', 'idx_deliveries_notification_id');
        });

        // 3. reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('reportable_type', 100)->comment('Class or table name being reported');
            $table->unsignedBigInteger('reportable_id')->comment('ID of target entity');
            $table->enum('category', ['HARASSMENT', 'SPAM', 'NO_SHOW', 'INAPPROPRIATE_CONTENT', 'FRAUD', 'OTHER']);
            $table->text('reason');
            $table->foreignId('evidence_media_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['PENDING', 'INVESTIGATING', 'RESOLVED', 'DISMISSED'])->default('PENDING');
            $table->string('action_taken', 255)->nullable();
            $table->foreignId('resolved_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'idx_reports_status_date');
            $table->index(['reportable_type', 'reportable_id'], 'idx_reports_target');
            $table->index('reporter_user_id', 'idx_reports_reporter');
        });

        // 4. moderation_actions
        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('report_id')
                ->nullable()
                ->constrained('reports')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('target_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('target_group_id')
                ->nullable()
                ->constrained('badminton_groups')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('action_type', ['WARNING', 'SUSPEND_USER', 'BAN_USER', 'SUSPEND_GROUP', 'CLOSE_GROUP', 'REMOVE_CONTENT']);
            $table->text('reason');
            $table->unsignedInteger('duration_days')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['target_user_id', 'is_active'], 'idx_mod_user_active');
            $table->index(['target_group_id', 'is_active'], 'idx_mod_group_active');
        });

        // 5. audit_logs
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('event', 100)->comment('e.g. group.approved, member.role_changed');
            $table->string('auditable_type', 100);
            $table->unsignedBigInteger('auditable_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->dateTime('created_at');

            $table->index(['event', 'created_at'], 'idx_audit_logs_event_date');
            $table->index(['auditable_type', 'auditable_id'], 'idx_audit_logs_target');
            $table->index(['user_id', 'created_at'], 'idx_audit_logs_user_date');
        });

        // 6. system_settings
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('setting_key', 100)->unique();
            $table->text('setting_value')->nullable();
            $table->enum('data_type', ['STRING', 'INTEGER', 'BOOLEAN', 'JSON'])->default('STRING');
            $table->string('description', 255)->nullable();
            $table->boolean('is_public')->default(false)->comment('If true, exposed to frontend config API');
            $table->foreignId('updated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->index('is_public', 'idx_system_settings_public');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('notification_deliveries');
        Schema::dropIfExists('notifications');
    }
};
