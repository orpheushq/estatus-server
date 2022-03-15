<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
        DB::table('users')->insert([
            'name' => "Orpheus Digital",
            'email' => 'admin@orpheus.digital',
            'organizationId'=>1,
            'password' => Hash::make('Increase1!or'),
        ]);
        DB::table('users')->insert([
            'name' => "admin",
            'email' => 'admin@lorem.dev',
            'organizationId'=>1,
            'password' => Hash::make('Admin1!'),
        ]);

        //branch-admin
        DB::table('users')->insert([
            'name' => "Anne Bennett",
            'organizationId'=>1,
            'email' => 'anne@lorem.dev',
            'password' => Hash::make('Anne1!'),
        ]);
        DB::table('users')->insert([
            'name' => "Ben Chrisworth",
            'organizationId'=>2,
            'email' => 'ben@lorem.dev',
            'password' => Hash::make('Ben1!'),
        ]);

        //client
        DB::table('users')->insert([
            'name' => "Cathy Drew",
            'organizationId'=>2,
            'email' => 'cathy@lorem.dev',
            'password' => Hash::make('Cathy1!'),
        ]);
    }
}
