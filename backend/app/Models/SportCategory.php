<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SportCategory extends Model
{
    use HasFactory;

    protected $table = 'sport_categories';

    protected $fillable = [
        'code',
        'name_th',
        'name_en',
        'icon',
        'description',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function skillLevels(): HasMany
    {
        return $this->hasMany(BadmintonSkillLevel::class, 'sport_category_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(BadmintonGroup::class, 'sport_category_id');
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'sport_category_id');
    }
}
