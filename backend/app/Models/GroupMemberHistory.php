<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMemberHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'group_member_history';

    protected $fillable = [
        'group_member_id',
        'group_id',
        'user_id',
        'action',
        'old_role',
        'new_role',
        'old_status',
        'new_status',
        'reason',
        'performed_by_user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(GroupMember::class, 'group_member_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(BadmintonGroup::class, 'group_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by_user_id');
    }
}
