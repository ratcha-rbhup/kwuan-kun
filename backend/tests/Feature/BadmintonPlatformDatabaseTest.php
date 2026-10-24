<?php

namespace Tests\Feature;

use App\Models\BadmintonGroup;
use App\Models\GroupActivity;
use App\Models\GroupCreationRequest;
use App\Models\GroupMember;
use App\Models\SportCategory;
use App\Models\User;
use App\Services\GroupApprovalService;
use App\Services\GroupMembershipService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BadmintonPlatformDatabaseTest extends TestCase
{
    /**
     * Test reference data is seeded correctly.
     */
    public function test_reference_data_exists(): void
    {
        $this->assertDatabaseHas('sport_categories', [
            'code' => 'badminton',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('badminton_skill_levels', [
            'code' => 'INTERMEDIATE_P',
        ]);

        $this->assertDatabaseHas('provinces', [
            'code' => '10', // Bangkok
        ]);

        $this->assertDatabaseHas('districts', [
            'code' => '1030', // Chatuchak
        ]);

        $this->assertDatabaseHas('platform_roles', [
            'slug' => 'platform_admin',
        ]);
    }

    /**
     * Test Group Approval atomic transaction.
     */
    public function test_group_approval_transaction(): void
    {
        $badminton = SportCategory::where('code', 'badminton')->first();
        $bkk = DB::table('provinces')->where('code', '10')->first();
        $chatuchak = DB::table('districts')->where('code', '1030')->first();

        // Create requester and admin
        $requester = User::create(['status' => 'ACTIVE']);
        $admin = User::create(['status' => 'ACTIVE']);

        // Create pending request
        $request = GroupCreationRequest::create([
            'requested_by_user_id' => $requester->id,
            'sport_category_id' => $badminton->id,
            'proposed_name' => 'ก๊วนทดสอบ แบดมินตัน พีค',
            'proposed_description' => 'รายละเอียดก๊วนทดสอบ',
            'province_id' => $bkk->id,
            'district_id' => $chatuchak->id,
            'join_policy' => 'APPROVAL_REQUIRED',
            'status' => 'PENDING',
        ]);

        $service = new GroupApprovalService();
        $group = $service->approveRequest($request, $admin, 'อนุมัติผ่านการทดสอบ');

        $this->assertNotNull($group->id);
        $this->assertEquals('ACTIVE', $group->status);

        // Verify request updated
        $this->assertDatabaseHas('group_creation_requests', [
            'id' => $request->id,
            'status' => 'APPROVED',
            'created_group_id' => $group->id,
            'reviewed_by_user_id' => $admin->id,
        ]);

        // Verify owner assigned in group_members
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $requester->id,
            'role' => 'OWNER',
            'status' => 'ACTIVE',
        ]);

        // Verify history logged
        $this->assertDatabaseHas('group_member_history', [
            'group_id' => $group->id,
            'user_id' => $requester->id,
            'action' => 'JOINED',
            'new_role' => 'OWNER',
        ]);
    }

    /**
     * Test database constraint enforces strictly ONE active OWNER per group.
     */
    public function test_group_cannot_have_multiple_active_owners(): void
    {
        $badminton = SportCategory::where('code', 'badminton')->first();
        $bkk = DB::table('provinces')->where('code', '10')->first();
        $chatuchak = DB::table('districts')->where('code', '1030')->first();

        $admin = User::create(['status' => 'ACTIVE']);
        $user1 = User::create(['status' => 'ACTIVE']);
        $user2 = User::create(['status' => 'ACTIVE']);

        $group = BadmintonGroup::create([
            'sport_category_id' => $badminton->id,
            'name' => 'Single Owner Test Group',
            'slug' => 'single-owner-test-' . uniqid(),
            'description' => 'Test',
            'province_id' => $bkk->id,
            'district_id' => $chatuchak->id,
            'status' => 'ACTIVE',
            'created_by_user_id' => $admin->id,
        ]);

        // Add first OWNER
        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user1->id,
            'role' => 'OWNER',
            'status' => 'ACTIVE',
        ]);

        // Trying to insert a SECOND active OWNER must throw QueryException due to uq_group_single_active_owner
        $this->expectException(QueryException::class);

        GroupMember::create([
            'group_id' => $group->id,
            'user_id' => $user2->id,
            'role' => 'OWNER',
            'status' => 'ACTIVE',
        ]);
    }

    /**
     * Test re-joining without destroying previous member history.
     */
    public function test_member_leave_and_rejoin_workflow(): void
    {
        $badminton = SportCategory::where('code', 'badminton')->first();
        $bkk = DB::table('provinces')->where('code', '10')->first();
        $chatuchak = DB::table('districts')->where('code', '1030')->first();

        $admin = User::create(['status' => 'ACTIVE']);
        $memberUser = User::create(['status' => 'ACTIVE']);

        $group = BadmintonGroup::create([
            'sport_category_id' => $badminton->id,
            'name' => 'Rejoin Test Group',
            'slug' => 'rejoin-test-' . uniqid(),
            'description' => 'Test',
            'province_id' => $bkk->id,
            'district_id' => $chatuchak->id,
            'status' => 'ACTIVE',
            'created_by_user_id' => $admin->id,
        ]);

        $service = new GroupMembershipService();

        // 1. Join
        $member = $service->joinOrRejoinGroup($group, $memberUser);
        $this->assertEquals('ACTIVE', $member->status);

        // 2. Leave
        $service->leaveGroup($group, $memberUser, 'ติดภารกิจชั่วคราว');
        $member->refresh();
        $this->assertEquals('LEFT', $member->status);

        // 3. Re-join
        $rejoined = $service->joinOrRejoinGroup($group, $memberUser);
        $rejoined->refresh();
        $this->assertEquals('ACTIVE', $rejoined->status);

        // Verify histories contain both LEFT and REJOINED
        $this->assertDatabaseHas('group_member_history', [
            'group_id' => $group->id,
            'user_id' => $memberUser->id,
            'action' => 'LEFT',
        ]);

        $this->assertDatabaseHas('group_member_history', [
            'group_id' => $group->id,
            'user_id' => $memberUser->id,
            'action' => 'REJOINED',
        ]);
    }

    /**
     * Test duplicate signup for the same activity is prevented by unique constraint.
     */
    public function test_duplicate_activity_signup_prevented(): void
    {
        $group = BadmintonGroup::first();
        $user = User::create(['status' => 'ACTIVE']);

        $activity = GroupActivity::create([
            'group_id' => $group->id,
            'created_by_user_id' => $user->id,
            'title' => 'Test Activity',
            'start_datetime' => now()->addDay(),
            'end_datetime' => now()->addDay()->addHours(2),
            'max_participants' => 8,
            'status' => 'PUBLISHED',
        ]);

        // First registration
        DB::table('activity_participants')->insert([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'status' => 'CONFIRMED',
            'queue_number' => 1,
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Second registration must fail with unique constraint violation
        $this->expectException(QueryException::class);

        DB::table('activity_participants')->insert([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'status' => 'CONFIRMED',
            'queue_number' => 2,
            'registered_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
