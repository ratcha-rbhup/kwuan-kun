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
        // 1. Add foreign key to media_assets (uploaded_by_user_id)
        Schema::table('media_assets', function (Blueprint $table) {
            $table->foreign('uploaded_by_user_id', 'fk_media_assets_uploader')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });

        // 2. Add foreign key to group_member_answers (join_request_id)
        Schema::table('group_member_answers', function (Blueprint $table) {
            $table->foreign('join_request_id', 'fk_member_answers_join_req')
                ->references('id')
                ->on('group_join_requests')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_member_answers', function (Blueprint $table) {
            $table->dropForeign('fk_member_answers_join_req');
        });

        Schema::table('media_assets', function (Blueprint $table) {
            $table->dropForeign('fk_media_assets_uploader');
        });
    }
};
