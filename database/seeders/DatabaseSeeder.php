<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //super-admin
        User::create([
            'name' => "Orpheus Digital",
            'email' => "admin@orpheus.digital",
            'organizationId'=>1,
            'password' => Hash::make('Increase1!or'),
        ]);
        User::create([
            'name' => "Administrator",
            'email' => "admin@lorem.dev",
            'organizationId'=>1,
            'password' => Hash::make('Admin1!or'),
        ]);

        //admin
        User::create([
            'name' => "Kanchana Ekanayake",
            'organizationId'=>1,
            'email' => 'emkanchana98@gmail.com',
            'password' => Hash::make('Kanchana1!'),
        ]);

        //client
        User::create([
            'name' => "Cathy Drew",
            'organizationId'=>2,
            'email' => 'cathy@lorem.dev',
            'password' => Hash::make('Cathy1!'),
        ]);

        //tester
        User::create([
            'name' => "Rasheen Ruwisha",
            'organizationId'=>1,
            'email' => 'rasheen@lorem.dev',
            'password' => Hash::make('Rasheen1!'),
        ]);

        // call other seeders
        $this->call([
            OrganizationSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserAccessSeeder::class
        ]);
    }
}
