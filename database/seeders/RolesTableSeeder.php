<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // seeder command 
        // php artisan db:seed --class=RolesTableSeeder

        DB::table('roles')->firstOrCreate([
            'role_name' => 'Admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed student role
        DB::table('roles')->firstOrCreate([
            'role_name' => 'Student',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed teacher role
        DB::table('roles')->firstOrCreate([
            'role_name' => 'Teacher',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
