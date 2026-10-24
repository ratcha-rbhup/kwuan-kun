<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ActivityParticipant extends Model
{
    use HasFactory;

    protected $table = 'activity_participants';

    protected $fillable = [
        'activity_id',
        'user_id',
        'status',
        'queue_number',
        'registered_at',
        'cancelled_at',
        'cancellation_reason',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'queue_number' => 'integer',
        'registered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'checked_in_at' => 'datetime',
    ];

    public function activity(): BelongsTo
    {
        return $this->belongsTo(GroupActivity::class, 'activity_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(ActivityParticipantHistory::class, 'participant_id')->latest('created_at');
    }
}
