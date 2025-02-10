<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
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
    public function showSubscriptionDetails()
    {
        $user = Auth::user();

        if ($user->getAgeGroup() == '1-5') {
            return redirect()->route('student.dashboard')->with('error', 'You are not eligible to subscribe to any plan.');
        }

        $subscription = UserSubscription::where('user_id', $user->id)->first();

        if ($subscription) {
            if ($subscription->status == 'active' || $subscription->status == 'suspend') {
                if ($subscription->subscription_id == 'cliq-' . $user->id) {
                    $planDetails = Subscription::where('id', $subscription->subscriptionId)->first();
                    $payment = Payment::where('user_id', $user->id)->latest()->first();
                } else {
                    $planDetails = Subscription::where('paypal_plan_id', $subscription->subscription_id)->first();
                    $payment = Payment::where('user_id', $user->id)->latest()->first();
                }
            } else {
                $planDetails = Subscription::where('is_active', true)->first();
                $payment = Payment::where('user_id', $user->id)->latest()->first();
            }
        } else {
            $planDetails = Subscription::where('is_active', true)->first();
            $payment = new Payment();
            $subscription = new UserSubscription();
        }

        $otherPlan = Subscription::where('is_active', true)
            ->where('subscription_type', '!=', $planDetails->subscription_type)
            ->first();
        // dd($otherPlan);
        $cliqUserName = config('cliq.username');
        $activePlan = Subscription::where('is_active', true)->first();
        $monthlyActivePlan = Subscription::where('is_active', true)->where('subscription_type', 'monthly')->first();
        $yearlyActivePlan = Subscription::where('is_active', true)->where('subscription_type', 'yearly')->first();

        return view('dashboard.student.subscription_details', compact('planDetails', 'subscription', 'cliqUserName', 'payment', 'otherPlan', 'monthlyActivePlan', 'yearlyActivePlan', 'activePlan'));
    }





    public function create(Request $request)
    {
        $user = Auth::user();
        $planId = $request->input('plan_id');
        $email = $request->input('email');
        $subscription_id = Subscription::where('paypal_plan_id', $planId)->first()->id;



        // Check if user already has an active subscription based on user_id and subscription_id

        $userSubscription = UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            ['subscription_id' => $planId, 'status' => 'inactive', 'subscriptionId' => $subscription_id]
        );

        // Create PayPal subscription
        $customId = $user->id;
        $subscriptionResponse = $this->paypalService->createSubscription($planId, $email, $customId);

        // Log the full response for debugging
        \Log::info('PayPal Subscription Response:', $subscriptionResponse);

        if (isset($subscriptionResponse['status']) && $subscriptionResponse['status'] == 'APPROVAL_PENDING') {
            $approvalUrl = collect($subscriptionResponse['links'])->where('rel', 'approve')->first()['href'];
            $user->paypal_subscription_id = $subscriptionResponse['id'];
            $user->save();

            return response()->json(['success' => true, 'approval_url' => $approvalUrl]);
        } else {
            // Log error details
            \Log::error('Failed to create PayPal Subscription:', $subscriptionResponse);

            return response()->json(['success' => false, 'message' => 'Failed to create subscription.']);
        }
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


    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscriptionId = $user->paypal_subscription_id;
        $response = $this->paypalService->cancelSubscription($subscriptionId);


        UserSubscription::where('user_id', $user->id)->update(['status' => 'cancelled']);
        $user->student->subscription_status = 'cancelled';
        $user->student->save();
        return response()->json(['success' => true, 'message' => 'Subscription cancelled successfully.']);

    }

    public function reactivate(Request $request)
    {
        $user = Auth::user();
        $subscriptionId = $user->paypal_subscription_id;
        $response = $this->paypalService->reactivateSubscription($subscriptionId);

        Log::info('PayPal Subscription Reactivation Response:', $response);

        if (isset($response['status']) && $response['status'] == 'ACTIVE') {
            UserSubscription::where('user_id', $user->id)->update(['status' => 'active']);
            $user->student->subscription_status = 'subscribed';
            $user->student->save();
            return response()->json(['success' => true, 'message' => 'Subscription reactivated successfully.']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to reactivate subscription.']);
        }
    }
}