<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegionStatistic extends Model
{
    protected $fillable = [
        'region_id', // Add 'region_id' to the fillable array
        'price',
        'interest',
    ];

    protected $touches = [ 'region' ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }
}
