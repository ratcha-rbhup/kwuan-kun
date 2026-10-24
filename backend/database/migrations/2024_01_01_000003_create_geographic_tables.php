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
        // 1. provinces (จังหวัด)
        Schema::create('provinces', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('Official DOPA code e.g. 10 for Bangkok');
            $table->string('name_th', 100)->comment('ชื่อจังหวัดภาษาไทย');
            $table->string('name_en', 100)->comment('ชื่อจังหวัดภาษาอังกฤษ');
            $table->string('geography', 50)->nullable()->comment('ภาค เช่น ภาคกลาง, ภาคเหนือ');
            $table->timestamps();

            $table->index('name_th', 'idx_provinces_name_th');
            $table->index('name_en', 'idx_provinces_name_en');
        });

        // 2. districts (อำเภอ / เขต)
        Schema::create('districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('province_id')
                ->constrained('provinces')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('code', 10)->unique()->comment('Official DOPA district code e.g. 1001');
            $table->string('name_th', 100)->comment('ชื่ออำเภอ/เขตภาษาไทย');
            $table->string('name_en', 100)->comment('ชื่ออำเภอ/เขตภาษาอังกฤษ');
            $table->timestamps();

            $table->index('province_id', 'idx_districts_province_id');
            $table->index(['province_id', 'name_th'], 'idx_districts_prov_name_th');
        });

        // 3. subdistricts (ตำบล / แขวง)
        Schema::create('subdistricts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')
                ->constrained('districts')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('code', 10)->unique()->comment('Official DOPA subdistrict code e.g. 100101');
            $table->string('name_th', 100)->comment('ชื่อตำบล/แขวงภาษาไทย');
            $table->string('name_en', 100)->comment('ชื่อตำบล/แขวงภาษาอังกฤษ');
            $table->string('zip_code', 10)->comment('รหัสไปรษณีย์');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->index('district_id', 'idx_subdistricts_district_id');
            $table->index('zip_code', 'idx_subdistricts_zip_code');
            $table->index(['district_id', 'name_th'], 'idx_subdistricts_dist_name_th');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subdistricts');
        Schema::dropIfExists('districts');
        Schema::dropIfExists('provinces');
    }
};
