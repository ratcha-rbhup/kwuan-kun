<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupActivity extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'group_activities';

    protected $fillable = [
        'group_id',
        'venue_id',
        'created_by_user_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'court_details',
        'max_participants',
        'allow_waitlist',
        'max_waitlist',
        'confirmed_count',
        'waitlist_count',
        'status',
        'cancellation_reason',
        'estimated_cost_per_person',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'max_participants' => 'integer',
        'allow_waitlist' => 'boolean',
        'max_waitlist' => 'integer',
        'confirmed_count' => 'integer',
        'waitlist_count' => 'integer',
        'estimated_cost_per_person' => 'decimal:2',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BadmintonGroup::class, 'group_id');
    }

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class, 'activity_id');
    }

    public function confirmedParticipants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class, 'activity_id')
            ->where('status', 'CONFIRMED')
            ->orderBy('queue_number');
    }

    public function waitlistParticipants(): HasMany
    {
        return $this->hasMany(ActivityParticipant::class, 'activity_id')
            ->where('status', 'WAITLIST')
            ->orderBy('queue_number');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ActivityExpense::class, 'activity_id');
    }

    /**
     * Helper to return start datetime formatted in Asia/Bangkok timezone
     */
    public function getStartDatetimeBangkokAttribute(): ?Carbon
    {
        return $this->start_datetime?->timezone('Asia/Bangkok');
    }

    /**
     * Helper to return end datetime formatted in Asia/Bangkok timezone
     */
    public function getEndDatetimeBangkokAttribute(): ?Carbon
    {
        return $this->end_datetime?->timezone('Asia/Bangkok');
    }
}
