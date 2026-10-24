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
        Schema::create('badminton_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_category_id')
                ->constrained('sport_categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('name', 150)->comment('ชื่อก๊วน');
            $table->string('slug', 180)->unique()->comment('Slug สำหรับ URL');
            $table->text('description')->comment('รายละเอียดก๊วน');
            $table->foreignId('logo_media_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('cover_media_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
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
            $table->foreignId('min_skill_level_id')
                ->nullable()
                ->constrained('badminton_skill_levels')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('max_skill_level_id')
                ->nullable()
                ->constrained('badminton_skill_levels')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('join_policy', ['OPEN', 'APPROVAL_REQUIRED', 'INVITE_ONLY', 'CLOSED'])
                ->default('APPROVAL_REQUIRED')
                ->comment('นโยบายการเข้าร่วมก๊วน');
            $table->unsignedSmallInteger('max_members')->nullable()->comment('จำนวนสมาชิกสูงสุด (null คือไม่จำกัด)');
            $table->unsignedSmallInteger('current_member_count')->default(0)->comment('Denormalized active count for performance');
            $table->enum('status', ['ACTIVE', 'SUSPENDED', 'CLOSED'])->default('ACTIVE');
            $table->foreignId('created_by_user_id')
                ->comment('ผู้สร้างรายการก๊วน (แยกจากบทบาท OWNER ใน group_members)')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['province_id', 'district_id'], 'idx_badminton_groups_location');
            $table->index(['sport_category_id', 'status'], 'idx_badminton_groups_sport_status');
            $table->index(['status', 'join_policy'], 'idx_badminton_groups_status_policy');
            $table->index('primary_venue_id', 'idx_badminton_groups_venue');
            $table->index('created_by_user_id', 'idx_badminton_groups_creator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badminton_groups');
    }
};
