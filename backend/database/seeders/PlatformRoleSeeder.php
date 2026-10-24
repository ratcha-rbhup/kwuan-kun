<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlatformRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Administrator',
                'slug' => 'super_admin',
                'description' => 'ผู้ดูแลระบบสูงสุดของแพลตฟอร์ม มีสิทธิ์เต็มทุกส่วน',
                'is_system' => true,
            ],
            [
                'name' => 'Platform Administrator',
                'slug' => 'platform_admin',
                'description' => 'ผู้ดูแลแพลตฟอร์ม มีสิทธิ์อนุมัติคำขอสร้างก๊วน จัดการรายงาน และดูแลระบบ',
                'is_system' => true,
            ],
            [
                'name' => 'Platform Moderator',
                'slug' => 'platform_moderator',
                'description' => 'ผู้ดูแลความเรียบร้อย ตรวจสอบรายงานปัญหา และจัดการเนื้อหาไม่เหมาะสม',
                'is_system' => true,
            ],
            [
                'name' => 'Support Agent',
                'slug' => 'support_agent',
                'description' => 'เจ้าหน้าที่ดูแลลูกค้าและช่วยเหลือผู้ใช้งาน',
                'is_system' => true,
            ],
        ];

        foreach ($roles as $role) {
            DB::table('platform_roles')->updateOrInsert(
                ['slug' => $role['slug']],
                array_merge($role, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
