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

        // جلب الأدوار المتاحة في Laravel
        $roles = [
            'Admin'   => Role::where('name', 'Admin')->first(),
            'Student' => Role::where('name', 'Student')->first(),
            'Teacher' => Role::where('name', 'Teacher')->first(),
        ];

        // قائمة المستخدمين المراد إضافتهم
        $users = [
            [
                'first_name'      => 'Admin',
                'last_name'       => 'admin',
                'email'           => 'admin@example.com',
                'phone_number'    => '1234567890',
                'date_of_birth'   => '1990-01-01',
                'password'        => Hash::make('password'),
                'country_location'=> 'Jordan',
                'profile_image'   => null, // يمكن تعديلها لاحقًا
                'role_name'       => 'Admin',
            ],
            [
                'first_name'      => 'Student',
                'last_name'       => 'student',
                'email'           => 'student@example.com',
                'phone_number'    => '1234567890',
                'date_of_birth'   => '2000-01-01',
                'password'        => Hash::make('password'),
                'country_location'=> 'Jordan',
                'profile_image'   => null,
                'role_name'       => 'Student',
            ],
            [
                'first_name'      => 'Teacher',
                'last_name'       => 'teacher',
                'email'           => 'teacher@example.com',
                'phone_number'    => '1234567890',
                'date_of_birth'   => '1985-01-01',
                'password'        => Hash::make('password'),
                'country_location'=> 'Jordan',
                'profile_image'   => null,
                'role_name'       => 'Teacher',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role_name'];
            unset($userData['role_name']); // إزالة المفتاح قبل إنشاء المستخدم

            // إنشاء المستخدم في قاعدة بيانات Laravel
            $user = User::create($userData);

            // تعيين الدور في Laravel
            if (isset($roles[$roleName])) {
                $user->assignRole($roles[$roleName]);
            }

            // **الحصول على معرف الدور في Moodle**
            $roleId = isset($roles[$roleName]) ? $moodleUserService->getMoodleRoleId($user) : null;

            // **تسجيل المستخدم في Moodle**
            try {
                $moodleUserId = $moodleUserService->createUser($user, $roleId);
                if ($moodleUserId) {
                    // تحديث المعرّف في Laravel بعد تسجيل المستخدم في Moodle
                    $user->update(['moodle_id' => $moodleUserId]);

                    // تسجيل النشاط في Moodle
                    $moodleUserService->logUserActivity($moodleUserId, 'account_created');

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
