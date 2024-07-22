<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserActivityController extends Controller
{
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $data = json_decode(base64_decode($request->input('data')), true);
        // dd($data);
        $status = $data['status'];
        $additionalData = $data['additionalData'];

        if ($user) {
            $log = $user->timeLogs()->whereNull('logout_time')->first();

            if ($log) {
                $now = Carbon::now();
                $lastActivityTime = Carbon::parse($log->last_activity_time);
                $currentActivityStart = Carbon::parse($log->current_activity_start);

                if ($status === 'inactive' && isset($additionalData['activeTime'])) {
                    $activeTime = $additionalData['activeTime'];
                    if ($lastActivityTime->lessThan($currentActivityStart)) {
                        $log->update([
                            'total_active_time' => $log->total_active_time + $activeTime,
                            'session_status' => 'inactive',
                            'last_activity_time' => $now,
                            'stop_time' => $now,
                        ]);
                    } else {
                        $diffInSeconds = $lastActivityTime->diffInSeconds($currentActivityStart);
                        $adjustedActiveTime = max(0, $activeTime - $diffInSeconds);
                        $log->update([
                            'total_active_time' => $log->total_active_time + $adjustedActiveTime,
                            'session_status' => 'inactive',
                            'last_activity_time' => $now,
                            'stop_time' => $now,
                        ]);
                    }
                    session(['user_active_time' => $log->total_active_time]);
                } else if ($status === 'active') {
                    $log->update([
                        'current_activity_start' => $now,
                        'session_status' => 'active',
                        'last_activity_time' => $now,
                        'stop_time' => null,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
    }
}