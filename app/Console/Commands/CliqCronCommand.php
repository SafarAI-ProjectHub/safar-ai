<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CronTracker;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\UserSubscription;



class CliqCronCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cliq-cron-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $cronTracker = CronTracker::create([
                'command' => 'app:cliq-cron-command',
                'status' => 'Running',
                'started_at' => now(),
            ]);

            $this->cliqsubscriptions();

            $cronTracker->update([
                'status' => 'Success',
                'finished_at' => now(),
                'duration' => now()->diffInSeconds($cronTracker->started_at),
            ]);
        } catch (\Exception $e) {
            $cronTracker->update([
                'status' => 'Failed',
                'failed_at' => now(),
                'duration' => now()->diffInSeconds($cronTracker->started_at),
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function cliqsubscriptions()
    {
        $userSubscriptions = UserSubscription::whereHas('payments', function ($query) {
            $query->where('payment_type', 'cliq')
                ->whereRaw('created_at = (select max(created_at) from payments where user_subscription_id = user_subscriptions.id)');
        })->with([
                    'payments' => function ($query) {
                        $query->orderBy('created_at', 'desc');
                    }
                ])->get();



        if ($userSubscriptions->isEmpty()) {
            echo "No subscriptions found\n";

            \Log::channel('cliq')->info('No Subscriptions Found', [
                'status' => 'No Subscriptions Found',
                'date' => now(),
                'user->id' => 'NULL'
            ]);
            return;
        }

        foreach ($userSubscriptions as $subscription) {


            if ($subscription->next_billing_time < now()) {
                $subscription->update([
                    'status' => 'expired',
                ]);

                $student = Student::where('student_id', $subscription->user_id)->first();
                if (!$student) {
                    echo "Student not found" . $subscription->user_id . "\n";
                    continue;
                }
                $student->update([
                    'subscription_status' => 'expired',
                ]);
                echo "user id: " . $subscription->user_id . " expired\n";
                // new log file named cliq.log
                \Log::channel('cliq')->info('Subscription Expired', [
                    'status' => 'expired',
                    'expire date' => now(),
                    'user->id' => $subscription->user_id,
                ]);
            } else {
                echo "user id: " . $subscription->user_id . " skipped\n";

                \Log::channel('cliq')->info('Subscription Not Expired', [
                    'status' => 'skipped',
                    'next billing time' => $subscription->next_billing_time,
                    'user->id' => $subscription->user_id,
                ]);
            }
        }
    }
}