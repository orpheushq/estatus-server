<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;

class Property extends Model
{
    use HasFactory;

    protected $casts = [
        'location' => Point::class
    ];

    protected $guarded = []; //make all attributes mass assignable

    public function propertyable()
    {
        return $this->morphTo();
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    public function getTypes ()
    {
        $uniqueTypes = $this->select('propertyable_type')->distinct()->get();
        return array_values($uniqueTypes->pluck('propertyable_type')->toArray());
    }

    public function getAreas ()
    {
        $uniqueAreas = $this->select('area')->distinct()->get();
        return array_values($uniqueAreas->pluck('area')->toArray());
    }
}
