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
        // 1. Add pricing and session rules to group_activities
        Schema::table('group_activities', function (Blueprint $table) {
            $table->enum('walk_in_policy', ['NOT_ALLOWED', 'ALLOWED', 'ALLOWED_IF_VACANT'])
                ->default('NOT_ALLOWED')
                ->after('court_details')
                ->comment('นโยบายการรับ Walk-in: NOT_ALLOWED, ALLOWED, ALLOWED_IF_VACANT');

            $table->unsignedSmallInteger('points_per_set')
                ->default(21)
                ->after('walk_in_policy')
                ->comment('คะแนนต่อเซต เช่น 21, 15, 30');

            $table->string('shuttlecock_brand', 100)
                ->nullable()
                ->after('points_per_set')
                ->comment('ยี่ห้อและรุ่นลูกแบด เช่น RSL Silver Speed 76, Victor Champion');

            $table->string('rotation_system', 100)
                ->nullable()
                ->after('shuttlecock_brand')
                ->comment('ระบบจัดคู่ เช่น กระดานจัดคู่หมุนเวียนเสมอภาค, ท้าดวล');

            $table->enum('fee_model', ['DIVIDED_EQUAL', 'FLAT_RATE', 'SPLIT_COURT_SHUTTLE', 'PER_GAME'])
                ->default('DIVIDED_EQUAL')
                ->after('estimated_cost_per_person')
                ->comment('โมเดลค่าใช้จ่าย: หารเท่า, เหมาจ่ายรายหัว, แยกค่าสนาม-ค่าลูก, คิดต่อเกม');

            $table->decimal('court_entry_fee', 10, 2)
                ->nullable()
                ->after('fee_model')
                ->comment('ค่าเหยียบสนาม หรือ ค่าคอร์ตเริ่มต้น');

            $table->decimal('shuttlecock_fee', 10, 2)
                ->nullable()
                ->after('court_entry_fee')
                ->comment('ค่าลูกแบดมินตันต่อลูก หรือต่อคน');

            $table->enum('shuttlecock_fee_type', ['INCLUDED', 'PER_SHUTTLE', 'DIVIDED'])
                ->default('INCLUDED')
                ->after('shuttlecock_fee')
                ->comment('รูปแบบคิดค่าลูก: รวมแล้ว, คิดต่อลูก, หารเท่า');

            $table->decimal('game_fee', 10, 2)
                ->nullable()
                ->after('shuttlecock_fee_type')
                ->comment('ค่าลงเล่นต่อเกม (เช่น เกมละ 15 บาท)');

            $table->decimal('knock_fee', 10, 2)
                ->nullable()
                ->after('game_fee')
                ->comment('ค่าน็อค / วอร์มคอร์ด (เช่น ฟรี หรือ 20 บาท)');

            $table->decimal('discount_female_fee', 10, 2)
                ->nullable()
                ->after('knock_fee')
                ->comment('ส่วนลดสุภาพสตรี / มือใหม่');

            $table->text('pricing_notes')
                ->nullable()
                ->after('discount_female_fee')
                ->comment('หมายเหตุค่าใช้จ่ายและการชำระเงิน (PromptPay, เงินสด)');
        });

        // 2. Add default pricing and session templates to badminton_groups
        Schema::table('badminton_groups', function (Blueprint $table) {
            $table->enum('default_walk_in_policy', ['NOT_ALLOWED', 'ALLOWED', 'ALLOWED_IF_VACANT'])
                ->default('NOT_ALLOWED')
                ->after('join_policy');

            $table->unsignedSmallInteger('default_points_per_set')
                ->default(21)
                ->after('default_walk_in_policy');

            $table->string('default_shuttlecock_brand', 100)
                ->nullable()
                ->after('default_points_per_set');

            $table->string('default_rotation_system', 100)
                ->nullable()
                ->after('default_shuttlecock_brand');

            $table->enum('default_fee_model', ['DIVIDED_EQUAL', 'FLAT_RATE', 'SPLIT_COURT_SHUTTLE', 'PER_GAME'])
                ->default('DIVIDED_EQUAL')
                ->after('default_rotation_system');

            $table->decimal('default_court_entry_fee', 10, 2)
                ->nullable()
                ->after('default_fee_model');

            $table->decimal('default_shuttlecock_fee', 10, 2)
                ->nullable()
                ->after('default_court_entry_fee');

            $table->decimal('default_game_fee', 10, 2)
                ->nullable()
                ->after('default_shuttlecock_fee');

            $table->decimal('default_knock_fee', 10, 2)
                ->nullable()
                ->after('default_game_fee');

            $table->text('default_pricing_notes')
                ->nullable()
                ->after('default_knock_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('group_activities', function (Blueprint $table) {
            $table->dropColumn([
                'walk_in_policy',
                'points_per_set',
                'shuttlecock_brand',
                'rotation_system',
                'fee_model',
                'court_entry_fee',
                'shuttlecock_fee',
                'shuttlecock_fee_type',
                'game_fee',
                'knock_fee',
                'discount_female_fee',
                'pricing_notes'
            ]);
        });

        Schema::table('badminton_groups', function (Blueprint $table) {
            $table->dropColumn([
                'default_walk_in_policy',
                'default_points_per_set',
                'default_shuttlecock_brand',
                'default_rotation_system',
                'default_fee_model',
                'default_court_entry_fee',
                'default_shuttlecock_fee',
                'default_game_fee',
                'default_knock_fee',
                'default_pricing_notes'
            ]);
        });
    }
};
