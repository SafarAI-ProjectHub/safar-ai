<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Services\MoodleUserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateMoodleUser implements ShouldQueue
{
    use InteractsWithQueue;

    protected $moodleService;

    /**
     * حقن MoodleUserService
     */
    public function __construct(MoodleUserService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    /**
     * التعامل مع الحدث عند إنشاء مستخدم جديد
     */
    public function handle(UserCreated $event)
    {
        $user = $event->user;

        // إنشاء المستخدم في Moodle إذا لم يكن لديه Moodle ID
        if (!$user->moodle_id) {
            Log::info("🔍 يتم تسجيل المستخدم في Moodle: {$user->email}");

            $moodleUserId = $this->moodleService->createUser($user);

            if ($moodleUserId) {
                $user->update(['moodle_id' => $moodleUserId]);
                Log::info("✅ تم تسجيل المستخدم {$user->email} في Moodle بنجاح.");
            } else {
                Log::warning("⚠️ فشل تسجيل المستخدم {$user->email} في Moodle.");
            }
        } else {
            Log::warning("⚠️ المستخدم {$user->email} لديه بالفعل Moodle ID.");
        }
    }
}
