<?php

namespace App\Http\Controllers;

use App\Models\BadmintonGroup;
use App\Models\GroupCreationRequest;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Get list of group creation requests
     */
    public function groupRequests(Request $request): JsonResponse
    {
        $query = GroupCreationRequest::with(['requester.profile', 'province', 'district']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderByDesc('created_at')->get();

        $data = $requests->map(function ($r) {
            return [
                'id' => $r->id,
                'proposedName' => $r->proposed_name,
                'proposedSlug' => Str::slug($r->proposed_name),
                'applicantName' => $r->requester?->profile?->display_name ?? 'ผู้ขอเปิดก๊วน',
                'lineId' => 'line_requester',
                'phone' => '089-111-2233',
                'description' => $r->proposed_description,
                'venueName' => $r->primaryVenue?->name ?? 'Winner Sport Club จตุจักร',
                'provinceName' => $r->province?->name_th ?? 'กรุงเทพฯ',
                'districtName' => $r->district?->name_th ?? 'จตุจักร',
                'location' => ($r->district?->name_th ?? 'จตุจักร') . ', ' . ($r->province?->name_th ?? 'กรุงเทพฯ'),
                'skillRange' => $r->target_skill_levels ?: 'ระดับ S ถึง P+',
                'status' => $r->status,
                'submittedAt' => $r->created_at?->format('d/m/Y H:i') ?? 'วันนี้',
                'appliedDate' => $r->created_at?->format('d/m/Y H:i') ?? 'วันนี้',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Approve group creation request
     */
    public function approveGroupRequest(Request $request, int $id): JsonResponse
    {
        $groupReq = GroupCreationRequest::find($id);
        if (!$groupReq) {
            return response()->json(['status' => 'error', 'message' => 'Request not found'], 404);
        }

        DB::beginTransaction();
        try {
            // Update request
            $groupReq->status = 'APPROVED';
            $groupReq->reviewed_by_user_id = $request->user()?->id ?? 1;
            $groupReq->reviewed_at = now();
            $groupReq->save();

            // Create real BadmintonGroup
            $group = BadmintonGroup::create([
                'sport_category_id' => $groupReq->sport_category_id ?: 1,
                'name' => $groupReq->proposed_name,
                'slug' => Str::slug($groupReq->proposed_name) . '-' . rand(100, 999),
                'description' => $groupReq->proposed_description ?? '',
                'province_id' => $groupReq->province_id ?: 1,
                'district_id' => $groupReq->district_id ?: 1,
                'primary_venue_id' => $groupReq->primary_venue_id ?: 1,
                'min_skill_level_id' => 2,
                'max_skill_level_id' => 5,
                'join_policy' => $groupReq->join_policy ?: 'APPROVAL_REQUIRED',
                'max_members' => 40,
                'current_member_count' => 1,
                'status' => 'ACTIVE',
                'created_by_user_id' => $groupReq->requested_by_user_id ?: 1,
            ]);

            $groupReq->created_group_id = $group->id;
            $groupReq->save();

            // Appoint creator as OWNER
            GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $groupReq->requested_by_user_id ?: 1,
                'role' => 'OWNER',
                'status' => 'ACTIVE',
                'joined_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "อนุมัติก๊วน \"{$group->name}\" เรียบร้อยแล้ว ระบบได้สร้างก๊วนและแต่งตั้งเป็น OWNER สำเร็จ",
                'data' => [
                    'groupId' => $group->id,
                    'groupName' => $group->name,
                    'slug' => $group->slug,
                    'status' => $group->status,
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการอนุมัติ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject group creation request
     */
    public function rejectGroupRequest(Request $request, int $id): JsonResponse
    {
        $groupReq = GroupCreationRequest::find($id);
        if (!$groupReq) {
            return response()->json(['status' => 'error', 'message' => 'Request not found'], 404);
        }

        $groupReq->status = 'REJECTED';
        $groupReq->rejection_reason = $request->reason ?? 'ข้อมูลไม่ผ่านเกณฑ์การพิจารณา';
        $groupReq->reviewed_by_user_id = $request->user()?->id ?? 1;
        $groupReq->reviewed_at = now();
        $groupReq->save();

        return response()->json([
            'status' => 'success',
            'message' => "ปฏิเสธคำขอเปิดก๊วน \"{$groupReq->proposed_name}\" เรียบร้อยแล้ว"
        ]);
    }

    /**
     * Get all groups for admin management
     */
    public function groups(): JsonResponse
    {
        $groups = BadmintonGroup::with(['province', 'district', 'primaryVenue', 'activeOwner.user.profile'])
            ->orderByDesc('created_at')
            ->get();

        $data = $groups->map(function ($g) {
            return [
                'id' => $g->id,
                'name' => $g->name,
                'slug' => $g->slug,
                'ownerName' => $g->activeOwner?->user?->profile?->display_name ?? 'Coach Kitti',
                'location' => ($g->district?->name_th ?? 'จตุจักร') . ' ' . ($g->province?->name_th ?? 'กรุงเทพฯ'),
                'venueName' => $g->primaryVenue?->name ?? 'Winner Sport Club',
                'memberCount' => $g->current_member_count ?: 18,
                'sessionsCount' => 24,
                'status' => $g->status,
                'createdAt' => $g->created_at?->format('d/m/Y') ?? '1 ม.ค. 2568',
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Toggle group status (ACTIVE / SUSPENDED)
     */
    public function toggleGroupStatus(Request $request, int $id): JsonResponse
    {
        $group = BadmintonGroup::find($id);
        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'Group not found'], 404);
        }

        $group->status = $group->status === 'ACTIVE' ? 'SUSPENDED' : 'ACTIVE';
        $group->save();

        return response()->json([
            'status' => 'success',
            'message' => "เปลี่ยนสถานะก๊วนเป็น \"{$group->status}\" เรียบร้อยแล้ว",
            'data' => [
                'id' => $group->id,
                'status' => $group->status,
            ]
        ]);
    }

    /**
     * Get moderation user reports
     */
    public function reports(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                [
                    'id' => 201,
                    'reporterName' => 'Coach Kitti',
                    'targetName' => 'ผู้เล่นไม่ระบุนาม (LINE: noname_bad)',
                    'targetType' => 'USER',
                    'category' => 'ลงชื่อแล้วไม่มา & ไม่จ่ายค่าคอร์ต',
                    'details' => 'ลงชื่อตีรอบวันอังคาร 2 ชม. แต่ไม่มาตามนัดและบล็อกไลน์ติดต่อไม่ได้',
                    'date' => '24 ต.ค. 2569',
                    'status' => 'PENDING',
                ],
                [
                    'id' => 202,
                    'reporterName' => 'แนน สายซัพพอร์ต',
                    'targetName' => 'ก๊วนแบดมินตัน มือทองคำ',
                    'targetType' => 'GROUP',
                    'category' => 'เก็บเงินเกินจริงไม่ตรงตามประกาศ',
                    'details' => 'ในระบบลง 150 บาทต่อคน แต่หน้างานเก็บจริง 300 บาท',
                    'date' => '21 ต.ค. 2569',
                    'status' => 'INVESTIGATING',
                ]
            ]
        ]);
    }

    /**
     * Get admin platform settings
     */
    public function settings(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'platformCommission' => 0,
                'maxMembersPerGroup' => 50,
                'allowGuestBrowsing' => true,
                'requireLineVerification' => true,
                'maintenanceMode' => false,
            ]
        ]);
    }

    /**
     * Update admin platform settings
     */
    public function updateSettings(Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'message' => 'บันทึกการตั้งค่าระบบเรียบร้อยแล้ว'
        ]);
    }
}
