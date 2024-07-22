<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;

class LogLogout
{
    public function handle(Logout $event)
    {
        $user = Auth::user();
        $log = $user->timeLogs()->whereNull('logout_time')->first();
        if ($log) {
            $activeTime = now()->diffInSeconds($log->current_activity_start);
            $log->update([
                'logout_time' => now(),
                'total_active_time' => $log->total_active_time + $activeTime,
                'session_status' => 'ended',
            ]);
        }
    }
}