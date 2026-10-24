<?php

namespace App\Services;

use App\Models\BadmintonGroup;
use App\Models\GroupMember;
use App\Models\GroupMemberHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Throwable;

class GroupMembershipService
{
    /**
     * Join or Re-join a group without destroying historical records.
     *
     * @param BadmintonGroup $group
     * @param User $user
     * @param int|null $invitedByUserId
     * @return GroupMember
     * @throws Throwable
     */
    public function joinOrRejoinGroup(BadmintonGroup $group, User $user, ?int $invitedByUserId = null): GroupMember
    {
        return DB::transaction(function () use ($group, $user, $invitedByUserId) {
            $existing = GroupMember::where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existing) {
                if ($existing->status === 'ACTIVE') {
                    throw new InvalidArgumentException("ผู้ใช้งานเป็นสมาชิกที่กำลังใช้งานอยู่ในก๊วนนี้แล้ว");
                }

                if ($existing->status === 'SUSPENDED') {
                    throw new InvalidArgumentException("บัญชีของคุณในก๊วนนี้ถูกระงับสิทธิ์ กรุณาติดต่อผู้ดูแลก๊วน");
                }

                // Re-joining: Update existing record to ACTIVE and preserve history
                $oldStatus = $existing->status;
                $oldRole = $existing->role;
                $newRole = ($existing->role === 'OWNER') ? 'MEMBER' : $existing->role; // Cannot automatically rejoin as owner

                $existing->update([
                    'status' => 'ACTIVE',
                    'role' => $newRole,
                    'joined_at' => now(),
                    'left_at' => null,
                    'leave_reason' => null,
                ]);

                // Record history
                GroupMemberHistory::create([
                    'group_member_id' => $existing->id,
                    'group_id' => $group->id,
                    'user_id' => $user->id,
                    'action' => 'REJOINED',
                    'old_role' => $oldRole,
                    'new_role' => $newRole,
                    'old_status' => $oldStatus,
                    'new_status' => 'ACTIVE',
                    'reason' => 'กลับเข้าก๊วนเดิม',
                    'performed_by_user_id' => $user->id,
                    'created_at' => now(),
                ]);

                $group->increment('current_member_count');

                return $existing;
            }

            // Fresh join: Create new row in group_members
            $member = GroupMember::create([
                'group_id' => $group->id,
                'user_id' => $user->id,
                'role' => 'MEMBER',
                'status' => 'ACTIVE',
                'joined_at' => now(),
                'invited_by_user_id' => $invitedByUserId,
            ]);

            GroupMemberHistory::create([
                'group_member_id' => $member->id,
                'group_id' => $group->id,
                'user_id' => $user->id,
                'action' => 'JOINED',
                'old_role' => null,
                'new_role' => 'MEMBER',
                'old_status' => null,
                'new_status' => 'ACTIVE',
                'reason' => 'เข้าร่วมก๊วนครั้งแรก',
                'performed_by_user_id' => $user->id,
                'created_at' => now(),
            ]);

            $group->increment('current_member_count');

            return $member;
        });
    }

    /**
     * Transfer group ownership atomically to another member.
     * Guarantees exactly one active owner at all times.
     *
     * @param BadmintonGroup $group
     * @param User $currentOwnerUser
     * @param User $newOwnerUser
     * @return void
     * @throws Throwable
     */
    public function transferOwnership(BadmintonGroup $group, User $currentOwnerUser, User $newOwnerUser): void
    {
        DB::transaction(function () use ($group, $currentOwnerUser, $newOwnerUser) {
            $currentOwnerMember = GroupMember::where('group_id', $group->id)
                ->where('user_id', $currentOwnerUser->id)
                ->where('role', 'OWNER')
                ->where('status', 'ACTIVE')
                ->firstOrFail();

            $newOwnerMember = GroupMember::where('group_id', $group->id)
                ->where('user_id', $newOwnerUser->id)
                ->where('status', 'ACTIVE')
                ->firstOrFail();

            // 1. Demote old owner to ADMIN first to prevent collision on uq_group_single_active_owner
            $currentOwnerMember->update([
                'role' => 'ADMIN',
            ]);

            GroupMemberHistory::create([
                'group_member_id' => $currentOwnerMember->id,
                'group_id' => $group->id,
                'user_id' => $currentOwnerUser->id,
                'action' => 'OWNERSHIP_TRANSFERRED',
                'old_role' => 'OWNER',
                'new_role' => 'ADMIN',
                'old_status' => 'ACTIVE',
                'new_status' => 'ACTIVE',
                'reason' => "ส่งมอบสถานะเจ้าของก๊วนให้ผู้ใช้ ID {$newOwnerUser->id}",
                'performed_by_user_id' => $currentOwnerUser->id,
                'created_at' => now(),
            ]);

            // 2. Promote target member to OWNER
            $newOwnerMember->update([
                'role' => 'OWNER',
            ]);

            GroupMemberHistory::create([
                'group_member_id' => $newOwnerMember->id,
                'group_id' => $group->id,
                'user_id' => $newOwnerUser->id,
                'action' => 'ROLE_CHANGED',
                'old_role' => $newOwnerMember->getOriginal('role'),
                'new_role' => 'OWNER',
                'old_status' => 'ACTIVE',
                'new_status' => 'ACTIVE',
                'reason' => "ได้รับการแต่งตั้งเป็นเจ้าของก๊วนคนใหม่",
                'performed_by_user_id' => $currentOwnerUser->id,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Leave a group safely without destroying membership history.
     *
     * @param BadmintonGroup $group
     * @param User $user
     * @param string|null $reason
     * @return void
     * @throws Throwable
     */
    public function leaveGroup(BadmintonGroup $group, User $user, ?string $reason = null): void
    {
        DB::transaction(function () use ($group, $user, $reason) {
            $member = GroupMember::where('group_id', $group->id)
                ->where('user_id', $user->id)
                ->where('status', 'ACTIVE')
                ->firstOrFail();

            if ($member->role === 'OWNER') {
                throw new InvalidArgumentException("เจ้าของก๊วนไม่สามารถออกจากก๊วนได้ กรุณาโอนย้ายสิทธิ์ OWNER ให้สมาชิกท่านอื่นก่อน");
            }

            $oldStatus = $member->status;
            $member->update([
                'status' => 'LEFT',
                'left_at' => now(),
                'leave_reason' => $reason,
            ]);

            GroupMemberHistory::create([
                'group_member_id' => $member->id,
                'group_id' => $group->id,
                'user_id' => $user->id,
                'action' => 'LEFT',
                'old_role' => $member->role,
                'new_role' => $member->role,
                'old_status' => $oldStatus,
                'new_status' => 'LEFT',
                'reason' => $reason ?: 'สมาชิกขอออกจากก๊วนด้วยตนเอง',
                'performed_by_user_id' => $user->id,
                'created_at' => now(),
            ]);

            $group->decrement('current_member_count');
        });
    }
}
