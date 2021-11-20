<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed default permissions and roles
     *
     * @return void
     */

    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'internal patients']); //patients of the same organization as current user
        Permission::create(['name' => 'external patients']); //patients outside the orgnization of the current user

        // create a role and assign an array of permissions
        $role = Role::create(['name' => 'hospital-admin'])
            ->givePermissionTo(['internal patients']);

        $role = Role::create(['name' => 'professional'])
            ->givePermissionTo(['internal patients']);

        $role = Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());
    }
}
