<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // استدعاء الـseeders بالترتيب الصحيح

        $this->call(RolesTableSeeder::class);
        $this->call(CourseCategorySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(BlocksTableSeeder::class);
    }
}
