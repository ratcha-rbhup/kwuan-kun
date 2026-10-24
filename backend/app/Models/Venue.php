<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'venues';

    protected $fillable = [
        'sport_category_id',
        'name',
        'branch_name',
        'province_id',
        'district_id',
        'subdistrict_id',
        'address_detail',
        'latitude',
        'longitude',
        'phone_number',
        'line_oa_id',
        'facilities',
        'total_courts',
        'is_verified',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'facilities' => 'array',
        'total_courts' => 'integer',
        'is_verified' => 'boolean',
    ];

    public function sportCategory(): BelongsTo
    {
        return $this->belongsTo(SportCategory::class, 'sport_category_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function subdistrict(): BelongsTo
    {
        return $this->belongsTo(Subdistrict::class, 'subdistrict_id');
    }

    public function courts(): HasMany
    {
        return $this->hasMany(VenueCourt::class, 'venue_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(BadmintonGroup::class, 'primary_venue_id');
    }
}
