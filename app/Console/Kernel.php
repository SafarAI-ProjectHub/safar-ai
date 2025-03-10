<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\MoodleService;

class Kernel extends ConsoleKernel
{
    /**
     * Register the commands that can be called.
     */
    protected $commands = [
        \App\Console\Commands\ProcessAIScript::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // يمكنك جدولته كما تشاء (هنا مثلاً كل 5 دقائق):
        $schedule->call(function() {
            app(MoodleService::class)->syncCoursesFromMoodle();
        })->everyFiveMinutes();

        // أمثلة أخرى:
        // $schedule->command('inspire')->hourly();
        $schedule->command('app:cliq-cron-command')->hourly();
        $schedule->command('session:timeout')->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
