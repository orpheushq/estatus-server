<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionStatistic extends Model
{
    protected $fillable = [
        'region_id', // Add 'region_id' to the fillable array
        'price',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
