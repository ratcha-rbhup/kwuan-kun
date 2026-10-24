<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'code' => 'badminton',
                'name_th' => 'แบดมินตัน',
                'name_en' => 'Badminton',
                'icon' => 'sports_badminton',
                'description' => 'กีฬาแบดมินตัน - ก๊วนหลักของแพลตฟอร์ม',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'code' => 'tennis',
                'name_th' => 'เทนนิส',
                'name_en' => 'Tennis',
                'icon' => 'sports_tennis',
                'description' => 'กีฬาเทนนิส (เตรียมความพร้อมรองรับในอนาคต)',
                'is_active' => false,
                'display_order' => 2,
            ],
            [
                'code' => 'pickleball',
                'name_th' => 'พิกเคิลบอล',
                'name_en' => 'Pickleball',
                'icon' => 'sports_tennis',
                'description' => 'กีฬาพิกเคิลบอล (เตรียมความพร้อมรองรับในอนาคต)',
                'is_active' => false,
                'display_order' => 3,
            ],
            [
                'code' => 'squash',
                'name_th' => 'สควอช',
                'name_en' => 'Squash',
                'icon' => 'sports_baseball',
                'description' => 'กีฬาสควอช (เตรียมความพร้อมรองรับในอนาคต)',
                'is_active' => false,
                'display_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('sport_categories')->updateOrInsert(
                ['code' => $category['code']],
                array_merge($category, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
