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
        // 1. group_rules
        Schema::create('group_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('title', 150);
            $table->text('description');
            $table->boolean('is_mandatory')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->index(['group_id', 'display_order'], 'idx_group_rules_order');
        });

        // 2. group_rule_acceptances
        Schema::create('group_rule_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('rule_id')
                ->constrained('group_rules')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('accepted_at');
            $table->timestamps();

            $table->unique(['group_id', 'user_id', 'rule_id'], 'uq_rule_acceptance_user');
            $table->index('user_id', 'idx_rule_acceptance_user');
        });

        // 3. group_custom_questions
        Schema::create('group_custom_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('question_text', 255);
            $table->enum('question_type', ['TEXT', 'RADIO', 'CHECKBOX'])->default('TEXT');
            $table->json('options')->nullable()->comment('Choices for RADIO/CHECKBOX');
            $table->boolean('is_required')->default(true);
            $table->unsignedSmallInteger('display_order')->default(0);
            $table->timestamps();

            $table->index(['group_id', 'display_order'], 'idx_custom_questions_order');
        });

        // 4. group_member_answers
        Schema::create('group_member_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                ->constrained('group_custom_questions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unsignedBigInteger('join_request_id')->nullable()->comment('Associated join request if applicable');
            $table->text('answer_text');
            $table->timestamps();

            $table->index(['question_id', 'user_id'], 'idx_member_answers_question_user');
            $table->index('join_request_id', 'idx_member_answers_join_req');
        });

        // 5. group_announcements
        Schema::create('group_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')
                ->constrained('badminton_groups')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('author_user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('title', 180);
            $table->text('content');
            $table->boolean('is_pinned')->default(false);
            $table->dateTime('published_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['group_id', 'is_pinned', 'published_at'], 'idx_announcements_group_pinned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_announcements');
        Schema::dropIfExists('group_member_answers');
        Schema::dropIfExists('group_custom_questions');
        Schema::dropIfExists('group_rule_acceptances');
        Schema::dropIfExists('group_rules');
    }
};
