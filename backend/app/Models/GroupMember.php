<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GroupMember extends Model
{
    use HasFactory;

    protected $table = 'group_members';

    protected $fillable = [
        'group_id',
        'user_id',
        'role',
        'status',
        'joined_at',
        'left_at',
        'leave_reason',
        'invited_by_user_id',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(BadmintonGroup::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(GroupMemberHistory::class, 'group_member_id')->latest('created_at');
    }

    public function isActive(): bool
    {
        return $this->status === 'ACTIVE';
    }

    public function isOwner(): bool
    {
        return $this->role === 'OWNER' && $this->isActive();
    }

    public function canManageGroup(): bool
    {
        return in_array($this->role, ['OWNER', 'ADMIN']) && $this->isActive();
    }
}
