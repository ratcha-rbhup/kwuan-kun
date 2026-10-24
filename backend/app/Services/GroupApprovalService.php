<?php

namespace App\Services;

use App\Models\BadmintonGroup;
use App\Models\GroupCreationRequest;
use App\Models\GroupCreationRequestHistory;
use App\Models\GroupMember;
use App\Models\GroupMemberHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Throwable;

class GroupApprovalService
{
    /**
     * Approve a group creation request within an atomic database transaction.
     *
     * @param GroupCreationRequest $request
     * @param User $adminUser
     * @param string|null $reviewerNotes
     * @return BadmintonGroup
     * @throws Throwable
     */
    public function approveRequest(GroupCreationRequest $request, User $adminUser, ?string $reviewerNotes = null): BadmintonGroup
    {
        if ($request->status !== 'PENDING' && $request->status !== 'NEEDS_REVISION') {
            throw new InvalidArgumentException("คำขอไม่อยู่ในสถานะที่สามารถอนุมัติได้ (สถานะปัจจุบัน: {$request->status})");
        }

        return DB::transaction(function () use ($request, $adminUser, $reviewerNotes) {
            // Generate unique slug
            $baseSlug = Str::slug($request->proposed_name);
            $slug = $baseSlug ?: 'group-' . Str::lower(Str::random(8));
            $counter = 1;
            while (BadmintonGroup::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            // 1. Create real record in badminton_groups
            $group = BadmintonGroup::create([
                'sport_category_id' => $request->sport_category_id,
                'name' => $request->proposed_name,
                'slug' => $slug,
                'description' => $request->proposed_description,
                'province_id' => $request->province_id,
                'district_id' => $request->district_id,
                'primary_venue_id' => $request->primary_venue_id,
                'join_policy' => $request->join_policy ?: 'APPROVAL_REQUIRED',
                'max_members' => 100,
                'current_member_count' => 1,
                'status' => 'ACTIVE',
                'created_by_user_id' => $adminUser->id, // Admin created on behalf of requester
            ]);

            // 2. Add requester to group_members with role OWNER
            $member = GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $request->requested_by_user_id,
                'role' => 'OWNER',
                'status' => 'ACTIVE',
                'joined_at' => now(),
            ]);

            // 3. Link created_group_id back to request & record approval details
            $oldStatus = $request->status;
            $request->update([
                'status' => 'APPROVED',
                'reviewed_by_user_id' => $adminUser->id,
                'reviewed_at' => now(),
                'reviewer_notes' => $reviewerNotes,
                'created_group_id' => $group->id,
            ]);

            // 4. Record history in group_creation_request_history
            GroupCreationRequestHistory::create([
                'request_id' => $request->id,
                'old_status' => $oldStatus,
                'new_status' => 'APPROVED',
                'comment' => $reviewerNotes ?: 'อนุมัติการจัดตั้งก๊วนเรียบร้อยแล้ว',
                'action_by_user_id' => $adminUser->id,
                'created_at' => now(),
            ]);

            // 5. Record membership history
            GroupMemberHistory::create([
                'group_member_id' => $member->id,
                'group_id' => $group->id,
                'user_id' => $request->requested_by_user_id,
                'action' => 'JOINED',
                'old_role' => null,
                'new_role' => 'OWNER',
                'old_status' => null,
                'new_status' => 'ACTIVE',
                'reason' => 'ได้รับสิทธิ์ OWNER เมื่อคำขอสร้างก๊วนผ่านการอนุมัติ',
                'performed_by_user_id' => $adminUser->id,
                'created_at' => now(),
            ]);

            // 6. Record Audit Log (without any sensitive tokens)
            DB::table('audit_logs')->insert([
                'user_id' => $adminUser->id,
                'event' => 'group.approved',
                'auditable_type' => BadmintonGroup::class,
                'auditable_id' => $group->id,
                'old_values' => json_encode(['request_id' => $request->id, 'status' => $oldStatus]),
                'new_values' => json_encode(['group_id' => $group->id, 'status' => 'APPROVED']),
                'ip_address' => request()->ip() ?? '127.0.0.1',
                'user_agent' => request()->userAgent() ?? 'Backend Worker',
                'created_at' => now(),
            ]);

            return $group;
        });
    }
}
