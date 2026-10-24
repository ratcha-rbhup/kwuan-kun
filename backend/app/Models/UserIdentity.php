<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserIdentity extends Model
{
    use HasFactory;

    protected $table = 'user_identities';

    protected $fillable = [
        'user_id',
        'provider',
        'provider_subject',
        'email',
        'id_token_claims',
        'registered_at',
        'last_login_at',
    ];

    protected $casts = [
        'id_token_claims' => 'array',
        'registered_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
