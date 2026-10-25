<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPhoneVerification extends Model
{
    use HasFactory;

    protected $table = 'user_phone_verifications';

    protected $fillable = [
        'user_id',
        'phone_number',
        'otp_hash',
        'status',
        'requested_at',
        'verified_at',
        'expires_at',
        'attempts',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
        'attempts' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
