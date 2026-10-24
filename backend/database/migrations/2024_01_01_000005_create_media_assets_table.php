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
        Schema::create('media_assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uploaded_by_user_id')->nullable()->comment('User who uploaded the asset');
            $table->string('disk', 30)->default('public')->comment('Storage disk: local, public, s3');
            $table->string('file_path', 255)->comment('Relative path to storage');
            $table->string('file_name', 255)->comment('Original client file name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('file_size_bytes');
            $table->enum('media_category', [
                'AVATAR',
                'GROUP_LOGO',
                'GROUP_COVER',
                'PAYMENT_SLIP',
                'REPORT_EVIDENCE',
                'ANNOUNCEMENT_IMAGE',
                'GENERAL'
            ])->default('GENERAL');
            $table->boolean('is_private')->default(false)->comment('If true, requires signed URL to view');
            $table->timestamps();
            $table->softDeletes();

            $table->index('uploaded_by_user_id', 'idx_media_uploaded_by');
            $table->index(['media_category', 'created_at'], 'idx_media_category_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_assets');
    }
};
