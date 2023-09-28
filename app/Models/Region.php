<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['region']; // Add 'region' to the fillable array

    public function statistics(): HasMany
    {
        return $this->hasMany(RegionStatistic::class);
    }
}
