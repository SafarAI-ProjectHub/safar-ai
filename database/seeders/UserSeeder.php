<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed roles
        $adminRole = Role::create(['role_name' => 'Admin']);
        $studentRole = Role::create(['role_name' => 'Student']);
        $teacherRole = Role::create(['role_name' => 'Teacher']);

        // Seed admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email' => 'admin@example.com',
            "phone_number" => "1234567890",
            'date_of_birth' => '1990-01-01', // 'Y-m-d
            'password' => bcrypt('password'),
            "country_location" => "Jordan",
            'role_id' => $adminRole->id,
        ]);

        // Seed student
        User::create([
            'first_name' => 'Student',
            'last_name' => 'Student',
            "phone_number" => "1234567890",
            'email' => 'student@example.com',
            'date_of_birth' => '1990-01-01', // 'Y-m-d
            'password' => bcrypt('password'),
            "country_location" => "Jordan",
            'role_id' => $studentRole->id,
        ]);

        // Seed teacher
        User::create([
            'first_name' => 'Teacher',
            'last_name' => 'teacher',
            "phone_number" => "1234567890",
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            "country_location" => "Jordan",
            'date_of_birth' => '1990-01-01', // 'Y-m-d
            'role_id' => $teacherRole->id,
        ]);
    }
}
