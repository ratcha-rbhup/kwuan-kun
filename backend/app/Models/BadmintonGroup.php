<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class BadmintonGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'badminton_groups';

    protected $fillable = [
        'sport_category_id',
        'name',
        'slug',
        'description',
        'logo_media_id',
        'cover_media_id',
        'province_id',
        'district_id',
        'primary_venue_id',
        'min_skill_level_id',
        'max_skill_level_id',
        'join_policy',
        'max_members',
        'current_member_count',
        'status',
        'created_by_user_id',
    ];

    protected $casts = [
        'max_members' => 'integer',
        'current_member_count' => 'integer',
    ];

    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class, 'sport_category_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function primaryVenue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'primary_venue_id');
    }

    public function minSkillLevel(): BelongsTo
    {
        return $this->belongsTo(BadmintonSkillLevel::class, 'min_skill_level_id');
    }

    public function maxSkillLevel(): BelongsTo
    {
        return $this->belongsTo(BadmintonSkillLevel::class, 'max_skill_level_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function logo(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'logo_media_id');
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(MediaAsset::class, 'cover_media_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id')->where('status', 'ACTIVE');
    }

    public function activeOwner(): HasOne
    {
        return $this->hasOne(GroupMember::class, 'group_id')
            ->where('role', 'OWNER')
            ->where('status', 'ACTIVE');
    }

    public function rules(): HasMany
    {
        return $this->hasMany(GroupRule::class, 'group_id')->orderBy('display_order');
    }

    public function customQuestions(): HasMany
    {
        return $this->hasMany(GroupCustomQuestion::class, 'group_id')->orderBy('display_order');
    }

    public function announcements(): HasMany
    {
        return $this->hasMany(GroupAnnouncement::class, 'group_id')
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(GroupActivity::class, 'group_id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(GroupInvite::class, 'group_id');
    }
}
