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
        $uniqueTypes = $this->all()->unique('propertyable_type');

        return array_values(array_map(fn($v): string => $v['propertyable_type'], $uniqueTypes->toArray()));
    }

    public function getAreas ()
    {
        $uniqueAreas = $this->all()->unique('area');
//        dd($uniqueAreas->toArray());
        return array_values(array_map(fn($v): string => $v['area'], $uniqueAreas->toArray()));
    }

    public function getAddresses ($region)
    {
        $uniqueAddresses = $this->all('address', 'area')->where('area', '=', $region)->unique('address');
        return array_values(array_map(fn($v): string => $v['address'], $uniqueAddresses->toArray()));;
    }
}
