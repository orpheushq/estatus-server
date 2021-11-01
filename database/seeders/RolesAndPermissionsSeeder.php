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
        Permission::create(['name' => 'edit patients']);
        Permission::create(['name' => 'delete patients']);

        // create a role and assign an array of permissions
        $role = Role::create(['name' => 'hospital-admin'])
            ->givePermissionTo(['edit patients', 'delete patients']);

        $role = Role::create(['name' => 'professional']);

        $role = Role::create(['name' => 'super-admin'])
            ->givePermissionTo(Permission::all());
    }
}
