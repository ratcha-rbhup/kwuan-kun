<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'setting_key',
        'setting_value',
        'data_type',
        'description',
        'is_public',
        'updated_by_user_id',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public static function get(string $key, $default = null)
    {
        $setting = static::where('setting_key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return match ($setting->data_type) {
            'INTEGER' => (int) $setting->setting_value,
            'BOOLEAN' => filter_var($setting->setting_value, FILTER_VALIDATE_BOOLEAN),
            'JSON' => json_decode($setting->setting_value, true),
            default => $setting->setting_value,
        };
    }
}
