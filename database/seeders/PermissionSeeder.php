<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permission = Permission::firstOrCreate(
            ['name' => 'create courses', 'guard_name' => 'web']
        );

        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web']
        );
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );

        $adminRole->givePermissionTo($permission);
        $superAdminRole->givePermissionTo($permission);
    }
}
