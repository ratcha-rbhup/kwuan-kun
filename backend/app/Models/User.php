<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'status',
        'suspended_at',
        'suspension_reason',
        'last_active_at',
    ];

    protected $casts = [
        'suspended_at' => 'datetime',
        'last_active_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'user_id');
    }

    public function identities(): HasMany
    {
        return $this->hasMany(UserIdentity::class, 'user_id');
    }

    public function platformRoles(): BelongsToMany
    {
        return $this->belongsToMany(PlatformRole::class, 'user_platform_roles', 'user_id', 'role_id')
            ->withPivot(['assigned_by_user_id', 'assigned_at'])
            ->withTimestamps();
    }

    public function consents(): HasMany
    {
        return $this->hasMany(UserConsent::class, 'user_id');
    }

    public function phoneVerifications(): HasMany
    {
        return $this->hasMany(UserPhoneVerification::class, 'user_id');
    }

    public function notificationPreferences(): HasOne
    {
        return $this->hasOne(UserNotificationPreference::class, 'user_id');
    }

    public function groupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id');
    }

    public function activeGroupMemberships(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'user_id')->where('status', 'ACTIVE');
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(BadmintonGroup::class, 'group_members', 'user_id', 'group_id')
            ->withPivot(['role', 'status', 'joined_at', 'left_at', 'leave_reason', 'invited_by_user_id'])
            ->withTimestamps();
    }

    public function hasPlatformRole(string $slug): bool
    {
        return $this->platformRoles()->where('slug', $slug)->exists();
    }

    public function isPlatformAdmin(): bool
    {
        return $this->hasPlatformRole('platform_admin') || $this->hasPlatformRole('super_admin');
    }
}
