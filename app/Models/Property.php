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

    public function getTypes ()
    {
        $uniqueTypes = $this->all()->unique('type');

        return array_map(fn($v): string => $v['type'], $uniqueTypes->toArray());
    }

    public function getAreas ()
    {
        $uniqueAreas = $this->all()->unique('area');

        return array_map(fn($v): string => $v['area'], $uniqueAreas->toArray());
    }
}
