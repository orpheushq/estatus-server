<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['region']; // Add 'region' to the fillable array

    public function statistics()
    {
        return $this->hasMany(RegionStatistic::class);
    }
}
