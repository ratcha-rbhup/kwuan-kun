<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Services\MediaUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function __construct(
        protected MediaUploadService $mediaService
    ) {}

    /**
     * Get authenticated user's profile
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            // For mock/development fallback to first user or demo data
            $user = User::with(['profile.avatar', 'identities'])->first();
        }

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $profile = $user->profile;
        $lineIdentity = $user->identities()->where('provider', 'line')->first();

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $user->id,
                'display_name' => $profile?->display_name ?? ($lineIdentity?->id_token_claims['name'] ?? 'นักแบดมินตัน'),
                'avatar_url' => $profile?->avatar_url ?? ($lineIdentity?->id_token_claims['picture'] ?? null),
                'line_name' => $lineIdentity?->id_token_claims['name'] ?? null,
                'line_avatar' => $lineIdentity?->id_token_claims['picture'] ?? null,
                'phone' => $user->phoneVerifications()->where('status', 'VERIFIED')->latest()->value('phone_number') ?? '081-234-5678',
                'skill_level_id' => $profile?->badminton_skill_level_id,
                'gender' => $profile?->gender ?? 'PREFER_NOT_TO_SAY',
                'dominant_hand' => $profile?->dominant_hand ?? 'RIGHT',
                'play_style' => $profile?->play_style ?? 'ALL',
                'experience_years' => $profile?->experience_years,
                'bio' => $profile?->bio,
            ]
        ]);
    }

    /**
     * Update user profile data & optional avatar
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user() ?? User::first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'skill_level_id' => 'nullable|integer',
            'dominant_hand' => 'nullable|in:RIGHT,LEFT,BOTH',
            'play_style' => 'nullable|in:SINGLES,DOUBLES,MIXED,ALL',
            'avatarFile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $profile = $user->profile()->firstOrCreate(['user_id' => $user->id], [
            'display_name' => $request->input('display_name', 'Player')
        ]);

        $updateData = [];
        if ($request->has('display_name')) {
            $updateData['display_name'] = $request->input('display_name');
        }
        if ($request->has('bio')) {
            $updateData['bio'] = $request->input('bio');
        }
        if ($request->has('skill_level_id')) {
            $updateData['badminton_skill_level_id'] = $request->input('skill_level_id');
        }
        if ($request->has('dominant_hand')) {
            $updateData['dominant_hand'] = $request->input('dominant_hand');
        }
        if ($request->has('play_style')) {
            $updateData['play_style'] = $request->input('play_style');
        }

        // Handle avatar upload if attached
        if ($request->hasFile('avatarFile')) {
            $media = $this->mediaService->uploadAvatar($user, $request->file('avatarFile'));
            $updateData['avatar_media_id'] = $media->id;
        }

        if (!empty($updateData)) {
            $profile->update($updateData);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'บันทึกข้อมูลโปรไฟล์เรียบร้อยแล้ว',
            'data' => [
                'display_name' => $profile->display_name,
                'avatar_url' => $profile->fresh()->avatar_url,
            ]
        ]);
    }

    /**
     * Dedicated direct Avatar upload endpoint
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatarFile' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user = $request->user() ?? User::first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $media = $this->mediaService->uploadAvatar($user, $request->file('avatarFile'));

        return response()->json([
            'status' => 'success',
            'message' => 'อัปโหลดรูปโปรไฟล์เรียบร้อยแล้ว',
            'data' => [
                'media_id' => $media->id,
                'avatar_url' => $media->url,
            ]
        ]);
    }

    /**
     * Reset avatar to LINE default image
     */
    public function resetAvatar(Request $request): JsonResponse
    {
        $user = $request->user() ?? User::first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $this->mediaService->resetAvatar($user);

        return response()->json([
            'status' => 'success',
            'message' => 'รีเซ็ตรูปโปรไฟล์กลับไปใช้รูปจาก LINE สำเร็จ',
            'data' => [
                'avatar_url' => $user->profile?->fresh()->avatar_url
            ]
        ]);
    }
}
