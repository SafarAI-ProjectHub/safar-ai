<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Services\MoodleUserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserSeeder extends Seeder
{
    public function run()
    {
        $moodleUserService = app(MoodleUserService::class);

        $roles = [
            'Admin' => Role::where('name', 'Admin')->first(),
            'Student' => Role::where('name', 'Student')->first(),
            'Teacher' => Role::where('name', 'Teacher')->first(),
        ];

        $users = [
            [
                'first_name' => 'Admin',
                'last_name' => 'admin',
                'email' => 'admin@example.com',
                'phone_number' => '1234567890',
                'date_of_birth' => '1990-01-01',
                'password' => Hash::make('password'),
                'country_location' => 'Jordan',
                'role_name' => 'Admin',
            ],
            [
                'first_name' => 'Student',
                'last_name' => 'student',
                'email' => 'student@example.com',
                'phone_number' => '1234567890',
                'date_of_birth' => '2000-01-01',
                'password' => Hash::make('password'),
                'country_location' => 'Jordan',
                'role_name' => 'Student',
            ],
            [
                'first_name' => 'Teacher',
                'last_name' => 'teacher',
                'email' => 'teacher@example.com',
                'phone_number' => '1234567890',
                'date_of_birth' => '1985-01-01',
                'password' => Hash::make('password'),
                'country_location' => 'Jordan',
                'role_name' => 'Teacher',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role_name'];
            unset($userData['role_name']); // إزالة الدور من بيانات المستخدم

            $user = User::create($userData);
            if (isset($roles[$roleName])) {
                $user->assignRole($roles[$roleName]);
            }

            // تسجيل المستخدم في Moodle
            try {
                $moodleUserId = $moodleUserService->createUser($user);
                if ($moodleUserId) {
                    $user->update(['moodle_id' => $moodleUserId]);
                    Log::info("✅ تم تسجيل المستخدم في Moodle بنجاح: {$user->email}");
                } else {
                    Log::warning("⚠️ فشل تسجيل المستخدم في Moodle: {$user->email}");
                }
            } catch (\Exception $e) {
                Log::error("❌ خطأ أثناء تسجيل المستخدم في Moodle: {$e->getMessage()}");
            }
        }
    }
}
