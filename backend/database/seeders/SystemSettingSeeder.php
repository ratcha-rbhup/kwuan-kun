<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'app.platform_name',
                'setting_value' => 'KwuanKun',
                'data_type' => 'STRING',
                'description' => 'ชื่อระบบทางการของแพลตฟอร์ม',
                'is_public' => true,
            ],
            [
                'setting_key' => 'app.default_sport',
                'setting_value' => 'badminton',
                'data_type' => 'STRING',
                'description' => 'ประเภทกีฬาเริ่มต้นของระบบ (badminton)',
                'is_public' => true,
            ],
            [
                'setting_key' => 'app.default_timezone',
                'setting_value' => 'Asia/Bangkok',
                'data_type' => 'STRING',
                'description' => 'เขตเวลาสำหรับแสดงผลต่อผู้ใช้ปลายทาง',
                'is_public' => true,
            ],
            [
                'setting_key' => 'app.support_email',
                'setting_value' => 'support@kwuankun.app',
                'data_type' => 'STRING',
                'description' => 'อีเมลสำหรับติดต่อทีมงานและแจ้งปัญหา',
                'is_public' => true,
            ],
            [
                'setting_key' => 'group.require_admin_approval',
                'setting_value' => 'true',
                'data_type' => 'BOOLEAN',
                'description' => 'คำขอสร้างก๊วนใหม่ต้องได้รับการอนุมัติจาก Platform Admin หรือไม่',
                'is_public' => false,
            ],
            [
                'setting_key' => 'group.max_member_default_limit',
                'setting_value' => '200',
                'data_type' => 'INTEGER',
                'description' => 'จำนวนสมาชิกสูงสุดเริ่มต้นต่อก๊วน',
                'is_public' => true,
            ],
            [
                'setting_key' => 'auth.line_token_verify_url',
                'setting_value' => 'https://api.line.me/oauth2/v2.1/verify',
                'data_type' => 'STRING',
                'description' => 'Endpoint สำหรับตรวจสอบ LINE ID Token ฝั่ง Backend',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('system_settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
