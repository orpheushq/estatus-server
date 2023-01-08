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
        $permissions = [
            "internal users",
            "external users",
            "view users",

            "view login logs",

            "properties",
            "trends"
        ];

        foreach ($permissions as $p) {
            if (Permission::where("name", "=", $p)->first() == NULL) {
                Permission::create(['name' => $p]); // users of the same organization as the signed in user
            }
        }

        // create a role and assign an array of permissions
        $roles = [
            "admin" => [ "internal users", "properties", "trends" ],
            "client" => [],
            "tester" => [ "internal users", "external users", "view users", "view login logs" ],
            "super-admin" => "all"
        ];

        foreach ($roles as $r => $permissions) {
            $role = Role::where("name", "=", $r)->first();

            if ($role == NULL) {
                $role = Role::create(["name" => $r]);
            }

            switch ($r) {
                case "super-admin": {
                    $role->syncPermissions(Permission::all());
                    break;
                }
                default: {
                    $role->syncPermissions($permissions);
                    break;
                }
            }
        }
    }
}
