<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';

    protected $fillable = [
        'code',
        'name_th',
        'name_en',
        'geography',
    ];

    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'province_id');
    }

    public function venues(): HasMany
    {
        return $this->hasMany(Venue::class, 'province_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(BadmintonGroup::class, 'province_id');
    }
}
