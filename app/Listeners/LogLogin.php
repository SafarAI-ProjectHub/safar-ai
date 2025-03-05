<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MoodleUserService;

class LogLogin
{
    protected $moodleService;

    public function __construct(MoodleUserService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    public function handle(Login $event)
    {
        $user = Auth::user();

        if (!$user) {
            Log::error("❌ حدث تسجيل دخول بدون مستخدم صالح.");
            return;
        }

        Log::info("🔹 تسجيل دخول المستخدم: {$user->email}");

        // إنهاء أي جلسات مفتوحة سابقة
        $user->timeLogs()->whereNull('logout_time')->update([
            'logout_time' => now(),
            'session_status' => 'ended'
        ]);

        // تسجيل دخول جديد
        $user->timeLogs()->create([
            'login_time' => now(),
            'last_activity_time' => now(),
            'current_activity_start' => now(),
            'session_status' => 'active',
        ]);

        // **التحقق من وجود المستخدم في Moodle أو إنشاؤه**
        if (!$user->moodle_id) {
            Log::warning("⚠️ المستخدم {$user->email} غير مسجل في Moodle. سيتم إنشاؤه الآن...");
            $moodleUserId = $this->moodleService->createUser($user);

            if ($moodleUserId) {
                $user->update(['moodle_id' => $moodleUserId]);
                Log::info("✅ تم تسجيل المستخدم {$user->email} بنجاح في Moodle.");
            } else {
                Log::error("❌ فشل إنشاء المستخدم {$user->email} في Moodle.");
                return; // إيقاف تنفيذ التحديثات إذا فشل التسجيل
            }
        }

        // **إرسال إشعار تسجيل الدخول إلى Moodle**
        if ($user->moodle_id) {
            Log::info("🔄 تسجيل دخول المستخدم في Moodle...");
            $this->moodleService->logUserActivity($user->moodle_id, 'login');
        }

        // **تحديث بيانات المستخدم في Moodle**
        Log::info("🔄 تحديث بيانات المستخدم في Moodle...");
        $this->moodleService->updateUser($user);

        // **مزامنة كلمة المرور في Moodle عند تغييرها**
        if ($user->wasChanged('password')) {
            Log::info("🔄 تحديث كلمة المرور في Moodle...");
            $this->moodleService->updatePassword($user, $user->password);
        }
    }
}
