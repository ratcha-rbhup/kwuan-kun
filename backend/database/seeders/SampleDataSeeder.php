<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if sample data already exists to ensure idempotency when running seeders repeatedly
        if (DB::table('badminton_groups')->where('slug', 'chatuchak-smashers')->exists()) {
            return;
        }

        $badminton = DB::table('sport_categories')->where('code', 'badminton')->first();
        $bkk = DB::table('provinces')->where('code', '10')->first();
        $chatuchak = DB::table('districts')->where('code', '1030')->first();
        $latyao = DB::table('subdistricts')->where('code', '103001')->first();
        $levelP = DB::table('badminton_skill_levels')->where('code', 'INTERMEDIATE_P')->first();
        $levelS = DB::table('badminton_skill_levels')->where('code', 'BEGINNER_S')->first();
        $levelPPlus = DB::table('badminton_skill_levels')->where('code', 'INTERMEDIATE_P_PLUS')->first();

        // 1. Create Sample Venue
        $venueId = DB::table('venues')->insertGetId([
            'sport_category_id' => $badminton->id,
            'name' => 'สนามแบดมินตัน วินเนอร์ สปอร์ต คลับ (Winner Sport Club)',
            'branch_name' => 'สาขาจตุจักร',
            'province_id' => $bkk->id,
            'district_id' => $chatuchak->id,
            'subdistrict_id' => $latyao->id,
            'address_detail' => '123 ถนนวิภาวดีรังสิต แขวงลาดยาว เขตจตุจักร กรุงเทพฯ 10900',
            'latitude' => 13.8425000,
            'longitude' => 100.5601000,
            'phone_number' => '02-999-8888',
            'line_oa_id' => '@winnersport',
            'facilities' => json_encode([
                'aircon' => true,
                'parking' => true,
                'shower' => true,
                'pro_shop' => true,
                'canteen' => true,
            ]),
            'total_courts' => 8,
            'is_verified' => true,
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create Venue Courts
        for ($i = 1; $i <= 8; $i++) {
            DB::table('venue_courts')->insert([
                'venue_id' => $venueId,
                'court_number_or_name' => "คอร์ต {$i}",
                'floor_type' => 'RUBBER_MAT',
                'is_active' => true,
                'notes' => 'พื้นยางมาตรฐานแข่งขัน BWF Certified',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2. Create Sample Platform Admin User (No hardcoded credentials, purely identified via IDP or internal role)
        $adminUserId = DB::table('users')->insertGetId([
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => $adminUserId,
            'display_name' => 'Admin Somchai (Platform Admin)',
            'bio' => 'ผู้ดูแลระบบกลางแพลตฟอร์ม Add ก๊วน',
            'badminton_skill_level_id' => $levelP->id,
            'preferred_province_id' => $bkk->id,
            'preferred_district_id' => $chatuchak->id,
            'gender' => 'MALE',
            'dominant_hand' => 'RIGHT',
            'play_style' => 'DOUBLES',
            'experience_years' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $adminRole = DB::table('platform_roles')->where('slug', 'platform_admin')->first();
        DB::table('user_platform_roles')->insert([
            'user_id' => $adminUserId,
            'role_id' => $adminRole->id,
            'assigned_by_user_id' => null,
            'assigned_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Create Sample Group Leader (User & Identity via LINE)
        $leaderUserId = DB::table('users')->insertGetId([
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_profiles')->insert([
            'user_id' => $leaderUserId,
            'display_name' => 'Coach Kitti (หัวหน้าก๊วน)',
            'bio' => 'รักการตีแบดมินตัน ตีประจำทุกวันอังคารและพฤหัสบดี ยินดีต้อนรับเพื่อนใหม่ทุกคนครับ',
            'badminton_skill_level_id' => $levelPPlus->id,
            'preferred_province_id' => $bkk->id,
            'preferred_district_id' => $chatuchak->id,
            'gender' => 'MALE',
            'dominant_hand' => 'RIGHT',
            'play_style' => 'ALL',
            'experience_years' => 8,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_identities')->insert([
            'user_id' => $leaderUserId,
            'provider' => 'line',
            'provider_subject' => 'U' . Str::random(32), // LINE user subject id from verified ID token
            'email' => 'kitti.leader@example.com',
            'id_token_claims' => json_encode([
                'iss' => 'https://access.line.me',
                'aud' => 'SAMPLE_LINE_CHANNEL_ID',
                'name' => 'Coach Kitti',
            ]),
            'registered_at' => now(),
            'last_login_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('user_consents')->insert([
            'user_id' => $leaderUserId,
            'policy_type' => 'TERMS_OF_SERVICE',
            'version' => '1.0.0',
            'is_agreed' => true,
            'agreed_at' => now(),
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Create Sample Group Creation Request (Approved)
        $requestId = DB::table('group_creation_requests')->insertGetId([
            'requested_by_user_id' => $leaderUserId,
            'sport_category_id' => $badminton->id,
            'proposed_name' => 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์ (Chatuchak Smashers)',
            'proposed_description' => 'ก๊วนตีแบดมินตันเพื่อสุขภาพ มิตรภาพ และออกกำลังกาย ระดับฝีมือ S ถึง P+ ตีทุกวันอังคาร 19:00 - 21:00 น. ณ Winner Sport Club จตุจักร',
            'province_id' => $bkk->id,
            'district_id' => $chatuchak->id,
            'primary_venue_id' => $venueId,
            'target_skill_levels' => 'S, S+, P-, P, P+',
            'join_policy' => 'APPROVAL_REQUIRED',
            'supporting_info' => 'กลุ่มคนทำงานย่านจตุจักร ตีสม่ำเสมอมาแล้วกว่า 2 ปี ต้องการขยายก๊วนหาเพื่อนใหม่ร่วมก๊วน',
            'status' => 'APPROVED',
            'reviewed_by_user_id' => $adminUserId,
            'reviewed_at' => now(),
            'reviewer_notes' => 'อนุมัติการเปิดก๊วน ข้อมูลสถานที่และวัตถุประสงค์ครบถ้วนชัดเจน',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5. Create Approved Group in badminton_groups
        $groupId = DB::table('badminton_groups')->insertGetId([
            'sport_category_id' => $badminton->id,
            'name' => 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์ (Chatuchak Smashers)',
            'slug' => 'chatuchak-smashers',
            'description' => 'ก๊วนตีแบดมินตันเพื่อสุขภาพ มิตรภาพ และออกกำลังกาย ระดับฝีมือ S ถึง P+ ตีทุกวันอังคาร 19:00 - 21:00 น. ณ Winner Sport Club จตุจักร',
            'province_id' => $bkk->id,
            'district_id' => $chatuchak->id,
            'primary_venue_id' => $venueId,
            'min_skill_level_id' => $levelS->id,
            'max_skill_level_id' => $levelPPlus->id,
            'join_policy' => 'APPROVAL_REQUIRED',
            'max_members' => 40,
            'current_member_count' => 1,
            'status' => 'ACTIVE',
            'created_by_user_id' => $adminUserId, // Created on behalf or by system/admin
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link created group back to request
        DB::table('group_creation_requests')->where('id', $requestId)->update([
            'created_group_id' => $groupId,
        ]);

        // Record request status history
        DB::table('group_creation_request_history')->insert([
            'request_id' => $requestId,
            'old_status' => 'PENDING',
            'new_status' => 'APPROVED',
            'comment' => 'อนุมัติการจัดตั้งก๊วนสำเร็จ',
            'action_by_user_id' => $adminUserId,
            'created_at' => now(),
        ]);

        // 6. Add Leader to group_members as OWNER
        $memberId = DB::table('group_members')->insertGetId([
            'group_id' => $groupId,
            'user_id' => $leaderUserId,
            'role' => 'OWNER',
            'status' => 'ACTIVE',
            'joined_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Record membership history
        DB::table('group_member_history')->insert([
            'group_member_id' => $memberId,
            'group_id' => $groupId,
            'user_id' => $leaderUserId,
            'action' => 'JOINED',
            'old_role' => null,
            'new_role' => 'OWNER',
            'old_status' => null,
            'new_status' => 'ACTIVE',
            'reason' => 'ได้รับสิทธิ์ OWNER เมื่อคำขอสร้างก๊วนผ่านการอนุมัติ',
            'performed_by_user_id' => $adminUserId,
            'created_at' => now(),
        ]);

        // 7. Group Rules & Questions
        $rule1 = DB::table('group_rules')->insertGetId([
            'group_id' => $groupId,
            'title' => 'ตรงต่อเวลาและมารยาทในสนาม',
            'description' => 'กรุณามาถึงสนามก่อนเวลา 10 นาที และเคารพเพื่อนร่วมก๊วน ไม่พูดจาบั่นทอน',
            'is_mandatory' => true,
            'display_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('group_rules')->insert([
            'group_id' => $groupId,
            'title' => 'การยกเลิกลงชื่อกิจกรรม',
            'description' => 'หากลงชื่อแล้วไม่สามารถมาได้ กรุณายกเลิกก่อนเวลาเริ่มกิจกรรมอย่างน้อย 4 ชั่วโมง เพื่อให้สิทธิ์รายชื่อสำรอง',
            'is_mandatory' => true,
            'display_order' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('group_rule_acceptances')->insert([
            'group_id' => $groupId,
            'user_id' => $leaderUserId,
            'rule_id' => $rule1,
            'accepted_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('group_custom_questions')->insert([
            'group_id' => $groupId,
            'question_text' => 'ระดับฝีมือของคุณโดยประมาณ และตีแบดมินตันมาแล้วกี่ปี?',
            'question_type' => 'TEXT',
            'is_required' => true,
            'display_order' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 8. Create Sample Group Activity
        $activityId = DB::table('group_activities')->insertGetId([
            'group_id' => $groupId,
            'venue_id' => $venueId,
            'created_by_user_id' => $leaderUserId,
            'title' => 'ตีแบดมินตันประจำสัปดาห์ (อังคาร 19:00 - 21:00)',
            'description' => 'เปิด 2 คอร์ต (คอร์ต 3, 4) ใช้ลูกแบดมินตัน RSL Silver มีน้ำดื่มบริการ',
            'start_datetime' => now()->addDays(2)->setTime(12, 0, 0), // 19:00 BKK is 12:00 UTC
            'end_datetime' => now()->addDays(2)->setTime(14, 0, 0),   // 21:00 BKK is 14:00 UTC
            'court_details' => 'คอร์ต 3, คอร์ต 4',
            'max_participants' => 12,
            'allow_waitlist' => true,
            'max_waitlist' => 5,
            'confirmed_count' => 1,
            'waitlist_count' => 0,
            'status' => 'PUBLISHED',
            'estimated_cost_per_person' => 180.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Join Leader as participant
        $partId = DB::table('activity_participants')->insertGetId([
            'activity_id' => $activityId,
            'user_id' => $leaderUserId,
            'status' => 'CONFIRMED',
            'queue_number' => 1,
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('activity_participant_history')->insert([
            'participant_id' => $partId,
            'activity_id' => $activityId,
            'user_id' => $leaderUserId,
            'old_status' => null,
            'new_status' => 'CONFIRMED',
            'changed_by_user_id' => $leaderUserId,
            'change_reason' => 'ผู้สร้างกิจกรรมลงชื่อเข้าร่วมอัตโนมัติ',
            'created_at' => now(),
        ]);

        // 9. Sample Audit Log
        DB::table('audit_logs')->insert([
            'user_id' => $adminUserId,
            'event' => 'group.approved',
            'auditable_type' => 'App\\Models\\BadmintonGroup',
            'auditable_id' => $groupId,
            'old_values' => json_encode(['status' => 'PENDING']),
            'new_values' => json_encode(['status' => 'ACTIVE', 'group_id' => $groupId]),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            'created_at' => now(),
        ]);
    }
}
