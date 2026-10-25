<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MediaAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'media_assets';

    protected $fillable = [
        'uploaded_by_user_id',
        'disk',
        'file_path',
        'file_name',
        'mime_type',
        'file_size_bytes',
        'media_category',
        'is_private',
    ];

    protected $casts = [
        'file_size_bytes' => 'integer',
        'is_private' => 'boolean',
    ];

    protected $appends = [
        'url',
    ];

    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::disk($this->disk)->url($this->file_path);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }
}
