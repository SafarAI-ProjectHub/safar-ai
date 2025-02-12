<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('name', 'Admin')->first();

        if ($adminRole) { 
            User::create([
                'first_name' => 'Admin',
                'last_name' => 'admin',
                'email' => 'admin@example.com',
                'phone_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'password' => bcrypt('password'),
                'country_location' => 'Jordan',
                // 'role_id' => $adminRole->id, 
            ]);
        }

        $studentRole = Role::where('name', 'Student')->first();

        if ($studentRole) {
            User::create([
                'first_name' => 'Student',
                'last_name' => 'student',
                'email' => 'student@example.com',
                'phone_number' => '1234567890',
                'date_of_birth' => '2000-01-01',
                'password' => bcrypt('password'),
                'country_location' => 'Jordan',
                // 'role_id' => $studentRole->id, 
            ]);
        }

        $teacherRole = Role::where('name', 'Teacher')->first();

        if ($teacherRole) {
            User::create([
                'first_name' => 'Teacher',
                'last_name' => 'teacher',
                'email' => 'teacher@example.com',
                'phone_number' => '1234567890',
                'date_of_birth' => '1985-01-01',
                'password' => bcrypt('password'),
                'country_location' => 'Jordan',
                // 'role_id' => $teacherRole->id,
            ]);
        }
    }
}
