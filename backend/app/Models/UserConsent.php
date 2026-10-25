<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserConsent extends Model
{
    use HasFactory;

    protected $table = 'user_consents';

    protected $fillable = [
        'user_id',
        'policy_type',
        'version',
        'is_agreed',
        'agreed_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'is_agreed' => 'boolean',
        'agreed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
