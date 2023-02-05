<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $guarded = []; //make all attributes mass assignable

    protected $touches = [ 'property' ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
