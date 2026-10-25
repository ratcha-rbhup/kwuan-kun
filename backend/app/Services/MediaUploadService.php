<?php

namespace App\Services;

use App\Models\MediaAsset;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MediaUploadService
{
    /**
     * Upload and assign user avatar
     */
    public function uploadAvatar(User $user, UploadedFile $file): MediaAsset
    {
        $profile = $user->profile;

        // Clean up previous avatar file if exists
        if ($profile && $profile->avatar_media_id) {
            $this->deleteMediaAssetById($profile->avatar_media_id);
        }

        // Store file to public disk: avatars/users
        $path = $file->store('avatars/users', 'public');

        $mediaAsset = MediaAsset::create([
            'uploaded_by_user_id' => $user->id,
            'disk' => 'public',
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size_bytes' => $file->getSize(),
            'media_category' => 'AVATAR',
            'is_private' => false,
        ]);

        if ($profile) {
            $profile->update(['avatar_media_id' => $mediaAsset->id]);
        }

        return $mediaAsset;
    }

    /**
     * Reset avatar (delete custom upload, revert to default / LINE)
     */
    public function resetAvatar(User $user): bool
    {
        $profile = $user->profile;
        if ($profile && $profile->avatar_media_id) {
            $this->deleteMediaAssetById($profile->avatar_media_id);
            $profile->update(['avatar_media_id' => null]);
            return true;
        }

        return false;
    }

    /**
     * Upload group logo or cover
     */
    public function uploadGroupMedia(User $user, UploadedFile $file, string $category = 'GROUP_LOGO'): MediaAsset
    {
        $subFolder = $category === 'GROUP_COVER' ? 'groups/covers' : 'groups/logos';
        $path = $file->store($subFolder, 'public');

        return MediaAsset::create([
            'uploaded_by_user_id' => $user->id,
            'disk' => 'public',
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size_bytes' => $file->getSize(),
            'media_category' => $category,
            'is_private' => false,
        ]);
    }

    /**
     * Helper to safely remove physical file and delete record
     */
    public function deleteMediaAssetById(int $mediaAssetId): void
    {
        $media = MediaAsset::find($mediaAssetId);
        if ($media) {
            if (Storage::disk($media->disk)->exists($media->file_path)) {
                Storage::disk($media->disk)->delete($media->file_path);
            }
            $media->delete();
        }
    }
}
