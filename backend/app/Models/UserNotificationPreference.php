<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $table = 'user_notification_preferences';

    protected $fillable = [
        'user_id',
        'line_notify_enabled',
        'line_pre_session_hours',
        'line_new_activity_enabled',
        'email_notify_enabled',
    ];

    protected $casts = [
        'line_notify_enabled' => 'boolean',
        'line_pre_session_hours' => 'integer',
        'line_new_activity_enabled' => 'boolean',
        'email_notify_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
