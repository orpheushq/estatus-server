<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $guarded = []; //make all attributes mass assignable

    public function property()
    {
        return $this->morphOne(Property::class, 'propertyable');
    }
}
