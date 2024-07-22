<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class LogLogin
{
    public function handle(Login $event)
    {
        $user = Auth::user();

        // Update previous records
        $user->timeLogs()->whereNull('logout_time')->update([
            'logout_time' => \DB::raw('current_activity_start'),
            'session_status' => 'ended'
        ]);

        // Create a new log entry
        $user->timeLogs()->create([
            'login_time' => now(),
            'last_activity_time' => now(),
            'current_activity_start' => now(),
            'session_status' => 'active',
        ]);
    }
}