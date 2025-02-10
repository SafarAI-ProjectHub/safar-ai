<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email' => 'admin@example.com',
            'phone_number' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'password' => bcrypt('password'),
            'country_location' => 'Jordan',
            'role_id' => 1,
        ]);

        User::create([
            'first_name' => 'Student',
            'last_name' => 'Student',
            'phone_number' => '1234567890',
            'email' => 'student@example.com',
            'date_of_birth' => '1990-01-01',
            'password' => bcrypt('password'),
            'country_location' => 'Jordan',
            'role_id' => 2,
        ]);

        User::create([
            'first_name' => 'Teacher',
            'last_name' => 'teacher',
            'phone_number' => '1234567890',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'country_location' => 'Jordan',
            'date_of_birth' => '1990-01-01',
            'role_id' => 3,
        ]);
    }
}