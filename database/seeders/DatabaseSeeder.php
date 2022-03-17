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
    }
}
