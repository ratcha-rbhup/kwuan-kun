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
        // 1. group_creation_requests
        Schema::create('group_creation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requested_by_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('sport_category_id')
                ->constrained('sport_categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('proposed_name', 150);
            $table->text('proposed_description');
            $table->foreignId('province_id')
                ->constrained('provinces')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('district_id')
                ->constrained('districts')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('primary_venue_id')
                ->nullable()
                ->constrained('venues')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->string('target_skill_levels', 255)->nullable()->comment('เป้าหมายระดับฝีมือที่ระบุในคำขอ');
            $table->enum('join_policy', ['OPEN', 'APPROVAL_REQUIRED', 'INVITE_ONLY'])->default('APPROVAL_REQUIRED');
            $table->text('supporting_info')->nullable()->comment('ข้อมูลเพิ่มเติมหรือเหตุผลประกอบการขอเปิดก๊วน');
            $table->enum('status', ['PENDING', 'NEEDS_REVISION', 'APPROVED', 'REJECTED', 'CANCELLED'])->default('PENDING');
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->comment('Platform Admin ผู้พิจารณาคำขอ')
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('reviewer_notes')->nullable();
            $table->foreignId('created_group_id')
                ->nullable()
                ->comment('ก๊วนที่ถูกสร้างขึ้นจริงเมื่อคำขอได้รับอนุมัติ')
                ->constrained('badminton_groups')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();

            $table->index(['status', 'created_at'], 'idx_creation_req_status_date');
            $table->index('requested_by_user_id', 'idx_creation_req_user');
            $table->index('created_group_id', 'idx_creation_req_created_group');
        });

        // 2. group_creation_request_history
        Schema::create('group_creation_request_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')
                ->constrained('group_creation_requests')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('old_status', 30)->nullable();
            $table->string('new_status', 30);
            $table->text('comment')->nullable();
            $table->foreignId('action_by_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('created_at');

            $table->index(['request_id', 'created_at'], 'idx_req_history_request_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_creation_request_history');
        Schema::dropIfExists('group_creation_requests');
    }
};
