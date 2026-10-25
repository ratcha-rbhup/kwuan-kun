<?php

namespace App\Http\Controllers;

use App\Models\BadmintonGroup;
use App\Models\User;
use App\Services\MediaUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GroupMediaController extends Controller
{
    public function __construct(
        protected MediaUploadService $mediaService
    ) {}

    /**
     * Upload group logo or cover
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'category' => 'required|in:GROUP_LOGO,GROUP_COVER',
            'group_id' => 'nullable|integer|exists:badminton_groups,id'
        ]);

        $user = $request->user() ?? User::first();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $media = $this->mediaService->uploadGroupMedia(
            $user,
            $request->file('image'),
            $request->input('category')
        );

        // If group_id is provided, optionally link directly
        if ($request->filled('group_id')) {
            $group = BadmintonGroup::find($request->input('group_id'));
            if ($group) {
                if ($request->input('category') === 'GROUP_LOGO') {
                    $group->update(['logo_media_id' => $media->id]);
                } else {
                    $group->update(['cover_media_id' => $media->id]);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'อัปโหลดรูปภาพก๊วนสำเร็จ',
            'data' => [
                'media_id' => $media->id,
                'category' => $media->media_category,
                'url' => $media->url,
            ]
        ]);
    }
}
