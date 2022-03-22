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
        Permission::create(['name' => 'internal users']); // users of the same organization as the signed in user
        Permission::create(['name' => 'external users']); // users outside of the signed in user's organization

        Permission::create(['name' => 'add locations']); // users with this permission can add new locations

        // create a role and assign an array of permissions
        $role = Role::create(['name' => 'branch-admin'])
            ->givePermissionTo(['internal users']);

        $role = Role::create(['name' => 'client']);

        $role = Role::create(['name' => 'guide'])
            ->givePermissionTo(['add locations']);

        $role = Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());
    }
}
