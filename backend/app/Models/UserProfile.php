<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profiles';

    protected $fillable = [
        'user_id',
        'display_name',
        'avatar_media_id',
        'bio',
        'badminton_skill_level_id',
        'preferred_province_id',
        'preferred_district_id',
        'gender',
        'dominant_hand',
        'play_style',
        'experience_years',
    ];

    protected $casts = [
        'experience_years' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'avatar_media_id');
    }

    public function skillLevel(): BelongsTo
    {
        return $this->belongsTo(BadmintonSkillLevel::class, 'badminton_skill_level_id');
    }

    public function preferredProvince(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'preferred_province_id');
    }

    public function preferredDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'preferred_district_id');
    }
}
