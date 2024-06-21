<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\UserSubscription;
use Auth;

class SubscriptionController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }



    public function create(Request $request)
    {
        $user = Auth::user();
        $planId = $request->input('plan_id');
        $email = $request->input('email');

        // Create a pending payment record
        $payment = new Payment();
        $payment->subscription_id = $planId;
        $payment->paypal_subscription_id = null;
        $payment->user_id = $user->id;
        $payment->amount = 0; // Amount will be updated upon PayPal approval
        $payment->payment_status = 'pending';
        $payment->payment_type = 'paypal';
        $payment->transaction_date = now();
        $payment->save();

        // Create a pending user subscription record
        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $planId,
            'status' => 'inactive',  // Default status is inactive until webhook updates it
        ]);

        // Create PayPal subscription
        $subscriptionResponse = $this->paypalService->createSubscription($planId, $email);

        // Log the full response for debugging
        \Log::info('PayPal Subscription Response:', $subscriptionResponse);

        if (isset($subscriptionResponse['status']) && $subscriptionResponse['status'] == 'APPROVAL_PENDING') {
            $approvalUrl = collect($subscriptionResponse['links'])->where('rel', 'approve')->first()['href'];

            // Update payment record with PayPal subscription ID
            $payment->paypal_subscription_id = $subscriptionResponse['id'];
            $payment->save();

            return response()->json(['success' => true, 'approval_url' => $approvalUrl]);
        } else {
            // Log error details
            \Log::error('Failed to create PayPal Subscription:', $subscriptionResponse);

            return response()->json(['success' => false, 'message' => 'Failed to create subscription.']);
        }
    }


    public function handleWebhook(Request $request)
    {
        $payload = $request->all();

        // Validate the webhook notification

        if ($payload['event_type'] === 'BILLING.SUBSCRIPTION.ACTIVATED') {
            $subscriptionId = $payload['resource']['id'];
            $userId = $payload['resource']['custom_id'];

            // Update the subscription and user status
            $subscription = Subscription::where('paypal_subscription_id', $subscriptionId)->first();
            if ($subscription) {
                $subscription->is_active = true;
                $subscription->save();

                $user = $subscription->user;
                $user->student->subscription_status = 'subscribed';
                $user->student->save();

                // Update user subscription entry
                $userSubscription = UserSubscription::where('user_id', $userId)->where('subscription_id', $subscription->id)->first();
                if ($userSubscription) {
                    $userSubscription->status = 'active';
                    $userSubscription->save();
                }

                // Update payment status
                $payment = Payment::where('subscription_id', $subscription->id)->where('user_id', $userId)->first();
                if ($payment) {
                    $payment->payment_status = 'completed';
                    $payment->save();
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function handleReturn(Request $request)
    {
        // Handle PayPal return
        return redirect()->route('student.dashboard')->with('success', 'Subscription successful.');
    }

    public function handleCancel(Request $request)
    {
        // Handle PayPal cancel
        return redirect()->route('student.dashboard')->with('error', 'Subscription cancelled.');
    }
}