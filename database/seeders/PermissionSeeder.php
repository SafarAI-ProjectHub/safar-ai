<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Create new permission
        Permission::create(['name' => 'create courses']);

        // Assign permission to roles
        $adminRole = Role::findByName('Admin');
        $superAdminRole = Role::findByName('Super Admin');

        $adminRole->givePermissionTo('create courses');
        $superAdminRole->givePermissionTo('create courses');


    }
}