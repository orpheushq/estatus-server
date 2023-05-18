<?php

namespace Database\Seeders;

use App\Models\Land;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use MatanYadaev\EloquentSpatial\Objects\Point;

class LandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $lands[0] = Land::create([
            'size' => 7.2
        ]);
        $lands[0]->property()->create([
            'title' => 'My bare land',
            'area' => 'Miriswatte',
            'description' => 'My own bare land on the way to Gampaha',
            'url' => 'https://gampaha.lk',
            'location' => new Point(7.065697, 80.011635)
        ]);

    }
}
