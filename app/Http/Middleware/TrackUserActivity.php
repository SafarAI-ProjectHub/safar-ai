<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TrackUserActivity
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $log = $user->timeLogs()->whereNull('logout_time')->first();

            if ($log) {
                $now = Carbon::now();
                $lastActivityTime = Carbon::parse($log->last_activity_time);
                $currentActivityStart = Carbon::parse($log->current_activity_start);
                $stopTime = $log->stop_time ? Carbon::parse($log->stop_time) : null;

                if ($stopTime) {
                    $log->update([
                        'stop_time' => null,
                        'last_activity_time' => $now,
                        'session_status' => 'active',
                    ]);
                } else if ($lastActivityTime->lessThan($currentActivityStart)) {
                    $log->update([
                        'last_activity_time' => $now,
                        'session_status' => 'active',
                    ]);
                } else {
                    $diffInSeconds = $lastActivityTime->diffInSeconds($currentActivityStart);
                    $adjustedActiveTime = max(0, $diffInSeconds);
                    $log->update([
                        'total_active_time' => $log->total_active_time + $adjustedActiveTime,
                        'last_activity_time' => $now,
                        'session_status' => 'active',
                    ]);
                    session(['user_active_time' => $log->total_active_time]);
                    session(['last_activity_time' => $now]);
                }
            }
        }

        return $next($request);
    }
}