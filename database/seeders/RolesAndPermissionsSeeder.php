<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'admin',
            'teacher',
            'student',
            'Super Admin',
            'create courses'
        ];

        $roles = [
            'Admin',
            'Student',
            'Teacher',
            'Super Admin'
        ];

        // إنشاء الأدوار إذا لم تكن موجودة
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdminRole = Role::where('name', 'Super Admin')->first();

        $superAdminEmail = 'superadmin@safarAi.com';
        $superAdmin = User::firstOrCreate(
            ['email' => $superAdminEmail],
            [
                'first_name'       => 'Super',
                'last_name'        => 'Admin',
                'phone_number'     => '1234567890',
                'date_of_birth'    => '1990-01-01',
                'password'         => Hash::make('password'),
                'country_location' => 'USA',
                'status'           => 'active'
            ]
        );

        $superAdminRole->syncPermissions(Permission::all());

        if (!$superAdmin->hasRole('Super Admin')) {
            $superAdmin->assignRole('Super Admin');
        }

        User::all()->each(function ($user) {
            if (strpos($user->email, 'admin') !== false) {
                $user->assignRole('Admin');
            } elseif (strpos($user->email, 'teacher') !== false) {
                $user->assignRole('Teacher');
            } elseif (strpos($user->email, 'student') !== false) {
                $user->assignRole('Student');
            }
        });

        echo "✅ Roles and Permissions seeding completed successfully.\n";
    }
}
