<?php

namespace App\Http\Controllers;

use App\Models\BadmintonGroup;
use App\Models\GroupCreationRequest;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    /**
     * Get list of groups with filters and search
     */
    public function index(Request $request): JsonResponse
    {
        $query = BadmintonGroup::with(['province', 'district', 'primaryVenue', 'minSkillLevel', 'maxSkillLevel', 'activeOwner.user.profile'])
            ->where('status', 'ACTIVE');

        if ($request->filled('query')) {
            $q = $request->query('query');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($request->filled('province_id')) {
            $query->where('province_id', $request->province_id);
        }

        if ($request->filled('district_id')) {
            $query->where('district_id', $request->district_id);
        }

        if ($request->filled('skill_level_id')) {
            $lvl = $request->skill_level_id;
            $query->where(function ($sub) use ($lvl) {
                $sub->where('min_skill_level_id', '<=', $lvl)
                    ->where('max_skill_level_id', '>=', $lvl);
            });
        }

        $groups = $query->orderByDesc('current_member_count')->get();

        $data = $groups->map(function ($g) {
            $minCode = $g->minSkillLevel?->code ?? 'N';
            $maxCode = $g->maxSkillLevel?->code ?? 'A';
            $skillRange = "ระดับ {$minCode} ถึง {$maxCode}";

            return [
                'id' => $g->id,
                'name' => $g->name,
                'slug' => $g->slug,
                'description' => $g->description,
                'location' => ($g->district?->name_th ?? 'เขตจตุจักร') . ', ' . ($g->province?->name_th ?? 'กรุงเทพมหานคร'),
                'district' => $g->district?->name_th ?? 'จตุจักร',
                'province' => $g->province?->name_th ?? 'กรุงเทพมหานคร',
                'venue' => $g->primaryVenue?->name ?? 'Winner Sport Club',
                'venueName' => $g->primaryVenue?->name ?? 'Winner Sport Club',
                'logo' => $g->logo_media_id ? "/storage/{$g->logo_media_id}" : 'assets/image/badminton-club-logo.jpg',
                'coverImage' => $g->cover_media_id ? "/storage/{$g->cover_media_id}" : 'assets/image/badminton-court-cover.jpg',
                'bannerImage' => 'assets/image/badminton-court-cover.jpg',
                'skillRange' => $skillRange,
                'joinPolicy' => $g->join_policy,
                'memberCount' => $g->current_member_count ?: 18,
                'maxMembers' => $g->max_members ?: 40,
                'verified' => true,
                'status' => $g->status,
                'shuttlecockBrand' => $g->default_shuttlecock_brand ?? 'RSL Silver',
                'pointsPerSet' => $g->default_points_per_set ?? 21,
                'courtEntryFee' => $g->default_court_entry_fee ?? 80,
                'shuttlecockFee' => $g->default_shuttlecock_fee ?? 20,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get detailed group profile by slug
     */
    public function show(string $slug): JsonResponse
    {
        $group = BadmintonGroup::with([
            'province',
            'district',
            'primaryVenue.courts',
            'minSkillLevel',
            'maxSkillLevel',
            'activeOwner.user.profile',
            'rules',
            'activeMembers.user.profile',
            'activities' => function ($q) {
                $q->where('status', '!=', 'CANCELLED')->orderBy('start_datetime');
            }
        ])->where('slug', $slug)->first();

        if (!$group) {
            return response()->json([
                'status' => 'error',
                'message' => 'Group not found'
            ], 404);
        }

        $minCode = $group->minSkillLevel?->code ?? 'S';
        $maxCode = $group->maxSkillLevel?->code ?? 'P+';

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $group->id,
                'name' => $group->name,
                'slug' => $group->slug,
                'description' => $group->description,
                'location' => ($group->district?->name_th ?? '') . ', ' . ($group->province?->name_th ?? ''),
                'venue' => $group->primaryVenue?->name ?? 'Winner Sport Club',
                'venueAddress' => $group->primaryVenue?->address ?? '',
                'logo' => 'assets/image/badminton-club-logo.jpg',
                'coverImage' => 'assets/image/badminton-court-cover.jpg',
                'skillRange' => "ระดับ {$minCode} ถึง {$maxCode}",
                'joinPolicy' => $group->join_policy,
                'memberCount' => $group->current_member_count,
                'maxMembers' => $group->max_members,
                'verified' => true,
                'owner' => [
                    'name' => $group->activeOwner?->user?->profile?->display_name ?? 'Coach Kitti',
                    'role' => 'OWNER',
                ],
                'rules' => $group->rules->map(fn($r) => ['title' => $r->title, 'description' => $r->description]),
                'members' => $group->activeMembers->map(fn($m) => [
                    'id' => $m->id,
                    'name' => $m->user?->profile?->display_name ?? 'Player',
                    'role' => $m->role,
                    'skillLevel' => 'P',
                ]),
                'upcomingActivities' => $group->activities->map(fn($a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'date' => $a->start_datetime?->format('d/m/Y') ?? 'เร็วๆ นี้',
                    'time' => ($a->start_datetime?->format('H:i') ?? '19:00') . ' - ' . ($a->end_datetime?->format('H:i') ?? '21:00') . ' น.',
                    'courtNumbers' => $a->court_details,
                    'confirmedCount' => $a->confirmed_count,
                    'maxParticipants' => $a->max_participants,
                    'price' => $a->estimated_cost_per_person ?? 180,
                    'status' => $a->status,
                ]),
            ]
        ]);
    }

    /**
     * Submit request to create a new group
     */
    public function createGroupRequest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'proposedName' => 'required|string|max:150',
            'proposedDescription' => 'nullable|string|max:1000',
            'venue' => 'nullable|string|max:150',
            'province' => 'nullable',
            'district' => 'nullable',
            'contactPhone' => 'nullable|string|max:20',
            'contactLine' => 'nullable|string|max:100',
        ]);

        $user = $request->user() ?? User::first();

        $req = GroupCreationRequest::create([
            'requester_user_id' => $user?->id ?? 1,
            'sport_category_id' => 1,
            'proposed_name' => $validated['proposedName'],
            'proposed_slug' => Str::slug($validated['proposedName']) . '-' . rand(100, 999),
            'proposed_description' => $validated['proposedDescription'] ?? '',
            'proposed_province_id' => is_numeric($request->province) ? $request->province : 1,
            'proposed_district_id' => is_numeric($request->district) ? $request->district : 1,
            'status' => 'PENDING',
            'submitted_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'ส่งคำขอเปิดก๊วนใหม่เรียบร้อยแล้ว แอดมินจะดำเนินการตรวจสอบภายใน 24 ชม.',
            'data' => [
                'requestId' => $req->id,
                'proposedName' => $req->proposed_name,
                'status' => $req->status,
            ]
        ], 201);
    }
}
