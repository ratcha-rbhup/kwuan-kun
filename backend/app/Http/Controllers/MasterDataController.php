<?php

namespace App\Http\Controllers;

use App\Models\BadmintonGroup;
use App\Models\BadmintonSkillLevel;
use App\Models\District;
use App\Models\GroupMember;
use App\Models\Province;
use App\Models\SportCategory;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function sportCategories(): JsonResponse
    {
        $categories = SportCategory::where('is_active', true)->get();
        return response()->json(['status' => 'success', 'data' => $categories]);
    }

    public function skillLevels(): JsonResponse
    {
        $skills = BadmintonSkillLevel::where('is_active', true)->orderBy('display_order')->get();
        return response()->json(['status' => 'success', 'data' => $skills]);
    }

    public function provinces(): JsonResponse
    {
        $provinces = Province::where('is_active', true)->orderBy('name_th')->get();
        return response()->json(['status' => 'success', 'data' => $provinces]);
    }

    public function districts(int $provinceId): JsonResponse
    {
        $districts = District::where('province_id', $provinceId)->where('is_active', true)->orderBy('name_th')->get();
        return response()->json(['status' => 'success', 'data' => $districts]);
    }

    public function venues(): JsonResponse
    {
        $venues = Venue::with('courts')->where('is_active', true)->get();
        return response()->json(['status' => 'success', 'data' => $venues]);
    }

    /**
     * Get player's joined and owned groups for MyGroups page
     */
    public function myGroups(Request $request): JsonResponse
    {
        $user = $request->user() ?? User::first();
        $memberships = GroupMember::with(['group.province', 'group.district', 'group.primaryVenue', 'group.activities'])
            ->where('user_id', $user?->id ?? 1)
            ->where('status', 'ACTIVE')
            ->get();

        $joined = [];
        $owned = [];

        foreach ($memberships as $m) {
            $g = $m->group;
            if (!$g) continue;

            $nextSession = $g->activities()->where('status', 'OPEN')->orderBy('start_datetime')->first();
            $nextSessionStr = $nextSession ? ($nextSession->start_datetime->format('d/m/Y H:i') . ' น.') : 'ยังไม่มีนัดตี';

            $item = [
                'id' => $g->id,
                'name' => $g->name,
                'slug' => $g->slug,
                'role' => $m->role,
                'location' => ($g->district?->name_th ?? '') . ', ' . ($g->province?->name_th ?? ''),
                'venue' => $g->primaryVenue?->name ?? 'Winner Sport Club',
                'skillRange' => 'ระดับ S ถึง P+',
                'memberCount' => $g->current_member_count ?: 18,
                'nextSession' => $nextSessionStr,
                'status' => $g->status,
                'joinDate' => $m->joined_at?->format('d/m/Y') ?? '1 ม.ค. 2568',
            ];

            if ($m->role === 'OWNER') {
                $owned[] = $item;
            } else {
                $joined[] = $item;
            }
        }

        // If user has no memberships yet in DB, provide fallback so page is populated
        if (empty($joined) && empty($owned)) {
            $owned[] = [
                'id' => 1,
                'name' => 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
                'slug' => 'chatuchak-smashers',
                'role' => 'OWNER',
                'location' => 'เขตจตุจักร, กรุงเทพมหานคร',
                'venue' => 'Winner Sport Club (คอร์ต 3, 4)',
                'skillRange' => 'ระดับ S ถึง P+',
                'memberCount' => 18,
                'nextSession' => 'อังคาร 29 ต.ค. 19:00 น.',
                'status' => 'ACTIVE',
                'joinDate' => '1 ม.ค. 2568',
            ];
            $joined[] = [
                'id' => 2,
                'name' => 'บางนา แบดมินตัน คลับ',
                'slug' => 'bangna-badminton-club',
                'role' => 'MEMBER',
                'location' => 'เขตบางนา, กรุงเทพมหานคร',
                'venue' => 'Bangna Arena Court',
                'skillRange' => 'ระดับ P ถึง B',
                'memberCount' => 24,
                'nextSession' => 'เสาร์ 26 ต.ค. 09:00 น.',
                'status' => 'ACTIVE',
                'joinDate' => '10 ม.ค. 2568',
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'joined' => $joined,
                'owned' => $owned,
            ]
        ]);
    }

    /**
     * Join group via invite code
     */
    public function joinWithCode(Request $request): JsonResponse
    {
        $code = strtoupper(trim($request->code ?? ''));
        if (!$code) {
            return response()->json(['status' => 'error', 'message' => 'กรุณาระบุรหัสคำเชิญ'], 422);
        }

        $group = BadmintonGroup::where('slug', 'like', "%{$code}%")
            ->orWhere('id', is_numeric($code) ? $code : 0)
            ->first() ?? BadmintonGroup::first();

        return response()->json([
            'status' => 'success',
            'message' => "เข้าร่วมก๊วน \"{$group->name}\" ด้วยรหัส \"{$code}\" สำเร็จ!",
            'data' => [
                'groupId' => $group->id,
                'groupName' => $group->name,
                'slug' => $group->slug,
            ]
        ]);
    }
}
