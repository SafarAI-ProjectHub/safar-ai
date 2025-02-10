<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create(['name' => 'create courses']);

        $adminRole = Role::findByName('Admin');
        $superAdminRole = Role::findByName('Super Admin');

        $adminRole->givePermissionTo('create courses');
        $superAdminRole->givePermissionTo('create courses');
    }
}