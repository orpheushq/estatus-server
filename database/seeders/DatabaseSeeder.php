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
            'password' => Hash::make('decrease'),
        ]);
        DB::table('users')->insert([
            'name' => "admin",
            'email' => 'admin@lorem.dev',
            'password' => Hash::make('admin123'),
        ]);

        //hospital-admin
        DB::table('users')->insert([
            'name' => "Anne Bennett",
            'email' => 'anne@lorem.dev',
            'password' => Hash::make('anne123'),
        ]);

        //professional
        DB::table('users')->insert([
            'name' => "Ben Chrisworth",
            'email' => 'ben@lorem.dev',
            'password' => Hash::make('ben123'),
        ]);
    }
}
