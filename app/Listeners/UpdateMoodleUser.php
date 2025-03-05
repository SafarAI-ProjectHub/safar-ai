<?php

namespace App\Listeners;

use App\Events\UserUpdated;
use App\Services\MoodleUserService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateMoodleUser implements ShouldQueue
{
    use InteractsWithQueue;

    protected $moodleService;

    public function __construct(MoodleUserService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    public function handle(UserUpdated $event)
    {
        $user = $event->user;
        if (!$user->moodle_id) {
            Log::warning("⚠️ المستخدم {$user->email} غير مرتبط بـ Moodle.");
            return;
        }

        $updated = $this->moodleService->updateUser($user);
        if ($updated) {
            Log::info("✅ تم تحديث بيانات المستخدم في Moodle: {$user->email}");
        } else {
            Log::warning("⚠️ فشل تحديث المستخدم في Moodle: {$user->email}");
        }
    }
}
