<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Payment;
use App\Models\User;

class PayPalWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('PayPal Webhook Received:', $payload);

        switch ($payload['event_type']) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->handleSubscriptionActivated($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.RENEWED':
                $this->handleSubscriptionRenewed($payload['resource']);
                break;
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($payload['resource']);
                break;
            case 'PAYMENT.SALE.FAILED':
                $this->handlePaymentFailed($payload['resource']);
                break;
            default:
                Log::info('Unhandled event type:', $payload['event_type']);
        }

        return response()->json(['status' => 'success']);
    }

    protected function handleSubscriptionActivated($resource)
    {
        $subscriptionId = $resource['id'];
        $planId = $resource['plan_id'];
        $startTime = $resource['billing_info']['last_payment']['time'];
        $nextBillingTime = $resource['billing_info']['next_billing_time'];

        // Find the user by PayPal subscription ID
        $user = User::where('paypal_subscription_id', $subscriptionId)->first();
        if ($user) {
            // Update user's PayPal payer ID and subscription status
            $user->student->subscription_status = 'subscribed';
            $user->save();

            // Find the user subscription entry
            $userSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_id', $planId)
                ->first();
            if ($userSubscription) {
                $userSubscription->status = 'active';
                $userSubscription->start_date = $startTime;
                $userSubscription->next_billing_time = $nextBillingTime;
                $userSubscription->save();
            }

            // Update payment status
            $payment = Payment::where('user_subscription_id', $userSubscription->id)->first();
            if ($payment) {
                $payment->payment_status = 'completed';
                $payment->amount = $resource['billing_info']['last_payment']['amount']['value'];
                $payment->save();
            }
        }
    }

    protected function handleSubscriptionRenewed($resource)
    {
        $subscriptionId = $resource['id'];
        $planId = $resource['plan_id'];
        $nextBillingTime = $resource['billing_info']['next_billing_time'];

        // Find the user by PayPal subscription ID
        $user = User::where('paypal_subscription_id', $subscriptionId)->first();
        if ($user) {
            // Find the user subscription entry
            $userSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_id', $planId)
                ->first();
            if ($userSubscription) {
                $userSubscription->next_billing_time = $nextBillingTime;
                $userSubscription->save();
            }
        }
    }


    protected function handleSubscriptionCancelled($resource)
    {
        $subscriptionId = $resource['id'];
        $planId = $resource['plan_id'];

        // Find the user by PayPal subscription ID
        $user = User::where('paypal_subscription_id', $subscriptionId)->first();
        if ($user) {
            // Update user's subscription status
            $user->subscription_status = 'cancelled';
            $user->save();

            // Find the user subscription entry
            $userSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_id', $planId)
                ->first();
            if ($userSubscription) {
                $userSubscription->status = 'cancelled';
                $userSubscription->save();
            }
        }
    }


    protected function handlePaymentFailed($resource)
    {
        $subscriptionId = $resource['billing_agreement_id'];
        $planId = $resource['plan_id'];

        // Find the user by PayPal subscription ID
        $user = User::where('paypal_subscription_id', $subscriptionId)->first();
        if ($user) {
            // Find the user subscription entry
            $userSubscription = UserSubscription::where('user_id', $user->id)
                ->where('subscription_id', $planId)
                ->first();
            if ($userSubscription) {
                // Update payment status to failed
                $payment = Payment::where('user_subscription_id', $userSubscription->id)->first();
                if ($payment) {
                    $payment->payment_status = 'failed';
                    $payment->save();
                }
            }
        }
    }



}