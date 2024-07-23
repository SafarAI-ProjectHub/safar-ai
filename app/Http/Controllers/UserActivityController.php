<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Teacher;
use App\Models\User;
use App\Models\UserActivityLog;
use Yajra\DataTables\DataTables;

class UserActivityController extends Controller
{
    public function updateStatus(Request $request)
    {
        $user = Auth::user();
        $data = json_decode(base64_decode($request->input('data')), true);
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

    public function getDailyActivity(Request $request)
    {
        if ($request->ajax()) {
            $teachers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Teacher');
            })->get();

            $today = Carbon::today();

            $teacherActivity = $teachers->map(function ($teacher) use ($today) {
                $logs = $teacher->timeLogs()->whereDate('created_at', $today)->get();
                $totalActiveTime = $logs->sum('total_active_time');

                return [
                    'teacher' => $teacher->full_name,
                    'active_time' => $this->formatActiveTime($totalActiveTime),
                    'id' => $teacher->id,
                ];
            });

            return DataTables::of($teacherActivity)->make(true);
        }

        return view('dashboard.admin.teacher.index');
    }

    public function showLogs($id, Request $request)
    {
        if ($request->ajax()) {
            $user = User::findOrFail($id);
            $logs = $user->timeLogs()->orderBy('login_time', 'desc')->get();

            return DataTables::of($logs)
                ->addColumn('total_active_time', function ($log) {
                    return $log->total_active_time;
                })
                ->addColumn('end_time', function ($log) {
                    if ($log->logout_time) {
                        return Carbon::parse($log->logout_time)->format('Y-m-d H:i:s');
                    } else {
                        return Carbon::parse($log->last_activity_time)->format('Y-m-d H:i:s');
                    }
                })
                ->make(true);
        }

        return view('dashboard.admin.teacher.logs', compact('id'));
    }

    public function getMonthlyActivity(Request $request, $id)
    {
        if ($request->ajax()) {
            // Fetch the teacher by ID
            $teacher = Teacher::find($id);
            if (!$teacher) {
                return response()->json(['error' => 'Teacher not found'], 404);
            }
            $user = $teacher->user;

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $previousMonth = Carbon::now()->subMonth()->month;
            $previousYear = Carbon::now()->subMonth()->year;

            $daysInCurrentMonth = Carbon::now()->daysInMonth;
            $daysInPreviousMonth = Carbon::now()->subMonth()->daysInMonth;

            $currentMonthData = [];
            $previousMonthData = [];

            for ($day = 1; $day <= $daysInCurrentMonth; $day++) {
                $date = Carbon::create($currentYear, $currentMonth, $day);
                $dailyTotal = $user->timeLogs()->whereDate('created_at', $date)->sum('total_active_time');
                $currentMonthData[] = $dailyTotal;
            }

            for ($day = 1; $day <= $daysInPreviousMonth; $day++) {
                $date = Carbon::create($previousYear, $previousMonth, $day);
                $dailyTotal = $user->timeLogs()->whereDate('created_at', $date)->sum('total_active_time');
                $previousMonthData[] = $dailyTotal;
            }

            $currentMonthTotal = array_sum($currentMonthData);
            $previousMonthTotal = array_sum($previousMonthData);

            return response()->json([
                'currentMonthData' => $currentMonthData,
                'previousMonthData' => $previousMonthData,
                'currentMonthTotal' => $currentMonthTotal,
                'previousMonthTotal' => $previousMonthTotal,
            ]);
        }

        return view('dashboard.admin.teacher.index');
    }


    private function formatActiveTime($totalActiveTime)
    {

        if ($totalActiveTime < 60) {
            return $totalActiveTime . ' seconds';
        } else if ($totalActiveTime < 3600) {
            return round($totalActiveTime / 60) . ' minutes';
        } else {
            return round($totalActiveTime / 3600, 2) . ' hours';
        }
    }
}