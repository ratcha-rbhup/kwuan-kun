<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'province_id',
        'code',
        'name_th',
        'name_en',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function subdistricts(): HasMany
    {
        return $this->hasMany(Subdistrict::class, 'district_id');
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'district_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(BadmintonGroup::class, 'district_id');
    }
}
