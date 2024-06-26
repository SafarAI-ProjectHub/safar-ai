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
    public function showSubscriptionDetails($planId)
    {
        $planDetails = Subscription::find($planId);

        if (!$planDetails) {
            return redirect()->back()->withErrors('Plan not found.');
        }

        if (!$planDetails->is_active) {
            return redirect()->back()->withErrors('Plan is not active.');
        }

        return view('dashboard.student.subscription_details', compact('planDetails'));
    }


    public function create(Request $request)
    {
        $user = Auth::user();
        $planId = $request->input('plan_id');
        $email = $request->input('email');
        $subscription_id = Subscription::where('paypal_plan_id', $planId)->first()->id;




        $userSubscription = UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $planId,
            'status' => 'inactive',
        ]);

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
}