<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\User;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayPal Webhook Received:', $payload);

        if (!$this->verifyWebhookSignature($request)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
        }

        switch ($payload['event_type']) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                Log::channel('webhook')->info('Subscription activated:', $payload['resource']);
                $this->handleSubscriptionActivated($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.CREATED':
                Log::channel('webhook')->info('Subscription created:', $payload['resource']);
                $this->handleSubscriptionCreated($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                Log::channel('webhook')->info('Subscription cancelled:', $payload['resource']);
                $this->handleSubscriptionCancelled($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.EXPIRED':
                Log::channel('webhook')->info('Subscription expired:', $payload['resource']);
                $this->handleSubscriptionExpired($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
                Log::channel('webhook')->info('Subscription payment failed:', $payload['resource']);
                $this->handleSubscriptionPaymentFailed($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.RE-ACTIVATED':
                Log::channel('webhook')->info('Subscription reactivated:', $payload['resource']);
                $this->handleSubscriptionReactivated($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.SUSPENDED':
                Log::channel('webhook')->info('Subscription suspended:', $payload['resource']);
                $this->handleSubscriptionSuspended($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.UPDATED':
                Log::channel('webhook')->info('Subscription updated:', $payload['resource']);
                $this->handleSubscriptionUpdated($payload['resource']);
                break;
            case 'PAYMENT.SALE.COMPLETED':
                Log::channel('webhook')->info('Payment completed:', $payload['resource']);
                $this->handlePaymentCompleted($payload['resource']);
                break;
            case 'PAYMENT.SALE.DENIED':
                Log::channel('webhook')->info('Payment denied:', $payload['resource']);
                $this->handlePaymentDenied($payload['resource']);
                break;
            case 'PAYMENT.SALE.PENDING':
                Log::channel('webhook')->info('Payment pending:', $payload['resource']);
                $this->handlePaymentPending($payload['resource']);
                break;
            case 'PAYMENT.SALE.REFUNDED':
                Log::channel('webhook')->info('Payment refunded:', $payload['resource']);
                $this->handlePaymentRefunded($payload['resource']);
                break;
            case 'PAYMENT.SALE.REVERSED':
                Log::channel('webhook')->info('Payment reversed:', $payload['resource']);
                $this->handlePaymentReversed($payload['resource']);
                break;
            case 'PAYMENT.REFUND.PENDING':
                Log::channel('webhook')->info('Refund pending:', $payload['resource']);
                $this->handleRefundPending($payload['resource']);
                break;
            default:
                Log::channel('webhook')->info('Webhook event not handled:', $payload['event_type']);
                break;
        }

        return response()->json(['status' => 'success']);
    }

    // private function verifyWebhookSignature($request)
    // {
    //     $headers = $request->headers->all();

    //     $signatureVerification = $this->paypal->verifyWebhookSignature([
    //         'auth_algo' => $headers['paypal-auth-algo'][0],
    //         'cert_url' => $headers['paypal-cert-url'][0],
    //         'transmission_id' => $headers['paypal-transmission-id'][0],
    //         'transmission_sig' => $headers['paypal-transmission-sig'][0],
    //         'transmission_time' => $headers['paypal-transmission-time'][0],
    //         'webhook_id' => config('paypal.webhook_id'),
    //         'webhook_event' => $request->getContent(),
    //     ]);

    //     if ($signatureVerification['verification_status'] === 'SUCCESS') {
    //         return true;
    //     }

    //     Log::channel('webhook')->info('Invalid signature:', $signatureVerification);
    //     return false;
    // }
    private function verifyWebhookSignature($request)
    {
        // Convert headers to an array with case-insensitive keys
        $headers = array_change_key_case($request->headers->all(), CASE_LOWER);

        // Verify all necessary headers are present
        $requiredHeaders = ['paypal-auth-algo', 'paypal-cert-url', 'paypal-transmission-id', 'paypal-transmission-sig', 'paypal-transmission-time'];
        foreach ($requiredHeaders as $header) {
            if (!isset($headers[$header][0])) {
                Log::channel('webhook')->error("Missing required header: $header");
                return false;
            }
        }

        $signatureVerification = $this->paypal->verifyWebhookSignature([
            'auth_algo' => $headers['paypal-auth-algo'][0],
            'cert_url' => $headers['paypal-cert-url'][0],
            'transmission_id' => $headers['paypal-transmission-id'][0],
            'transmission_sig' => $headers['paypal-transmission-sig'][0],
            'transmission_time' => $headers['paypal-transmission-time'][0],
            'webhook_id' => config('paypal.webhook_id'),
            'webhook_event' => $request->getContent(),
        ]);

        if ($signatureVerification['verification_status'] === 'SUCCESS') {
            return true;
        }

        Log::channel('webhook')->info('Invalid signature:', $signatureVerification);
        return false;
    }

    // Subscription Events Handlers
    protected function handleSubscriptionActivated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];
            $startTime = $resource['start_time'];
            $nextBillingTime = $resource['billing_info']['next_billing_time'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $user->student->subscription_status = 'subscribed';
                $user->save();

                $userSubscription = UserSubscription::updateOrCreate(
                    ['user_id' => $user->id, 'subscription_id' => $planId],
                    ['status' => 'active', 'start_date' => $startTime, 'next_billing_time' => $nextBillingTime]
                );
            }
        });
    }

    protected function handleSubscriptionCreated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];
            $startTime = $resource['start_time'];
            $subscriberEmail = $resource['subscriber']['email_address'];

            $user = User::where('email', $subscriberEmail)->first();
            if ($user) {
                $user->update(['paypal_subscription_id' => $subscriptionId]);

                UserSubscription::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'subscription_id' => $planId,
                    ],
                    [
                        'status' => 'created',
                        'start_date' => $startTime
                    ]
                );
            }
        });
    }


    protected function handleSubscriptionCancelled($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $user->student->subscription_status = 'cancelled';
                $user->save();

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'cancelled']);
                }
            }
        });
    }

    protected function handleSubscriptionExpired($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $user->student->subscription_status = 'expired';
                $user->save();

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'expired']);
                }
            }
        });
    }

    protected function handleSubscriptionPaymentFailed($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['billing_agreement_id'];
            $paypalPaymentId = $resource['id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
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
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }


    protected function handleSubscriptionReactivated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];
            $nextBillingTime = $resource['billing_info']['next_billing_time'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $user->student->subscription_status = 'subscribed';
                $user->save();

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'active', 'next_billing_time' => $nextBillingTime]);
                }
            }
        });
    }

    protected function handleSubscriptionSuspended($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $user->student->subscription_status = 'suspended';
                $user->save();

                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['status' => 'suspended']);
                }
            }
        });
    }

    protected function handleSubscriptionUpdated($resource)
    {
        DB::transaction(function () use ($resource) {
            $subscriptionId = $resource['id'];
            $planId = $resource['plan_id'];
            $updateTime = $resource['update_time'];
            $nextBillingTime = $resource['billing_info']['next_billing_time'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)
                    ->where('subscription_id', $planId)
                    ->first();

                if ($userSubscription) {
                    $userSubscription->update(['updated_at' => $updateTime, 'next_billing_time' => $nextBillingTime]);
                }
            }
        });
    }

    // Payment Events Handlers
    protected function handlePaymentCompleted($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId = $resource['billing_agreement_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $subscription_id = Subscription::where('paypal_plan_id', $userSubscription->subscription_id)->first()->id;
                    // Fetch subscription details from PayPal
                    $subscriptionDetails = $this->getSubscriptionDetails($subscriptionId);

                    if ($subscriptionDetails && isset($subscriptionDetails['billing_info']['next_billing_time'])) {
                        $nextBillingTime = $subscriptionDetails['billing_info']['next_billing_time'];

                        $userSubscription->update(['next_billing_time' => $nextBillingTime]);
                    }

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_method' => 'paypal',
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_id,
                        'payment_status' => 'completed',
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }

    protected function handlePaymentDenied($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId = $resource['billing_agreement_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $subscription_id = Subscription::where('paypal_plan_id', $userSubscription->subscription_id)->first()->id;
                    // Fetch subscription details from PayPal
                    $subscriptionDetails = $this->getSubscriptionDetails($subscriptionId);

                    if ($subscriptionDetails && isset($subscriptionDetails['billing_info']['next_billing_time'])) {
                        $nextBillingTime = $subscriptionDetails['billing_info']['next_billing_time'];

                        $userSubscription->update(['next_billing_time' => $nextBillingTime]);
                    }

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_method' => 'paypal',
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_id,
                        'payment_status' => 'denied',
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }

    protected function handlePaymentPending($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId = $resource['billing_agreement_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $subscription_id = Subscription::where('paypal_plan_id', $userSubscription->subscription_id)->first()->id;
                    // Fetch subscription details from PayPal
                    $subscriptionDetails = $this->getSubscriptionDetails($subscriptionId);

                    if ($subscriptionDetails && isset($subscriptionDetails['billing_info']['next_billing_time'])) {
                        $nextBillingTime = $subscriptionDetails['billing_info']['next_billing_time'];

                        $userSubscription->update(['next_billing_time' => $nextBillingTime]);
                    }

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_method' => 'paypal',
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_id,
                        'payment_status' => 'pending',
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }

    protected function handlePaymentRefunded($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId = $resource['billing_agreement_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $subscription_id = Subscription::where('paypal_plan_id', $userSubscription->subscription_id)->first()->id;
                    // Fetch subscription details from PayPal
                    $subscriptionDetails = $this->getSubscriptionDetails($subscriptionId);

                    if ($subscriptionDetails && isset($subscriptionDetails['billing_info']['next_billing_time'])) {
                        $nextBillingTime = $subscriptionDetails['billing_info']['next_billing_time'];

                        $userSubscription->update(['next_billing_time' => $nextBillingTime]);
                    }

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_method' => 'paypal',
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_id,
                        'payment_status' => 'refunded',
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }

    protected function handlePaymentReversed($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId = $resource['billing_agreement_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $subscription_id = Subscription::where('paypal_plan_id', $userSubscription->subscription_id)->first()->id;
                    // Fetch subscription details from PayPal
                    $subscriptionDetails = $this->getSubscriptionDetails($subscriptionId);

                    if ($subscriptionDetails && isset($subscriptionDetails['billing_info']['next_billing_time'])) {
                        $nextBillingTime = $subscriptionDetails['billing_info']['next_billing_time'];

                        $userSubscription->update(['next_billing_time' => $nextBillingTime]);
                    }

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_method' => 'paypal',
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_id,
                        'payment_status' => 'reversed',
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }

    protected function handleRefundPending($resource)
    {
        DB::transaction(function () use ($resource) {
            $paypalPaymentId = $resource['id'];
            $subscriptionId = $resource['billing_agreement_id'];

            $user = User::where('paypal_subscription_id', $subscriptionId)->first();
            if ($user) {
                $userSubscription = UserSubscription::where('user_id', $user->id)->first();

                if ($userSubscription) {
                    $subscription_id = Subscription::where('paypal_plan_id', $userSubscription->subscription_id)->first()->id;
                    // Fetch subscription details from PayPal
                    $subscriptionDetails = $this->getSubscriptionDetails($subscriptionId);

                    if ($subscriptionDetails && isset($subscriptionDetails['billing_info']['next_billing_time'])) {
                        $nextBillingTime = $subscriptionDetails['billing_info']['next_billing_time'];

                        $userSubscription->update(['next_billing_time' => $nextBillingTime]);
                    }

                    $payment = Payment::firstOrNew(['paypal_payment_id' => $paypalPaymentId]);
                    $payment->fill([
                        'user_subscription_id' => $userSubscription->id,
                        'payment_method' => 'paypal',
                        'user_id' => $user->id,
                        'subscription_id' => $subscription_id,
                        'payment_status' => 'refund_pending',
                        'transaction_date' => $resource['create_time'],
                        'amount' => $resource['amount']['total']
                    ]);
                    $payment->save();
                }
            }
        });
    }


    // Helper Functions
    private function getSubscriptionDetails($subscriptionId)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $subscriptionDetails = $provider->showSubscriptionDetails($subscriptionId);
        Log::channel('webhook')->info('Subscription details:', $subscriptionDetails);

        return $subscriptionDetails;
    }
}