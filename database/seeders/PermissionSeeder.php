<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // إنشاء إذن "create courses" إذا لم يكن موجودًا مسبقًا
        $permission = Permission::firstOrCreate(
            ['name' => 'create courses', 'guard_name' => 'web']
        );

        // التأكد من وجود الدورين المطلوبين، وإنشاؤهما إذا لم يكونا موجودين
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin', 'guard_name' => 'web']
        );
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'Super Admin', 'guard_name' => 'web']
        );

        // منح إذن "create courses" للدورين Admin و Super Admin
        $adminRole->givePermissionTo($permission);
        $superAdminRole->givePermissionTo($permission);
    }
}
