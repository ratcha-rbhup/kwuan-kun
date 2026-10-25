<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupJoinRequest extends Model
{
    use HasFactory;

    protected $table = 'group_join_requests';

    protected $fillable = [
        'group_id',
        'user_id',
        'status',
        'request_message',
        'reviewed_by_user_id',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BadmintonGroup::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }
}
