<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GeographicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Provinces
        $provinces = [
            ['code' => '10', 'name_th' => 'กรุงเทพมหานคร', 'name_en' => 'Bangkok', 'geography' => 'ภาคกลาง'],
            ['code' => '12', 'name_th' => 'นนทบุรี', 'name_en' => 'Nonthaburi', 'geography' => 'ภาคกลาง'],
            ['code' => '50', 'name_th' => 'เชียงใหม่', 'name_en' => 'Chiang Mai', 'geography' => 'ภาคเหนือ'],
            ['code' => '83', 'name_th' => 'ภูเก็ต', 'name_en' => 'Phuket', 'geography' => 'ภาคใต้'],
            ['code' => '20', 'name_th' => 'ชลบุรี', 'name_en' => 'Chon Buri', 'geography' => 'ภาคตะวันออก'],
        ];

        foreach ($provinces as $p) {
            DB::table('provinces')->updateOrInsert(
                ['code' => $p['code']],
                array_merge($p, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $bkk = DB::table('provinces')->where('code', '10')->first();
        $nonthaburi = DB::table('provinces')->where('code', '12')->first();
        $chiangmai = DB::table('provinces')->where('code', '50')->first();

        // 2. Districts (Bangkok Khets & Provinces Amphoes)
        $districts = [
            // Bangkok Districts
            ['province_id' => $bkk->id, 'code' => '1001', 'name_th' => 'เขตพระนคร', 'name_en' => 'Phra Nakhon'],
            ['province_id' => $bkk->id, 'code' => '1007', 'name_th' => 'เขตปทุมวัน', 'name_en' => 'Pathum Wan'],
            ['province_id' => $bkk->id, 'code' => '1030', 'name_th' => 'เขตจตุจักร', 'name_en' => 'Chatuchak'],
            ['province_id' => $bkk->id, 'code' => '1037', 'name_th' => 'เขตราชเทวี', 'name_en' => 'Ratchathewi'],
            ['province_id' => $bkk->id, 'code' => '1039', 'name_th' => 'เขตวัฒนา', 'name_en' => 'Watthana'],
            ['province_id' => $bkk->id, 'code' => '1047', 'name_th' => 'เขตบางนา', 'name_en' => 'Bang Na'],
            // Nonthaburi Districts
            ['province_id' => $nonthaburi->id, 'code' => '1201', 'name_th' => 'อำเภอเมืองนนทบุรี', 'name_en' => 'Mueang Nonthaburi'],
            ['province_id' => $nonthaburi->id, 'code' => '1206', 'name_th' => 'อำเภอปากเกร็ด', 'name_en' => 'Pak Kret'],
            // Chiang Mai Districts
            ['province_id' => $chiangmai->id, 'code' => '5001', 'name_th' => 'อำเภอเมืองเชียงใหม่', 'name_en' => 'Mueang Chiang Mai'],
        ];

        foreach ($districts as $d) {
            DB::table('districts')->updateOrInsert(
                ['code' => $d['code']],
                array_merge($d, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        $chatuchak = DB::table('districts')->where('code', '1030')->first();
        $bangna = DB::table('districts')->where('code', '1047')->first();
        $pathumwan = DB::table('districts')->where('code', '1007')->first();
        $pakkret = DB::table('districts')->where('code', '1206')->first();

        // 3. Subdistricts (Khwaeng & Tambon)
        $subdistricts = [
            // Chatuchak subdistricts
            ['district_id' => $chatuchak->id, 'code' => '103001', 'name_th' => 'แขวงลาดยาว', 'name_en' => 'Lat Yao', 'zip_code' => '10900', 'latitude' => 13.8425, 'longitude' => 100.5601],
            ['district_id' => $chatuchak->id, 'code' => '103002', 'name_th' => 'แขวงเสนานิคม', 'name_en' => 'Sena Nikhom', 'zip_code' => '10900', 'latitude' => 13.8340, 'longitude' => 100.5820],
            ['district_id' => $chatuchak->id, 'code' => '103003', 'name_th' => 'แขวงจันทรเกษม', 'name_en' => 'Chan Kasem', 'zip_code' => '10900', 'latitude' => 13.8210, 'longitude' => 100.5800],
            ['district_id' => $chatuchak->id, 'code' => '103004', 'name_th' => 'แขวงจอมพล', 'name_en' => 'Chomphon', 'zip_code' => '10900', 'latitude' => 13.8080, 'longitude' => 100.5620],
            ['district_id' => $chatuchak->id, 'code' => '103005', 'name_th' => 'แขวงจตุจักร', 'name_en' => 'Chatuchak', 'zip_code' => '10900', 'latitude' => 13.8010, 'longitude' => 100.5510],
            // Bang Na subdistricts
            ['district_id' => $bangna->id, 'code' => '104701', 'name_th' => 'แขวงบางนาเหนือ', 'name_en' => 'Bang Na Nuea', 'zip_code' => '10260', 'latitude' => 13.6680, 'longitude' => 100.6050],
            ['district_id' => $bangna->id, 'code' => '104702', 'name_th' => 'แขวงบางนาใต้', 'name_en' => 'Bang Na Tai', 'zip_code' => '10260', 'latitude' => 13.6550, 'longitude' => 100.6010],
            // Pathum Wan subdistrict
            ['district_id' => $pathumwan->id, 'code' => '100702', 'name_th' => 'แขวงวังใหม่', 'name_en' => 'Wang Mai', 'zip_code' => '10330', 'latitude' => 13.7430, 'longitude' => 100.5280],
            // Pak Kret subdistrict
            ['district_id' => $pakkret->id, 'code' => '120601', 'name_th' => 'ตำบลปากเกร็ด', 'name_en' => 'Pak Kret', 'zip_code' => '11120', 'latitude' => 13.9130, 'longitude' => 100.4980],
        ];

        foreach ($subdistricts as $sd) {
            DB::table('subdistricts')->updateOrInsert(
                ['code' => $sd['code']],
                array_merge($sd, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
