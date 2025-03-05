<?php

namespace App\Listeners;

use App\Events\UserRoleUpdated;
use App\Services\MoodleUserService;
use Illuminate\Support\Facades\Log;

class UpdateMoodleUserRole
{
    protected $moodleService;

    public function __construct(MoodleUserService $moodleService)
    {
        $this->moodleService = $moodleService;
    }

    public function handle(UserRoleUpdated $event)
    {
        $user = $event->user;
        if (!$user->moodle_id) return;

        $newRoleId = $this->moodleService->getMoodleRoleId($user);
        $this->moodleService->assignRole($user->moodle_id, $newRoleId);

        Log::info("✅ تم تحديث دور المستخدم في Moodle: {$user->email} إلى الدور الجديد {$newRoleId}.");
    }
}
