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
        // 1. venues (สนาม)
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_category_id')
                ->constrained('sport_categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('name', 150)->comment('ชื่อสนามแบดมินตัน');
            $table->string('branch_name', 100)->nullable()->comment('สาขา');
            $table->foreignId('province_id')
                ->constrained('provinces')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('district_id')
                ->constrained('districts')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('subdistrict_id')
                ->nullable()
                ->constrained('subdistricts')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->text('address_detail')->comment('ที่อยู่โดยละเอียด');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone_number', 30)->nullable();
            $table->string('line_oa_id', 50)->nullable()->comment('LINE Official Account ID');
            $table->json('facilities')->nullable()->comment('สิ่งอำนวยความสะดวก: แอร์, ที่จอดรถ, ห้องอาบน้ำ ฯลฯ');
            $table->unsignedSmallInteger('total_courts')->default(1);
            $table->boolean('is_verified')->default(false);
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['province_id', 'district_id'], 'idx_venues_province_district');
            $table->index(['sport_category_id', 'status'], 'idx_venues_sport_status');
            $table->index(['latitude', 'longitude'], 'idx_venues_geo');
        });

        // 2. venue_courts (คอร์ตในสนาม)
        Schema::create('venue_courts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')
                ->constrained('venues')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('court_number_or_name', 50)->comment('ชื่อหรือหมายเลขคอร์ต เช่น คอร์ต 1');
            $table->string('floor_type', 50)->default('RUBBER_MAT')->comment('ประเภทพื้น: ยาง, ปาร์เก้ ฯลฯ');
            $table->boolean('is_active')->default(true);
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['venue_id', 'court_number_or_name'], 'uq_venue_court_name');
            $table->index(['venue_id', 'is_active'], 'idx_venue_courts_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venue_courts');
        Schema::dropIfExists('venues');
    }
};
