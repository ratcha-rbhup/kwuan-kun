<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BadmintonSkillLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sportCategory = DB::table('sport_categories')->where('code', 'badminton')->first();

        if (! $sportCategory) {
            return;
        }

        $levels = [
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'BEGINNER_N',
                'name_th' => 'มือใหม่ / N (Novice)',
                'name_en' => 'Novice (N)',
                'level_score' => 1,
                'description' => 'เพิ่งเริ่มเล่น หรือยังไม่มีทักษะการตีลูกขั้นพื้นฐาน เล่นเพื่อออกกำลังกายเบาๆ',
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'BEGINNER_S_MINUS',
                'name_th' => 'เริ่มต้น / S- (Starter-)',
                'name_en' => 'Starter Minus (S-)',
                'level_score' => 2,
                'description' => 'เสิร์ฟและโต้ลูกพื้นฐานได้ แต่ยังไม่แม่นยำและการเคลื่อนไหวในสนามยังช้า',
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'BEGINNER_S',
                'name_th' => 'พื้นฐาน / S (Starter)',
                'name_en' => 'Starter (S)',
                'level_score' => 3,
                'description' => 'ตีโต้ลูกได้ต่อเนื่อง วิ่งคอร์ตรู้ตำแหน่งพื้นฐาน เล่นเกมแต้มสนุกสนาน',
                'is_active' => true,
                'display_order' => 3,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'INTERMEDIATE_P_MINUS',
                'name_th' => 'ปานกลาง / P- (Play-)',
                'name_en' => 'Play Minus (P-)',
                'level_score' => 4,
                'description' => 'ตีได้หลากหลายจังหวะ ดึง หยอด ตบได้พอประมาณ เล่นเป็นทีมคู่ได้ดี',
                'is_active' => true,
                'display_order' => 4,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'INTERMEDIATE_P',
                'name_th' => 'ทั่วไป / P (Play)',
                'name_en' => 'Play (P)',
                'level_score' => 5,
                'description' => 'ระดับมาตรฐานของก๊วนทั่วไป ตบหนัก วางลูกแม่นยำ วิ่งคอร์ตสลับตำแหน่งคล่องแคล่ว',
                'is_active' => true,
                'display_order' => 5,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'INTERMEDIATE_P_PLUS',
                'name_th' => 'ชำนาญ / P+ (Play+)',
                'name_en' => 'Play Plus (P+)',
                'level_score' => 6,
                'description' => 'ความเร็วลูกสูง แก้เกมไว มีเทคนิคการตบและหยอดเฉียบคม เกมเร็วและเหนียว',
                'is_active' => true,
                'display_order' => 6,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'ADVANCED_B',
                'name_th' => 'ระดับแข่ง / B (Bronze/Open)',
                'name_en' => 'Advanced (B)',
                'level_score' => 7,
                'description' => 'ผ่านการลงแข่งขันรายการเปิด ร่างกายฟิต ทักษะเชิงกลยุทธ์และการวางลูกมาตรฐานแข่งขัน',
                'is_active' => true,
                'display_order' => 7,
            ],
            [
                'sport_category_id' => $sportCategory->id,
                'code' => 'EXPERT_A',
                'name_th' => 'นักกีฬา / A (Athlete/Semi-Pro)',
                'name_en' => 'Expert (A)',
                'level_score' => 8,
                'description' => 'อดีตหรือนักกีฬาตัวแทนสโมสร/เยาวชน/จังหวัด ระดับมืออาชีพหรือกึ่งอาชีพ',
                'is_active' => true,
                'display_order' => 8,
            ],
        ];

        foreach ($levels as $level) {
            DB::table('badminton_skill_levels')->updateOrInsert(
                [
                    'sport_category_id' => $level['sport_category_id'],
                    'code' => $level['code'],
                ],
                array_merge($level, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
