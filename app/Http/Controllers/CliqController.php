<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\Student; 

class CliqController extends Controller
{
    public function payWithCliq(Request $request, $action = 'initial')
    {
        $request->validate([
            'userName' => 'required|string|max:255',
            'payment_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('payment_image')->store('payments', 'public');
        $path = 'storage/' . $path;

        $subscription = Subscription::where('is_active', true)->first();


        $userSubscription = UserSubscription::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'subscription_id' => 'cliq-' . auth()->id(),
                'status' => 'inactive',

            ]
        );

        // Create a new payment record for extension
        $payment = Payment::create([
            'user_id' => auth()->id(),
            'payment_image' => $path,
            'payment_type' => 'cliq',
            'status' => 'pending',
            'subscription_id' => 0,
            'amount' => $subscription->price,
            'transaction_date' => now(),
            'user_subscription_id' => $userSubscription->id,
            'rejection_reason' => null,
        ]);

        return response()->json(['success' => true]);
    }

    public function approvePayment($id)
    {
        $payment = Payment::find($id);
        if ($payment) {
            $payment->payment_status = 'completed';
            $payment->save();

            $student = Student::where('student_id', $payment->user_id)->first();
            $student->subscription_status = 'subscribed';

            // Update or create the user's subscription
            $userSubscription = UserSubscription::firstOrNew(['user_id' => $payment->user_id]);
            if ($userSubscription->exists) {
                if ($userSubscription->next_billing_time > now()) {
                    $userSubscription->next_billing_time = Carbon::parse($userSubscription->next_billing_time)->addMonths(1);
                } else {
                    $userSubscription->next_billing_time = now()->addMonths(1);
                }
            } else {
                $userSubscription->next_billing_time = now()->addMonths(1);
                $userSubscription->subscription_id = 'cliq-' . $payment->user_id;
                $userSubscription->user_id = $payment->user_id;
                $userSubscription->start_date = now();
            }
            $userSubscription->status = 'active';
            $userSubscription->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $payment = Payment::find($id);
        if ($payment) {
            $payment->payment_status = 'rejected';
            $payment->rejection_reason = $request->reason;
            $payment->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

    public function reuploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'userName' => 'required|string|max:255',
            'payment_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $payment = Payment::find($id);

        if ($payment && $payment->payment_status == 'rejected') {
            // Store the new payment proof
            $path = $request->file('payment_image')->store('payments', 'public');
            $path = 'storage/' . $path;
            // Update the payment record
            $payment->payment_image = $path;
            $payment->payment_status = 'pending'; // Reset status to pending
            $payment->rejection_reason = null; // Clear rejection reason
            $payment->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Payment record not found or not eligible for re-upload'], 404);
    }
    public function showPendingPayments()
    {
        $pendingPayments = Payment::where('payment_type', 'cliq')
            ->where('payment_status', 'pending')
            ->with('user')
            ->get();

        return view('dashboard.admin.subscriptions.cliq_payments', compact('pendingPayments'));
    }
}