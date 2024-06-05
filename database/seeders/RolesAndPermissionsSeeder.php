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
        // // Forget cached permissions
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // // Define permissions
        // $permissions = [
        //     'admin',
        //     'teacher',
        //     'student',
        //     'Super Admin'
        // ];
        // $roles = [
        //     'Admin',
        //     'Student',
        //     'Teacher',
        //     'Super Admin'
        // ];

        // foreach ($roles as $role) {
        //     Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        // }

        // // Create a user with Super Admin role
        // $superAdminEmail = 'superadmin@safarAi.com';
        // $superAdmin = User::firstOrCreate(
        //     ['email' => $superAdminEmail],
        //     [
        //         'first_name' => 'Super',
        //         'last_name' => 'Admin',
        //         'phone_number' => '1234567890',
        //         'date_of_birth' => '1990-01-01',
        //         'password' => Hash::make('password'),
        //         'country_location' => 'USA',
        //         'role_id' => 1,
        //         'status' => 'active'
        //     ]
        // );


        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        // }

        // // Assign all permissions to Super Admin role
        // $superAdminRole = Role::where('name', 'Super Admin')->first();
        // $allPermissions = Permission::all();
        // $superAdminRole->syncPermissions($allPermissions);

        // // Assign Super Admin role to the super admin user
        // $superAdmin->assignRole('Super Admin');

        // // Assign roles to users based on their role_id
        // User::all()->each(function ($user) {
        //     if ($user->role_id == 1) {
        //         $user->assignRole('Admin');
        //     } elseif ($user->role_id == 2) {
        //         $user->assignRole('Student');
        //     } elseif ($user->role_id == 3) {
        //         $user->assignRole('Teacher');
        //     }
        // });

        $user = User::find(1);
        $user->assignRole('Super Admin');
        // check if the user got the Role
        echo "user has role: " . $user->getRoleNames() . "\n";
        // check if hte user has Role
        echo "user has role: " . $user->hasRole('Super Admin') . "\n";


    }
}