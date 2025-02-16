<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\User;
use App\Models\Student;
use App\Models\Notification;
use App\Events\NotificationEvent;
use App\Events\SubscriptionEvent;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Carbon\Carbon;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        try {
            $payload = $request->all();

            Log::channel('webhook-log')->info('Webhook payload:', $payload);

            switch ($payload['event_type']) {
                case 'BILLING.SUBSCRIPTION.ACTIVATED':
                    $this->handleSubscriptionActivated($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    $this->handleSubscriptionCancelled($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.EXPIRED':
                    $this->handleSubscriptionExpired($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                    $this->handleSubscriptionPaymentFailed($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
                    $this->handleSubscriptionReactivated($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    $this->handleSubscriptionSuspended($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.UPDATED':
                    $this->handleSubscriptionUpdated($payload['resource']);
                    break;
                case 'PAYMENT.SALE.COMPLETED':
                    $this->handlePaymentCompleted($payload['resource']);
                    break;
                case 'PAYMENT.SALE.DENIED':
                    $this->handlePaymentDenied($payload['resource']);
                    break;
                case 'PAYMENT.SALE.PENDING':
                    $this->handlePaymentPending($payload['resource']);
                    break;
                case 'PAYMENT.SALE.REFUNDED':
                    $this->handlePaymentRefunded($payload['resource']);
                    break;
                case 'PAYMENT.SALE.REVERSED':
                    $this->handlePaymentReversed($payload['resource']);
                    break;
                case 'PAYMENT.REFUND.PENDING':
                    $this->handleRefundPending($payload['resource']);
                    break;
                default:
                    Log::channel('webhook-log')->info('Webhook event not handled:', [
                        'event_type' => $payload['event_type']
                    ]);
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error processing webhook:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    protected function handleSubscriptionActivated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id']; // PayPal subscription ID
            $planId         = $resource['plan_id'];
            $startTime      = Carbon::parse($resource['start_time']);
            $nextBillingTime= Carbon::parse($resource['billing_info']['next_billing_time'] ?? now());

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                // Mark student subscribed
                Student::where('student_id', $user->id)->update(['subscription_status' => 'subscribed']);

                // Mark userSubscription active
                UserSubscription::updateOrCreate(
                    ['user_id' => $user->id, 'subscription_id' => $planId],
                    [
                        'status' => 'active', 
                        'start_date' => $startTime, 
                        'next_billing_time' => $nextBillingTime
                    ]
                );

                $this->sendSubscriptionNotification($user->id, 'Subscription Activated', 'Your subscription has been activated.');
                event(new SubscriptionEvent($user->id, 'activated', 'Your subscription has been activated.'));
            }
        });
    }

    protected function handleSubscriptionCancelled($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId         = $resource['plan_id'] ?? null;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $user->update(['paypal_subscription_id' => null]);
                Student::where('student_id', $user->id)->update(['subscription_status' => 'cancelled']);

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'cancelled']);
                }

                $this->sendSubscriptionNotification($user->id, 'Subscription Cancelled', 'Your subscription has been cancelled.');
                event(new SubscriptionEvent($user->id, 'cancelled', 'Your subscription has been cancelled.'));
            }
        });
    }

    protected function handleSubscriptionExpired($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId         = $resource['plan_id'] ?? null;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                Student::where('student_id', $user->id)->update(['subscription_status' => 'expired']);

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'expired']);
                }

                $this->sendSubscriptionNotification($user->id, 'Subscription Expired', 'Your subscription has expired.');
            }
        });
    }

    protected function handleSubscriptionPaymentFailed($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId   = $resource['billing_agreement_id'];
            $paypalPaymentId  = $resource['id'];
            $amount           = $resource['amount']['total'] ?? 0;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                Student::where('student_id', $user->id)->update(['subscription_status' => 'failed']);
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'failed']);

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_type'       => 'paypal',
                        'user_id'              => $user->id,
                        'subscription_id'      => $userSubscription->subscription_id,
                        'payment_status'       => 'failed',
                        'transaction_date'     => Carbon::parse($resource['create_time']),
                        'amount'               => $amount,
                    ]);
                    $payment->save();

                    $this->sendSubscriptionNotification($user->id, 'Payment Failed', 'Your subscription payment has failed.');
                }
            }
        });
    }

    protected function handleSubscriptionReactivated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId   = $resource['id'];
            $planId           = $resource['plan_id'] ?? null;
            $nextBillingTime  = Carbon::parse($resource['billing_info']['next_billing_time'] ?? now());

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                Student::where('student_id', $user->id)->update(['subscription_status' => 'subscribed']);

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update([
                        'status' => 'active',
                        'next_billing_time' => $nextBillingTime
                    ]);

                    $this->sendSubscriptionNotification($user->id, 'Subscription Reactivated', 'Your subscription has been reactivated.');
                    event(new SubscriptionEvent($user->id, 'reactivated', 'Your subscription has been reactivated.'));
                }
            }
        });
    }

    protected function handleSubscriptionSuspended($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId         = $resource['plan_id'] ?? null;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                Student::where('student_id', $user->id)->update(['subscription_status' => 'suspended']);

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'suspended']);

                    $this->sendSubscriptionNotification($user->id, 'Subscription Suspended', 'Your subscription has been suspended.');
                }
            }
        });
    }

    protected function handleSubscriptionUpdated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId   = $resource['id'];
            $planId           = $resource['plan_id'] ?? null;
            $updateTime       = Carbon::parse($resource['update_time'] ?? now());
            $nextBillingTime  = Carbon::parse($resource['billing_info']['next_billing_time'] ?? now());

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update([
                        'updated_at' => $updateTime,
                        'next_billing_time' => $nextBillingTime
                    ]);

                    $this->sendSubscriptionNotification($user->id, 'Subscription Updated', 'Your subscription has been updated.');
                }
            }
        });
    }

    protected function handlePaymentCompleted($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId  = $resource['id'];
            $subscriptionId   = $resource['billing_agreement_id'] ?? null;
            $transactionDate  = Carbon::parse($resource['create_time'] ?? now());
            $amount           = $resource['amount']['total'] ?? 0;

            // Find user
            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                // userSubscription
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                if ($userSubscription) {
                    // جلب تفاصيل الاشتراك من باي بال
                    $paypalDetails = $this->getSubscriptionDetails($subscriptionId);
                    if (isset($paypalDetails['plan_id'])) {
                        $planId = $paypalDetails['plan_id'];
                        $dbSub = Subscription::where('paypal_plan_id', $planId)->first();
                        if ($dbSub) {
                            $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                            $payment->fill([
                                'user_subscription_id'   => $userSubscription->id,
                                'payment_type'         => 'paypal',
                                'user_id'                => $user->id,
                                'subscription_id'        => $dbSub->id,
                                'paypal_subscription_id' => $subscriptionId,
                                'payment_status'         => 'completed',
                                'transaction_date'       => $transactionDate,
                                'amount'                 => $amount,
                            ]);
                            $payment->save();

                            $this->sendSubscriptionNotification($user->id, 'Payment Completed', 'Your payment completed successfully.');
                        }
                    }
                }
            }
        });
    }

    protected function handlePaymentDenied($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId  = $resource['id'];
            $subscriptionId   = $resource['billing_agreement_id'] ?? null;
            $amount           = $resource['amount']['total'] ?? 0;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                if ($userSubscription) {
                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_type'       => 'paypal',
                        'user_id'              => $user->id,
                        'subscription_id'      => $userSubscription->subscription_id,
                        'payment_status'       => 'denied',
                        'transaction_date'     => Carbon::parse($resource['create_time'] ?? now()),
                        'amount'               => $amount
                    ]);
                    $payment->save();

                    $this->sendSubscriptionNotification($user->id, 'Payment Denied', 'Your payment has been denied.');
                }
            }
        });
    }

    protected function handlePaymentPending($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId  = $resource['billing_agreement_id'] ?? null;
            $amount          = $resource['amount']['total'] ?? 0;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                if ($userSubscription) {
                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_type'       => 'paypal',
                        'user_id'              => $user->id,
                        'subscription_id'      => $userSubscription->subscription_id,
                        'payment_status'       => 'pending',
                        'transaction_date'     => Carbon::parse($resource['create_time'] ?? now()),
                        'amount'               => $amount
                    ]);
                    $payment->save();

                    $this->sendSubscriptionNotification($user->id, 'Payment Pending', 'Your payment is pending.');
                }
            }
        });
    }

    protected function handlePaymentRefunded($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId  = $resource['billing_agreement_id'] ?? null;
            $amount          = $resource['amount']['total'] ?? 0;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                if ($userSubscription) {
                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_type'       => 'paypal',
                        'user_id'              => $user->id,
                        'subscription_id'      => $userSubscription->subscription_id,
                        'payment_status'       => 'refunded',
                        'transaction_date'     => Carbon::parse($resource['create_time'] ?? now()),
                        'amount'               => $amount
                    ]);
                    $payment->save();

                    $this->sendSubscriptionNotification($user->id, 'Payment Refunded', 'Your payment has been refunded.');
                }
            }
        });
    }

    protected function handlePaymentReversed($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId  = $resource['billing_agreement_id'] ?? null;
            $amount          = $resource['amount']['total'] ?? 0;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                if ($userSubscription) {
                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_type'       => 'paypal',
                        'user_id'              => $user->id,
                        'subscription_id'      => $userSubscription->subscription_id,
                        'payment_status'       => 'reversed',
                        'transaction_date'     => Carbon::parse($resource['create_time'] ?? now()),
                        'amount'               => $amount
                    ]);
                    $payment->save();

                    $this->sendSubscriptionNotification($user->id, 'Payment Reversed', 'Your payment has been reversed.');
                }
            }
        });
    }

    protected function handleRefundPending($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId  = $resource['billing_agreement_id'] ?? null;
            $amount          = $resource['amount']['total'] ?? 0;

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                if ($userSubscription) {
                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_type'       => 'paypal',
                        'user_id'              => $user->id,
                        'subscription_id'      => $userSubscription->subscription_id,
                        'payment_status'       => 'refund_pending',
                        'transaction_date'     => Carbon::parse($resource['create_time'] ?? now()),
                        'amount'               => $amount
                    ]);
                    $payment->save();

                    $this->sendSubscriptionNotification($user->id, 'Refund Pending', 'Your refund is pending.');
                }
            }
        });
    }

    private function getSubscriptionDetails($subscriptionId)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $subscriptionDetails = $provider->showSubscriptionDetails($subscriptionId);

            Log::channel('webhook-log')->info('Subscription details:', $subscriptionDetails);

            return $subscriptionDetails;
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error retrieving subscription details:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function sendSubscriptionNotification($userId, $title, $message)
    {
        try {
            $notification = Notification::create([
                'user_id'  => $userId,
                'title'    => $title,
                'message'  => $message,
                'icon'     => 'bx bx-credit-card',
                'type'     => 'subscription',
                'is_seen'  => false,
                'model_id' => 0,
            ]);
            event(new NotificationEvent($notification));
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error sending notification:', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString()
            ]);
        }
    }
}
