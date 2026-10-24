<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BadmintonSkillLevel extends Model
{
    use HasFactory;

    protected $table = 'badminton_skill_levels';

    protected $fillable = [
        'sport_category_id',
        'code',
        'name_th',
        'name_en',
        'level_score',
        'description',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'level_score' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class, 'sport_category_id');
    }
}
