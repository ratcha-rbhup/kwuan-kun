<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupCreationRequestHistory extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'group_creation_request_history';

    protected $fillable = [
        'request_id',
        'old_status',
        'new_status',
        'comment',
        'action_by_user_id',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(GroupCreationRequest::class, 'request_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_by_user_id');
    }
}
