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
        Schema::create('sport_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('Identifier code e.g. badminton, tennis');
            $table->string('name_th', 100)->comment('ชื่อภาษาไทย เช่น แบดมินตัน');
            $table->string('name_en', 100)->comment('ชื่อภาษาอังกฤษ เช่น Badminton');
            $table->string('icon', 100)->nullable()->comment('Icon name or SVG reference');
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'display_order'], 'idx_sport_categories_active_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_categories');
    }
};
