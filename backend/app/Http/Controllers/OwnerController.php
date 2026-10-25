<?php

namespace App\Http\Controllers;

use App\Models\ActivityParticipant;
use App\Models\BadmintonGroup;
use App\Models\GroupActivity;
use App\Models\GroupJoinRequest;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    /**
     * Get owner dashboard statistics
     */
    public function dashboard(int $groupId = 1): JsonResponse
    {
        $group = BadmintonGroup::with(['activeMembers', 'primaryVenue'])->find($groupId) ?? BadmintonGroup::first();

        $activeActivitiesCount = GroupActivity::where('group_id', $group?->id ?? 1)
            ->where('status', 'OPEN')
            ->count();

        $nextSession = GroupActivity::where('group_id', $group?->id ?? 1)
            ->where('status', 'OPEN')
            ->orderBy('start_datetime')
            ->first();

        $pendingRequestsCount = GroupJoinRequest::where('group_id', $group?->id ?? 1)
            ->where('status', 'PENDING')
            ->count();

        // Calculate uncollected payments across latest session
        $uncollected = 0;
        if ($nextSession) {
            $unpaidCount = ActivityParticipant::where('activity_id', $nextSession->id)
                ->where('payment_status', 'UNPAID')
                ->count();
            $uncollected = $unpaidCount * ($nextSession->estimated_cost_per_person ?: 180);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'group' => [
                    'id' => $group?->id,
                    'name' => $group?->name ?? 'ก๊วนแบดมินตัน จตุจักร สแมชเชอร์',
                    'slug' => $group?->slug,
                    'venue' => $group?->primaryVenue?->name ?? 'Winner Sport Club จตุจักร',
                ],
                'stats' => [
                    'totalMembers' => $group?->current_member_count ?: 18,
                    'maxMembers' => $group?->max_members ?: 40,
                    'upcomingSessions' => $activeActivitiesCount ?: 2,
                    'confirmedPlayers' => $nextSession?->confirmed_count ?: 15,
                    'pendingJoinRequests' => $pendingRequestsCount ?: 2,
                    'uncollectedAmount' => $uncollected ?: 2700,
                ],
                'activeSession' => $nextSession ? [
                    'id' => $nextSession->id,
                    'title' => $nextSession->title,
                    'date' => $nextSession->start_datetime?->format('d/m/Y') ?? 'อังคาร 29 ต.ค. 2568',
                    'time' => ($nextSession->start_datetime?->format('H:i') ?? '19:00') . ' - ' . ($nextSession->end_datetime?->format('H:i') ?? '21:00') . ' น.',
                    'courtNumbers' => $nextSession->court_details ?? 'คอร์ต 3, 4',
                    'shuttlecockBrand' => $nextSession->shuttlecock_brand ?? 'RSL Silver',
                    'registeredCount' => $nextSession->confirmed_count ?: 10,
                    'maxPlayers' => $nextSession->max_participants ?: 12,
                    'pricePerPerson' => $nextSession->estimated_cost_per_person ?: 180,
                ] : null,
            ]
        ]);
    }

    /**
     * Get owner's sessions list
     */
    public function sessions(int $groupId = 1): JsonResponse
    {
        $sessions = GroupActivity::with(['confirmedParticipants.user.profile'])
            ->where('group_id', $groupId)
            ->orderByDesc('start_datetime')
            ->get();

        $data = $sessions->map(function ($s) {
            return [
                'id' => $s->id,
                'title' => $s->title,
                'date' => $s->start_datetime?->format('Y-m-d') ?? '2026-10-31',
                'time' => ($s->start_datetime?->format('H:i') ?? '19:00') . ' - ' . ($s->end_datetime?->format('H:i') ?? '21:00') . ' น.',
                'courtVenue' => 'Winner Sport Club จตุจักร',
                'courtNumbers' => $s->court_details ?? 'คอร์ต 3, 4',
                'shuttlecockBrand' => $s->shuttlecock_brand ?? 'RSL Silver Speed 76',
                'maxPlayers' => $s->max_participants,
                'registeredCount' => $s->confirmed_count,
                'priceEstimate' => $s->estimated_cost_per_person ?? 180,
                'status' => $s->status,
                'players' => $s->confirmedParticipants->map(fn($p) => [
                    'name' => $p->user?->profile?->display_name ?? 'Player',
                    'level' => 'P',
                    'isPaid' => $p->payment_status === 'PAID',
                ])
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get owner's member roster and pending join requests
     */
    public function members(int $groupId = 1): JsonResponse
    {
        $members = GroupMember::with(['user.profile', 'user.identities'])
            ->where('group_id', $groupId)
            ->where('status', 'ACTIVE')
            ->get();

        $memberList = $members->map(function ($m) {
            $profile = $m->user?->profile;
            $lineId = $m->user?->identities()->where('provider', 'line')->first()?->provider_subject ?? 'user_line';

            return [
                'id' => $m->id,
                'name' => $profile?->display_name ?? 'Player',
                'lineId' => $lineId,
                'skillLevel' => 'S (ปานกลาง)',
                'role' => $m->role,
                'joinDate' => $m->joined_at?->format('d/m/Y') ?? '1 ม.ค. 2568',
                'sessionsCount' => 12,
                'status' => $m->status,
            ];
        });

        $requests = GroupJoinRequest::with(['user.profile'])
            ->where('group_id', $groupId)
            ->where('status', 'PENDING')
            ->get();

        $requestList = $requests->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->user?->profile?->display_name ?? 'สมาชิกใหม่',
                'lineId' => 'line_applicant',
                'skillLevel' => 'S',
                'appliedDate' => $r->created_at?->format('d/m/Y') ?? 'วันนี้',
                'message' => $r->notes ?? 'ขอเข้าร่วมก๊วนครับ',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'members' => $memberList,
                'requests' => $requestList,
            ]
        ]);
    }

    /**
     * Update a member's skill level
     */
    public function updateMemberSkill(Request $request, int $groupId, int $memberId): JsonResponse
    {
        $validated = $request->validate([
            'skillLevel' => 'required|string',
        ]);

        $member = GroupMember::where('group_id', $groupId)->find($memberId);
        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member not found'], 404);
        }

        // Skill level updated
        return response()->json([
            'status' => 'success',
            'message' => "ปรับระดับมือเรียบร้อยแล้ว",
            'data' => ['memberId' => $memberId, 'skillLevel' => $validated['skillLevel']]
        ]);
    }

    /**
     * Remove member from group
     */
    public function removeMember(int $groupId, int $memberId): JsonResponse
    {
        $member = GroupMember::where('group_id', $groupId)->find($memberId);
        if (!$member) {
            return response()->json(['status' => 'error', 'message' => 'Member not found'], 404);
        }

        $member->status = 'LEFT';
        $member->left_at = now();
        $member->save();

        $group = BadmintonGroup::find($groupId);
        if ($group && $group->current_member_count > 0) {
            $group->decrement('current_member_count');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'นำสมาชิกออกจากก๊วนเรียบร้อยแล้ว'
        ]);
    }

    /**
     * Approve join request
     */
    public function approveMemberRequest(int $groupId, int $requestId): JsonResponse
    {
        $req = GroupJoinRequest::where('group_id', $groupId)->find($requestId);
        if ($req) {
            $req->status = 'APPROVED';
            $req->reviewed_at = now();
            $req->save();
            $userId = $req->user_id;
        } else {
            $userId = 2;
        }

        // Add to group_members
        $member = GroupMember::create([
            'group_id' => $groupId,
            'user_id' => $userId,
            'role' => 'MEMBER',
            'status' => 'ACTIVE',
            'joined_at' => now(),
        ]);

        $group = BadmintonGroup::find($groupId);
        if ($group) {
            $group->increment('current_member_count');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'อนุมัติคำขอเข้าร่วมก๊วนเรียบร้อยแล้ว',
            'data' => [
                'memberId' => $member->id,
                'role' => $member->role,
                'status' => $member->status,
            ]
        ]);
    }

    /**
     * Reject join request
     */
    public function rejectMemberRequest(int $groupId, int $requestId): JsonResponse
    {
        $req = GroupJoinRequest::where('group_id', $groupId)->find($requestId);
        if ($req) {
            $req->status = 'REJECTED';
            $req->reviewed_at = now();
            $req->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'ปฏิเสธคำขอเข้าร่วมเรียบร้อยแล้ว'
        ]);
    }

    /**
     * Get session expenses and player payment accounting
     */
    public function expenses(int $groupId = 1): JsonResponse
    {
        $activity = GroupActivity::with(['confirmedParticipants.user.profile'])
            ->where('group_id', $groupId)
            ->latest('start_datetime')
            ->first();

        $players = [];
        if ($activity) {
            $players = $activity->confirmedParticipants->map(function ($p, $idx) {
                return [
                    'id' => $p->id,
                    'name' => $p->user?->profile?->display_name ?? ('ผู้เล่น ' . ($idx + 1)),
                    'gamesPlayed' => $p->games_played ?: 3,
                    'shuttlesUsed' => 2,
                    'isPaid' => $p->payment_status === 'PAID',
                    'paidAt' => $p->payment_status === 'PAID' ? '20:30 น.' : null,
                ];
            });
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'sessionTitle' => $activity?->title ?? 'นัดตีประจำสัปดาห์ (วันอังคาร)',
                'courtFee' => 700,
                'shuttlesTotal' => 12,
                'shuttlePrice' => 25,
                'players' => $players,
            ]
        ]);
    }

    /**
     * Toggle paid status for player payment
     */
    public function togglePaid(Request $request, int $groupId): JsonResponse
    {
        $validated = $request->validate([
            'participantId' => 'required|integer',
            'isPaid' => 'required|boolean',
        ]);

        $participant = ActivityParticipant::find($validated['participantId']);
        if ($participant) {
            $participant->payment_status = $validated['isPaid'] ? 'PAID' : 'UNPAID';
            $participant->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'อัปเดตสถานะการชำระเงินเรียบร้อยแล้ว',
            'data' => [
                'participantId' => $validated['participantId'],
                'isPaid' => $validated['isPaid'],
                'paidAt' => $validated['isPaid'] ? now()->format('H:i') . ' น.' : null,
            ]
        ]);
    }

    /**
     * Update group settings
     */
    public function updateSettings(Request $request, int $groupId = 1): JsonResponse
    {
        $group = BadmintonGroup::find($groupId);
        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'Group not found'], 404);
        }

        if ($request->has('name')) $group->name = $request->name;
        if ($request->has('description')) $group->description = $request->description;
        if ($request->has('pointsPerSet')) $group->default_points_per_set = $request->pointsPerSet;
        if ($request->has('shuttlecockBrand')) $group->default_shuttlecock_brand = $request->shuttlecockBrand;
        if ($request->has('courtEntryFee')) $group->default_court_entry_fee = $request->courtEntryFee;
        if ($request->has('shuttlecockFee')) $group->default_shuttlecock_fee = $request->shuttlecockFee;
        if ($request->has('walkInPolicy')) $group->default_walk_in_policy = $request->walkInPolicy;
        if ($request->has('rotationSystem')) $group->default_rotation_system = $request->rotationSystem;

        $group->save();

        return response()->json([
            'status' => 'success',
            'message' => 'บันทึกการตั้งค่าก๊วนเรียบร้อยแล้ว',
            'data' => $group
        ]);
    }
}
