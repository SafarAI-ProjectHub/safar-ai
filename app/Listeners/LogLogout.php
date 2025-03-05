<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\MoodleUserService;

class LogLogout
{
    protected $moodleService;

    public function __construct(MoodleUserService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    public function handle(Logout $event)
    {
        $user = Auth::user();

        if (!$user) {
            Log::error("❌ حدث تسجيل خروج بدون مستخدم صالح.");
            return;
        }

        Log::info("🔹 تسجيل خروج المستخدم: {$user->email}");

        // البحث عن الجلسة النشطة
        $log = $user->timeLogs()->whereNull('logout_time')->first();

        if ($log) {
            $activeTime = now()->diffInSeconds($log->current_activity_start);
            $log->update([
                'logout_time' => now(),
                'total_active_time' => $log->total_active_time + $activeTime,
                'session_status' => 'ended',
            ]);

            Log::info("⏳ تم تسجيل وقت الجلسة للمستخدم {$user->email}: {$activeTime} ثانية.");
        } else {
            Log::warning("⚠️ لم يتم العثور على جلسة نشطة للمستخدم {$user->email}.");
        }

        // تسجيل خروج المستخدم من Moodle
        if ($user->moodle_id) {
            Log::info("🔄 تسجيل خروج المستخدم من Moodle...");
            $this->moodleService->logUserActivity($user->moodle_id, 'logout');
        } else {
            Log::warning("⚠️ المستخدم {$user->email} غير مسجل في Moodle، لا يمكن تسجيل الخروج هناك.");
        }
    }
}
