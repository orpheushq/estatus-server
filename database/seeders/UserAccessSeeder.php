<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use App\Models\User;

class UserAccessSeeder extends Seeder
{
    /**
     * Assign roles to some default users.
     *
     * @return void
     */
    public function run()
    {
        /*Super Admin*/
        User::where('email', '=', 'admin@orpheus.digital')->first()
            ->assignRole('super-admin');

        User::where('email', '=', 'admin@lorem.dev')->first()
            ->assignRole('super-admin');

        /*Hospital Admin*/
        User::where('email', '=', 'anne@lorem.dev')->first()
            ->assignRole('hospital-admin');

        /*Professional*/
        User::where('email', '=', 'ben@lorem.dev')->first()
            ->assignRole('professional');

        User::where('email', '=', 'cathy@lorem.dev')->first()
            ->assignRole('professional');
    }
}
