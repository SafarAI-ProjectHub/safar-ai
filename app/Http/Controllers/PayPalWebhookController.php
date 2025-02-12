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
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.ACTIVATED:', $payload['resource']);
                    $this->handleSubscriptionActivated($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.CANCELLED':
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.CANCELLED:', $payload['resource']);
                    $this->handleSubscriptionCancelled($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.EXPIRED':
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.EXPIRED:', $payload['resource']);
                    $this->handleSubscriptionExpired($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.PAYMENT.FAILED:', $payload['resource']);
                    $this->handleSubscriptionPaymentFailed($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.RE-ACTIVATED:', $payload['resource']);
                    $this->handleSubscriptionReactivated($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.SUSPENDED':
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.SUSPENDED:', $payload['resource']);
                    $this->handleSubscriptionSuspended($payload['resource']);
                    break;
                case 'BILLING.SUBSCRIPTION.UPDATED':
                    Log::channel('webhook-log')->info('BILLING.SUBSCRIPTION.UPDATED:', $payload['resource']);
                    $this->handleSubscriptionUpdated($payload['resource']);
                    break;
                case 'PAYMENT.SALE.COMPLETED':
                    Log::channel('webhook-log')->info('PAYMENT.SALE.COMPLETED:', $payload['resource']);
                    $this->handlePaymentCompleted($payload['resource']);
                    break;
                case 'PAYMENT.SALE.DENIED':
                    Log::channel('webhook-log')->info('PAYMENT.SALE.DENIED:', $payload['resource']);
                    $this->handlePaymentDenied($payload['resource']);
                    break;
                case 'PAYMENT.SALE.PENDING':
                    Log::channel('webhook-log')->info('PAYMENT.SALE.PENDING:', $payload['resource']);
                    $this->handlePaymentPending($payload['resource']);
                    break;
                case 'PAYMENT.SALE.REFUNDED':
                    Log::channel('webhook-log')->info('PAYMENT.SALE.REFUNDED:', $payload['resource']);
                    $this->handlePaymentRefunded($payload['resource']);
                    break;
                case 'PAYMENT.SALE.REVERSED':
                    Log::channel('webhook-log')->info('PAYMENT.SALE.REVERSED:', $payload['resource']);
                    $this->handlePaymentReversed($payload['resource']);
                    break;
                case 'PAYMENT.REFUND.PENDING':
                    Log::channel('webhook-log')->info('PAYMENT.REFUND.PENDING:', $payload['resource']);
                    $this->handleRefundPending($payload['resource']);
                    break;
                default:
                    Log::channel('webhook-log')->info('Webhook event not handled:', ['event_type' => $payload['event_type']]);
                    break;
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error processing webhook:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal Server Error'], 500);
        }
    }

    protected function handleSubscriptionActivated($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['id'];
                $planId = $resource['plan_id'];
                $startTime = Carbon::parse($resource['start_time']);
                $nextBillingTime = Carbon::parse($resource['billing_info']['next_billing_time']);

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    Student::where('student_id', $user->id)->update(['subscription_status' => 'subscribed']);
                    Log::channel('webhook-log')->info('User found:', ['user_id' => $user->id]);

                    UserSubscription::updateOrCreate(
                        ['user_id' => $user->id, 'subscription_id' => $planId],
                        ['status' => 'active', 'start_date' => $startTime, 'next_billing_time' => $nextBillingTime]
                    );

                    // Send notification
                    $this->sendSubscriptionNotification($user->id, 'Subscription Activated', 'Your subscription has been activated.', 'bx bx-credit-card');
                    event(new SubscriptionEvent($user->id, 'activated', 'Your subscription has been activated.'));
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription activation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleSubscriptionCancelled($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['id'];
                $planId = $resource['plan_id'];

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

                    // Send notification
                    $this->sendSubscriptionNotification($user->id, 'Subscription Cancelled', 'Your subscription has been cancelled.', 'bx bx-credit-card');
                    event(new SubscriptionEvent($user->id, 'cancelled', 'Your subscription has been cancelled.'));
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription cancellation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleSubscriptionExpired($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['id'];
                $planId = $resource['plan_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    Student::where('student_id', $user->id)->update(['subscription_status' => 'expired']);

                    $userSubscription = UserSubscription::where('user_id', $user->id)
                        ->where('subscription_id', $planId)
                        ->first();

                    if ($userSubscription) {
                        $userSubscription->update(['status' => 'expired']);
                    }

                    // Send notification
                    $this->sendSubscriptionNotification($user->id, 'Subscription Expired', 'Your subscription has expired.', 'bx bx-credit-card');
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription expiration:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleSubscriptionPaymentFailed($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['billing_agreement_id'];
                $paypalPaymentId = $resource['id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    Student::where('student_id', $user->id)->update(['subscription_status' => 'failed']);
                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                    if ($userSubscription) {
                        $userSubscription->update(['status' => 'failed']);

                        $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                        $payment->fill([
                            'user_subscription_id' => $userSubscription->id,
                            'payment_method' => 'paypal',
                            'user_id' => $user->id,
                            'subscription_id' => $userSubscription->subscription_id,
                            'payment_status' => 'failed',
                            'transaction_date' => Carbon::parse($resource['create_time']),
                            'amount' => $resource['amount']['total']
                        ]);
                        $payment->save();

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Payment Failed', 'Your subscription payment has failed.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription payment failure:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleSubscriptionReactivated($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['id'];
                $planId = $resource['plan_id'];
                $nextBillingTime = Carbon::parse($resource['billing_info']['next_billing_time']);

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    Student::where('student_id', $user->id)->update(['subscription_status' => 'subscribed']);

                    $userSubscription = UserSubscription::where('user_id', $user->id)
                        ->where('subscription_id', $planId)
                        ->first();

                    if ($userSubscription) {
                        $userSubscription->update(['status' => 'active', 'next_billing_time' => $nextBillingTime]);

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Subscription Reactivated', 'Your subscription has been reactivated.', 'bx bx-credit-card');
                        event(new SubscriptionEvent($user->id, 'reactivated', 'Your subscription has been reactivated.'));
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription reactivation:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleSubscriptionSuspended($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['id'];
                $planId = $resource['plan_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    Student::where('student_id', $user->id)->update(['subscription_status' => 'suspended']);

                    $userSubscription = UserSubscription::where('user_id', $user->id)
                        ->where('subscription_id', $planId)
                        ->first();

                    if ($userSubscription) {
                        $userSubscription->update(['status' => 'suspended']);

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Subscription Suspended', 'Your subscription has been suspended.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription suspension:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleSubscriptionUpdated($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $subscriptionId = $resource['id'];
                $planId = $resource['plan_id'];
                $updateTime = Carbon::parse($resource['update_time']);
                $nextBillingTime = Carbon::parse($resource['billing_info']['next_billing_time']);

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    $userSubscription = UserSubscription::where('user_id', $user->id)
                        ->where('subscription_id', $planId)
                        ->first();

                    if ($userSubscription) {
                        $userSubscription->update(['updated_at' => $updateTime, 'next_billing_time' => $nextBillingTime]);

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Subscription Updated', 'Your subscription has been updated.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling subscription update:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handlePaymentCompleted($resource)
    {
        try {
            Log::channel('webhook-log')->info('Handling payment completion:', $resource);

            // Start database transaction
            DB::transaction(function () use ($resource) {
                Log::channel('webhook-log')->info('Transaction date:');

                $paypalPaymentId = $resource['id'];
                $subscriptionId = $resource['billing_agreement_id'];
                $transactionDate = Carbon::parse($resource['create_time']);
                $amount = $resource['amount']['total'];

                // Log extracted data
                Log::channel('webhook-log')->info('Extracted data:', [
                    'paypalPaymentId' => $paypalPaymentId,
                    'subscriptionId' => $subscriptionId,
                    'transactionDate' => $transactionDate,
                    'amount' => $amount,
                ]);

                // Find the user by PayPal subscription ID
                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    Log::channel('webhook-log')->info('User found:', ['user_id' => $user->id]);

                    // Find the user subscription
                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();
                    if ($userSubscription) {
                        Log::channel('webhook-log')->info('User subscription found:' . $userSubscription->id);

                        // Get the plan ID
                        $plan_id = $this->getSubscriptionDetails($subscriptionId)['plan_id'];

                        $subscriptionId = Subscription::where('paypal_plan_id', $plan_id)->first()->id;
                        if ($subscriptionId) {
                            // Find or create the payment record
                            $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                            $payment->fill([
                                'user_subscription_id' => $userSubscription->id,
                                'payment_method' => 'paypal',
                                'user_id' => $user->id,
                                'subscription_id' => $subscriptionId,
                                'paypal_subscription_id' => $userSubscription->subscription_id,
                                'payment_status' => 'completed',
                                'transaction_date' => $transactionDate,
                                'amount' => $amount,
                            ]);
                            $payment->save();

                            Log::channel('webhook-log')->info('Payment saved:', $payment->toArray());

                            // Send notification
                            $this->sendSubscriptionNotification($user->id, 'Payment Completed', 'Your payment has been completed successfully.', 'bx bx-credit-card');
                        } else {
                            Log::channel('webhook-log')->warning('Subscription not found for PayPal plan ID:', ['planId' => $plan_id]);
                        }
                    } else {
                        Log::channel('webhook-log')->warning('User subscription not found:', ['user_id' => $user->id]);
                    }
                } else {
                    Log::channel('webhook-log')->warning('User not found for PayPal subscription ID:', ['subscriptionId' => $subscriptionId]);
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling payment completion:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handlePaymentDenied($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $paypalPaymentId = $resource['id'];
                $subscriptionId = $resource['billing_agreement_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                    if ($userSubscription) {
                        $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                        $payment->fill([
                            'user_subscription_id' => $userSubscription->id,
                            'payment_method' => 'paypal',
                            'user_id' => $user->id,
                            'subscription_id' => $userSubscription->subscription_id,
                            'payment_status' => 'denied',
                            'transaction_date' => Carbon::parse($resource['create_time']),
                            'amount' => $resource['amount']['total']
                        ]);
                        $payment->save();

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Payment Denied', 'Your payment has been denied.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling payment denial:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handlePaymentPending($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $paypalPaymentId = $resource['id'];
                $subscriptionId = $resource['billing_agreement_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                    if ($userSubscription) {
                        $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                        $payment->fill([
                            'user_subscription_id' => $userSubscription->id,
                            'payment_method' => 'paypal',
                            'user_id' => $user->id,
                            'subscription_id' => $userSubscription->subscription_id,
                            'payment_status' => 'pending',
                            'transaction_date' => Carbon::parse($resource['create_time']),
                            'amount' => $resource['amount']['total']
                        ]);
                        $payment->save();

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Payment Pending', 'Your payment is pending.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling pending payment:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handlePaymentRefunded($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $paypalPaymentId = $resource['id'];
                $subscriptionId = $resource['billing_agreement_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                    if ($userSubscription) {
                        $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                        $payment->fill([
                            'user_subscription_id' => $userSubscription->id,
                            'payment_method' => 'paypal',
                            'user_id' => $user->id,
                            'subscription_id' => $userSubscription->subscription_id,
                            'payment_status' => 'refunded',
                            'transaction_date' => Carbon::parse($resource['create_time']),
                            'amount' => $resource['amount']['total']
                        ]);
                        $payment->save();

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Payment Refunded', 'Your payment has been refunded.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling refunded payment:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handlePaymentReversed($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $paypalPaymentId = $resource['id'];
                $subscriptionId = $resource['billing_agreement_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                    if ($userSubscription) {
                        $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                        $payment->fill([
                            'user_subscription_id' => $userSubscription->id,
                            'payment_method' => 'paypal',
                            'user_id' => $user->id,
                            'subscription_id' => $userSubscription->subscription_id,
                            'payment_status' => 'reversed',
                            'transaction_date' => Carbon::parse($resource['create_time']),
                            'amount' => $resource['amount']['total']
                        ]);
                        $payment->save();

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Payment Reversed', 'Your payment has been reversed.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling reversed payment:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function handleRefundPending($resource)
    {
        try {
            DB::transaction(function () use ($resource) {
                $paypalPaymentId = $resource['id'];
                $subscriptionId = $resource['billing_agreement_id'];

                $user = User::where('paypal_subscription_id', $subscriptionId)->first();
                if ($user) {
                    $SUBSCRIPTION = $this->getSubscriptionDetails($subscriptionId);

                    $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                    if ($userSubscription) {
                        $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                        $payment->fill([
                            'user_subscription_id' => $userSubscription->id,
                            'payment_method' => 'paypal',
                            'user_id' => $user->id,
                            'subscription_id' => $userSubscription->subscription_id,
                            'payment_status' => 'refund_pending',
                            'transaction_date' => Carbon::parse($resource['create_time']),
                            'amount' => $resource['amount']['total']
                        ]);
                        $payment->save();

                        // Send notification
                        $this->sendSubscriptionNotification($user->id, 'Refund Pending', 'Your refund is pending.', 'bx bx-credit-card');
                    }
                }
            });
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error handling pending refund:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    // Helper Functions
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
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    private function sendSubscriptionNotification($userId, $title, $message, $icon)
    {
        try {
            $notification = Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'icon' => $icon,
                'type' => 'subscription',
                'is_seen' => false,
                'model_id' => 0,
                'reminder' => false,
                'reminder_time' => null,
            ]);

            event(new NotificationEvent($notification));
        } catch (\Exception $e) {
            Log::channel('webhook-log')->error('Error sending notification:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}