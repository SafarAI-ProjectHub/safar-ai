<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class HandleSessionTimeouts extends Command
{
    protected $signature = 'session:timeout';
    protected $description = 'Handle session timeouts for users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $timeout = config('session.lifetime');
        $inactiveThreshold = Carbon::now()->subMinutes($timeout);

        $users = User::whereHas('timeLogs', function ($query) {
            $query->whereNull('logout_time');
        })->get();

        foreach ($users as $user) {
            $log = $user->timeLogs()->whereNull('logout_time')->first();
            if ($log) {
                $lastActivity = Carbon::parse($log->last_activity_time);

                if ($lastActivity->lessThanOrEqualTo($inactiveThreshold)) {
                    $activeTime = $lastActivity->diffInSeconds(Carbon::parse($log->current_activity_start));
                    $log->update([
                        'total_active_time' => $log->total_active_time + $activeTime,
                        'session_status' => 'inactive',
                        'logout_time' => $inactiveThreshold,
                    ]);
                }
            }
        }
    }
}