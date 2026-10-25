<?php

namespace App\Http\Controllers;

use App\Models\ActivityParticipant;
use App\Models\BadmintonGroup;
use App\Models\GroupActivity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Get list of activities / live sessions
     */
    public function index(Request $request): JsonResponse
    {
        $query = GroupActivity::with(['group', 'venue', 'confirmedParticipants.user.profile'])
            ->where('status', '!=', 'CANCELLED');

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        $activities = $query->orderBy('start_datetime', 'asc')->get();

        $data = $activities->map(function ($a) {
            $group = $a->group;
            $venue = $a->venue;

            // Format date nicely
            $start = $a->start_datetime ?? Carbon::now();
            $end = $a->end_datetime ?? Carbon::now()->addHours(2);
            $thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
            $dayName = $thaiDays[$start->dayOfWeek];
            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            $monthName = $thaiMonths[$start->month];
            $thaiYear = $start->year + 543;

            $dateDisplay = "{$dayName} {$start->day} {$monthName} {$thaiYear}";
            $timeDisplay = "{$start->format('H:i')} - {$end->format('H:i')} น. (2 ชม.)";

            $confirmedCount = $a->confirmed_count ?: $a->confirmedParticipants->count();
            $slotsAvailable = max(0, $a->max_participants - $confirmedCount);

            return [
                'id' => $a->id,
                'title' => $a->title,
                'groupName' => $group?->name ?? 'ก๊วนแบดมินตัน',
                'groupSlug' => $group?->slug ?? 'group',
                'venue' => $venue?->name ?? 'Winner Sport Club',
                'courtNumbers' => $a->court_details ?? 'คอร์ต 3, 4',
                'date' => $dateDisplay,
                'time' => $timeDisplay,
                'skillLevel' => 'ระดับ S ถึง P+',
                'priceEst' => number_format($a->estimated_cost_per_person ?: 180) . ' บาท/คน',
                'price' => number_format($a->estimated_cost_per_person ?: 180) . ' บาท/คน',
                'distance' => '3.2 กม.',
                'maxPlayers' => $a->max_participants,
                'confirmedPlayers' => $confirmedCount,
                'slotsAvailable' => $slotsAvailable,
                'totalSlots' => $a->max_participants,
                'isJoined' => false,
                'status' => $a->status,
                'players' => $a->confirmedParticipants->map(fn($p) => [
                    'name' => $p->user?->profile?->display_name ?? 'Player',
                    'role' => $p->role ?? 'MEMBER',
                    'skill' => 'P',
                    'isPaid' => $p->payment_status === 'PAID',
                ]),
                'facilities' => [
                    ['name' => 'ที่จอดรถ', 'icon' => 'fa-solid fa-square-parking'],
                    ['name' => 'ห้องอาบน้ำ', 'icon' => 'fa-solid fa-shower'],
                    ['name' => 'ติดแอร์', 'icon' => 'fa-solid fa-snowflake'],
                    ['name' => $a->shuttlecock_brand ?: 'ลูกแบด RSL', 'icon' => 'fa-solid fa-shuttlecock'],
                ]
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get activity detail
     */
    public function show(int $id): JsonResponse
    {
        $activity = GroupActivity::with(['group', 'venue', 'confirmedParticipants.user.profile'])->find($id);

        if (!$activity) {
            return response()->json(['status' => 'error', 'message' => 'Activity not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $activity
        ]);
    }

    /**
     * Create a new activity session (by Host/Owner)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'date' => 'required|date',
            'startTime' => 'nullable|string',
            'endTime' => 'nullable|string',
            'courtVenue' => 'nullable|string',
            'courtNumbers' => 'nullable|string',
            'shuttlecockBrand' => 'nullable|string',
            'maxPlayers' => 'nullable|integer|min:2',
            'priceEstimate' => 'nullable|numeric',
            'groupId' => 'nullable|integer',
        ]);

        $user = $request->user() ?? User::first();
        $group = BadmintonGroup::find($request->groupId) ?? BadmintonGroup::first();

        $startStr = $validated['date'] . ' ' . ($validated['startTime'] ?? '19:00') . ':00';
        $endStr = $validated['date'] . ' ' . ($validated['endTime'] ?? '21:00') . ':00';

        $activity = GroupActivity::create([
            'group_id' => $group?->id ?? 1,
            'venue_id' => $group?->primary_venue_id ?? 1,
            'created_by_user_id' => $user?->id ?? 1,
            'title' => $validated['title'],
            'start_datetime' => Carbon::parse($startStr),
            'end_datetime' => Carbon::parse($endStr),
            'court_details' => $validated['courtNumbers'] ?? 'คอร์ต 3, 4',
            'shuttlecock_brand' => $validated['shuttlecockBrand'] ?? 'RSL Silver Speed 76',
            'max_participants' => $validated['maxPlayers'] ?? 12,
            'confirmed_count' => 1,
            'estimated_cost_per_person' => $validated['priceEstimate'] ?? 180,
            'status' => 'OPEN',
        ]);

        // Auto add creator as participant
        ActivityParticipant::create([
            'activity_id' => $activity->id,
            'user_id' => $user?->id ?? 1,
            'status' => 'CONFIRMED',
            'role' => 'HOST',
            'queue_number' => 1,
            'confirmed_at' => now(),
            'payment_status' => 'PAID',
            'games_played' => 0,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'เปิดรอบตีสำเร็จ สมาชิกสามารถลงชื่อเข้าร่วมได้แล้ว',
            'data' => [
                'id' => $activity->id,
                'title' => $activity->title,
                'date' => $activity->start_datetime->format('Y-m-d'),
                'time' => $activity->start_datetime->format('H:i') . ' - ' . $activity->end_datetime->format('H:i') . ' น.',
                'courtVenue' => $validated['courtVenue'] ?? 'Winner Sport Club',
                'courtNumbers' => $activity->court_details,
                'maxPlayers' => $activity->max_participants,
                'registeredCount' => 1,
                'status' => $activity->status,
            ]
        ], 201);
    }

    /**
     * Update activity session (toggle status, edit details)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $activity = GroupActivity::find($id);
        if (!$activity) {
            return response()->json(['status' => 'error', 'message' => 'Activity not found'], 404);
        }

        if ($request->has('status')) {
            $activity->status = $request->status;
        }
        if ($request->has('title')) {
            $activity->title = $request->title;
        }
        if ($request->has('court_details')) {
            $activity->court_details = $request->court_details;
        }

        $activity->save();

        return response()->json([
            'status' => 'success',
            'message' => 'อัปเดตรอบตีสำเร็จ',
            'data' => $activity
        ]);
    }

    /**
     * Cancel/delete activity
     */
    public function destroy(int $id): JsonResponse
    {
        $activity = GroupActivity::find($id);
        if (!$activity) {
            return response()->json(['status' => 'error', 'message' => 'Activity not found'], 404);
        }

        $activity->status = 'CANCELLED';
        $activity->save();

        return response()->json([
            'status' => 'success',
            'message' => 'ยกเลิกรอบตีเรียบร้อยแล้ว'
        ]);
    }

    /**
     * Player joins activity
     */
    public function join(Request $request, int $id): JsonResponse
    {
        $activity = GroupActivity::find($id);
        if (!$activity) {
            return response()->json(['status' => 'error', 'message' => 'Activity not found'], 404);
        }

        $user = $request->user() ?? User::find(2) ?? User::first();

        // Check if already joined
        $existing = ActivityParticipant::where('activity_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            return response()->json([
                'status' => 'info',
                'message' => 'คุณได้ลงชื่อในรอบตีนี้ไว้แล้ว'
            ]);
        }

        $nextQueue = ActivityParticipant::where('activity_id', $id)->count() + 1;
        $isFull = $activity->confirmed_count >= $activity->max_participants;
        $status = $isFull ? 'WAITLIST' : 'CONFIRMED';

        $p = ActivityParticipant::create([
            'activity_id' => $activity->id,
            'user_id' => $user->id,
            'status' => $status,
            'role' => 'MEMBER',
            'queue_number' => $nextQueue,
            'confirmed_at' => $isFull ? null : now(),
            'notes' => $request->notes,
            'payment_status' => 'UNPAID',
            'games_played' => 0,
        ]);

        if (!$isFull) {
            $activity->increment('confirmed_count');
        }

        return response()->json([
            'status' => 'success',
            'message' => $isFull ? 'คอร์ตเต็มแล้ว คุณถูกจัดให้อยู่ในคิวสำรอง (Waitlist)' : 'ลงชื่อเข้าร่วมรอบตีสำเร็จ!',
            'data' => [
                'participantId' => $p->id,
                'status' => $p->status,
                'queueNumber' => $p->queue_number,
                'confirmedCount' => $activity->confirmed_count,
            ]
        ]);
    }

    /**
     * Player leaves activity
     */
    public function leave(Request $request, int $id): JsonResponse
    {
        $user = $request->user() ?? User::find(2) ?? User::first();

        $participant = ActivityParticipant::where('activity_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$participant) {
            return response()->json(['status' => 'error', 'message' => 'Participant not found'], 404);
        }

        $wasConfirmed = $participant->status === 'CONFIRMED';
        $participant->delete();

        $activity = GroupActivity::find($id);
        if ($wasConfirmed && $activity && $activity->confirmed_count > 0) {
            $activity->decrement('confirmed_count');
        }

        return response()->json([
            'status' => 'success',
            'message' => 'ถอนตัวจากรอบตีเรียบร้อยแล้ว'
        ]);
    }
}
