<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Property::create([
            'id' => 1,
            'title' => '4 bedroom house',
            'type' => 'house',
            'area' => 'Belummahara',
            'description' => '4 bedroom house with 1 garage and large garden',
            'url' => 'https://example.org',
            'size' => 7.2,
            'location' => new Point(7.065697, 80.011635)
        ]);
        Property::create([
            'id' => 2,
            'title' => '3 bedroom apartment',
            'type' => 'apartment',
            'area' => 'Miriswatte',
            'description' => '1 A/C room with attached bathroom. 2 non-A/C rooms',
            'url' => 'https://example.org/2',
            'size' => 5,
            'location' => new Point(7.072982, 80.015305)
        ]);
    }
}
