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
        DB::table('users')->insert([
            'name' => "Orpheus Digital",
            'email' => 'admin@orpheus.digital',
            'password' => Hash::make('decrease'),
        ]);
        DB::table('users')->insert([
            'name' => "Jane Doe",
            'email' => 'jane@example.com',
            'password' => Hash::make('jane123'),
        ]);
        DB::table('users')->insert([
            'name' => "Ben James",
            'email' => 'ben@acme.com',
            'password' => Hash::make('ben123'),
        ]);
    }
}
