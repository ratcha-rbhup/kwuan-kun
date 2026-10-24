<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupCreationRequest extends Model
{
    use HasFactory;

    protected $table = 'group_creation_requests';

    protected $fillable = [
        'requested_by_user_id',
        'sport_category_id',
        'proposed_name',
        'proposed_description',
        'province_id',
        'district_id',
        'primary_venue_id',
        'target_skill_levels',
        'join_policy',
        'supporting_info',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'reviewer_notes',
        'created_group_id',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

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

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function createdGroup(): BelongsTo
    {
        return $this->belongsTo(BadmintonGroup::class, 'created_group_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(GroupCreationRequestHistory::class, 'request_id')->latest('created_at');
    }
}
