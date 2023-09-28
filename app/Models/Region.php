<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $fillable = ['region']; // Add 'region' to the fillable array

    // Rest of your model code...

    public function statistics()
    {
        return $this->hasMany(RegionStatistic::class, 'region_id');
        
    }
}

   
