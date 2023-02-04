<?php

namespace Database\Seeders;

use App\Models\Rental;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class RentalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $rentals[0] = Rental::create([
            'rooms' => 4,
            'bathrooms' => 2
        ]);
        $rentals[0]->property()->create([
            'title' => '4 bedroom house',
            'area' => 'Belummahara',
            'description' => '4 bedroom house with 1 garage and large garden',
            'url' => 'https://example.org',
            'location' => new Point(7.065697, 80.011635)
        ]);
        $rentals[1] = Rental::create([
            'rooms' => 6,
            'bathrooms' => 3
        ]);
        $rentals[1]->property()->create([
            'title' => '6 bedroom house',
            'area' => 'Miriswatte',
            'description' => '4 bedroom house with 1 garage and large garden',
            'url' => 'https://example.org',
            'location' => new Point(7.065697, 80.011635)
        ]);
    }
}
