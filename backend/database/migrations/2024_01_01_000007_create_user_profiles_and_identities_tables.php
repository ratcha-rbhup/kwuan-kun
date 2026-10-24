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
        // 1. user_profiles
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique('uq_user_profile_user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('display_name', 100);
            $table->foreignId('avatar_media_id')
                ->nullable()
                ->constrained('media_assets')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->text('bio')->nullable();
            $table->foreignId('badminton_skill_level_id')
                ->nullable()
                ->constrained('badminton_skill_levels')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('preferred_province_id')
                ->nullable()
                ->constrained('provinces')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('preferred_district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->enum('gender', ['MALE', 'FEMALE', 'OTHER', 'PREFER_NOT_TO_SAY'])->default('PREFER_NOT_TO_SAY');
            $table->enum('dominant_hand', ['RIGHT', 'LEFT', 'BOTH'])->default('RIGHT');
            $table->enum('play_style', ['SINGLES', 'DOUBLES', 'MIXED', 'ALL'])->default('ALL');
            $table->unsignedTinyInteger('experience_years')->nullable();
            $table->timestamps();

            $table->index('badminton_skill_level_id', 'idx_user_profiles_skill_level');
            $table->index(['preferred_province_id', 'preferred_district_id'], 'idx_user_profiles_location');
        });

        // 2. user_identities (LINE, Google, Apple etc.)
        Schema::create('user_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('provider', 50)->comment('Identity provider: line, google, etc.');
            $table->string('provider_subject', 191)->comment('Subject/User ID from Identity Provider ID Token');
            $table->string('email', 191)->nullable();
            $table->json('id_token_claims')->nullable()->comment('Sanitized token claims (aud, iss, exp, name, picture)');
            $table->dateTime('registered_at');
            $table->dateTime('last_login_at');
            $table->timestamps();

            $table->unique(['provider', 'provider_subject'], 'uq_user_identities_provider_subject');
            $table->index('user_id', 'idx_user_identities_user_id');
        });

        // 3. user_consents
        Schema::create('user_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('policy_type', ['TERMS_OF_SERVICE', 'PRIVACY_POLICY', 'PDPA_CONSENT', 'MARKETING_COMMUNICATION']);
            $table->string('version', 20)->comment('Policy version e.g. 1.0.0');
            $table->boolean('is_agreed')->default(true);
            $table->dateTime('agreed_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'policy_type', 'version'], 'idx_user_consents_lookup');
        });

        // 4. user_phone_verifications
        Schema::create('user_phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('phone_number', 20)->comment('E.164 phone number');
            $table->string('otp_hash', 255)->comment('Bcrypt or SHA256 hashed OTP');
            $table->enum('status', ['PENDING', 'VERIFIED', 'EXPIRED', 'FAILED'])->default('PENDING');
            $table->unsignedTinyInteger('attempts_count')->default(0);
            $table->dateTime('expires_at');
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status'], 'idx_phone_verif_user_status');
            $table->index('phone_number', 'idx_phone_verif_number');
        });

        // 5. user_notification_preferences
        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->unique('uq_notif_pref_user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('in_app_enabled')->default(true);
            $table->boolean('line_notify_enabled')->default(true);
            $table->boolean('email_enabled')->default(false);
            $table->boolean('activity_reminders')->default(true);
            $table->boolean('group_announcements')->default(true);
            $table->boolean('join_request_updates')->default(true);
            $table->boolean('marketing_enabled')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
        Schema::dropIfExists('user_phone_verifications');
        Schema::dropIfExists('user_consents');
        Schema::dropIfExists('user_identities');
        Schema::dropIfExists('user_profiles');
    }
};
