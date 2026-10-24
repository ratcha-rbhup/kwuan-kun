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
        Schema::create('badminton_skill_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_category_id')
                ->constrained('sport_categories')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('code', 50)->comment('e.g. BEGINNER_N, INTERMEDIATE_P, ADVANCED_B');
            $table->string('name_th', 100)->comment('ชื่อระดับไทย เช่น มือใหม่ / N, ทั่วไป / P');
            $table->string('name_en', 100)->comment('ชื่อระดับอังกฤษ เช่น Beginner / N, Intermediate / P');
            $table->unsignedTinyInteger('level_score')->comment('ระดับคะแนน 1-10 สำหรับค้นหาช่วงระดับฝีมือ');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->unique(['sport_category_id', 'code'], 'uq_skill_sport_code');
            $table->index(['sport_category_id', 'level_score'], 'idx_skill_sport_score');
            $table->index(['is_active', 'display_order'], 'idx_skill_active_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badminton_skill_levels');
    }
};
