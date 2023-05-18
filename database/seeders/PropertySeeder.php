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
        $property[0] = Property::create([
            'title' => '4 bedroom house',
            'area' => 'Belummahara',
            'description' => '4 bedroom house with 1 garage and large garden',
            'url' => 'https://example.org',
            'propertyable_id' => 1,
            'propertyable_type' => 'rental',
            'location' => new Point(7.065697, 80.011635)
        ]);
        $property[0]->rentals()->create([
            "rooms" => 4,
            "bathrooms" => 2
        ]);


//        Property::create([
//            'title' => '3 bedroom apartment',
//            'area' => 'Miriswatte',
//            'description' => '1 A/C room with attached bathroom. 2 non-A/C rooms',
//            'url' => 'https://example.org/2',
//            'propertyable_id' => 1,
//            'propertyable_type' => 'rental',
//            'location' => new Point(7.072982, 80.015305)
//        ]);
    }
}
