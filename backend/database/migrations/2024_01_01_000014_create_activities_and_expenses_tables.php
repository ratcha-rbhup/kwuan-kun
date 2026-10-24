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
        // 1. group_activities
        Schema::create('group_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('venue_id')
                ->nullable()
                ->constrained('venues')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('created_by_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->dateTime('start_datetime')->comment('UTC start time');
            $table->dateTime('end_datetime')->comment('UTC end time');
            $table->string('court_details', 100)->nullable()->comment('เช่น คอร์ต 1, 2 หรือ โซน VIP');
            $table->unsignedSmallInteger('max_participants')->default(8);
            $table->boolean('allow_waitlist')->default(true);
            $table->unsignedSmallInteger('max_waitlist')->default(10);
            $table->unsignedSmallInteger('confirmed_count')->default(0);
            $table->unsignedSmallInteger('waitlist_count')->default(0);
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PUBLISHED');
            $table->string('cancellation_reason', 255)->nullable();
            $table->decimal('estimated_cost_per_person', 10, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'start_datetime'], 'idx_activities_group_date');
            $table->index(['status', 'start_datetime'], 'idx_activities_status_date');
            $table->index('venue_id', 'idx_activities_venue');
        });

        // 2. activity_participants
        Schema::create('activity_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                ->constrained('group_activities')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['CONFIRMED', 'WAITLIST', 'CANCELLED', 'NO_SHOW', 'CHECKED_IN'])->default('CONFIRMED');
            $table->unsignedSmallInteger('queue_number')->default(1);
            $table->dateTime('registered_at');
            $table->dateTime('cancelled_at')->nullable();
            $table->string('cancellation_reason', 255)->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            // Prevent duplicate signup for the same activity
            $table->unique(['activity_id', 'user_id'], 'uq_activity_user_participant');
            $table->index(['activity_id', 'status', 'queue_number'], 'idx_act_part_status_queue');
            $table->index(['user_id', 'status'], 'idx_act_part_user_status');
        });

        // 3. activity_participant_history
        Schema::create('activity_participant_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')
                ->constrained('activity_participants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('activity_id')
                ->constrained('group_activities')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);
            $table->foreignId('changed_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('change_reason', 255)->nullable();
            $table->dateTime('created_at');

            $table->index(['activity_id', 'user_id', 'created_at'], 'idx_act_part_hist_lookup');
        });

        // 4. activity_expenses
        Schema::create('activity_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')
                ->constrained('group_activities')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('recorded_by_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('title', 150)->comment('เช่น ค่าคอร์ต 2 ชม., ค่าลูกแบด RSL 2 หลอด');
            $table->enum('category', ['COURT_FEE', 'SHUTTLECOCK', 'COACH_FEE', 'BEVERAGES', 'OTHER'])->default('COURT_FEE');
            $table->decimal('amount', 10, 2);
            $table->foreignId('receipt_media_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['activity_id', 'category'], 'idx_expenses_activity_cat');
        });

        // 5. activity_expense_shares
        Schema::create('activity_expense_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_id')
                ->constrained('activity_expenses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('activity_id')
                ->constrained('group_activities')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('amount_due', 10, 2);
            $table->enum('payment_status', ['PENDING', 'PAID', 'EXEMPT'])->default('PENDING');
            $table->dateTime('paid_at')->nullable();
            $table->enum('payment_method', ['PROMPT_PAY', 'CASH', 'TRANSFER', 'OTHER'])->nullable();
            $table->foreignId('proof_media_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->unique(['expense_id', 'user_id'], 'uq_expense_share_user');
            $table->index(['activity_id', 'payment_status'], 'idx_shares_activity_status');
            $table->index(['user_id', 'payment_status'], 'idx_shares_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_expense_shares');
        Schema::dropIfExists('activity_expenses');
        Schema::dropIfExists('activity_participant_history');
        Schema::dropIfExists('activity_participants');
        Schema::dropIfExists('group_activities');
    }
};
