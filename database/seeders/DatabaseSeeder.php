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
            'password' => Hash::make('Admin1!'),
        ]);

        //branch-admin
        User::create([
            'name' => "Anne Bennett",
            'organizationId'=>1,
            'email' => 'anne@lorem.dev',
            'password' => Hash::make('Anne1!'),
        ]);
        User::create([
            'name' => "Ben Chrisworth",
            'organizationId'=>2,
            'email' => 'ben@lorem.dev',
            'password' => Hash::make('Ben1!'),
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
        User::create([
            'name' => "Ruchila Maditha",
            'organizationId'=>1,
            'email' => 'ruchila@lorem.dev',
            'password' => Hash::make('Ruchila2!'),
        ]);
        User::create([
            'name' => "Yukthika Fernando",
            'organizationId'=>1,
            'email' => 'yukthika@lorem.dev',
            'password' => Hash::make('Yukthika3!'),
        ]);

        // call other seeders
        $this->call([
            OrganizationSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserAccessSeeder::class
        ]);
    }
}
