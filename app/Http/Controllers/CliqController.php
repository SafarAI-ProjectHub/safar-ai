<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use Carbon\Carbon;

class CliqController extends Controller
{
    public function payWithCliq(Request $request)
    {
        $request->validate([
            'userName' => 'required|string|max:255',
            'proofOfPayment' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('proofOfPayment')->store('payments', 'public');

        // Save the payment information in the database
        $payment = Payment::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'user_name' => $request->userName,
                'proof_of_payment' => $path,
                'status' => 'pending', // Initial status as pending
                'rejection_reason' => null,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function approvePayment($id)
    {
        $payment = Payment::find($id);
        if ($payment) {
            $payment->status = 'approved';
            $payment->approved_at = now();
            $payment->save();

            // Update or create the user's subscription
            $userSubscription = UserSubscription::firstOrNew(['user_id' => $payment->user_id, 'type' => 'cliq']);
            if ($userSubscription->exists) {
                if ($userSubscription->next_billing_time > now()) {
                    $userSubscription->next_billing_time = Carbon::parse($userSubscription->next_billing_time)->addMonths(1);
                } else {
                    $userSubscription->next_billing_time = now()->addMonths(1);
                }
            } else {
                $userSubscription->next_billing_time = now()->addMonths(2);
            }
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
            $payment->status = 'rejected';
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

        if ($payment && $payment->status == 'rejected') {
            // Store the new payment proof
            $path = $request->file('proofOfPayment')->store('payments', 'public');

            // Update the payment record
            $payment->user_name = $request->userName;
            $payment->proof_of_payment = $path;
            $payment->status = 'pending'; // Reset status to pending
            $payment->rejection_reason = null; // Clear rejection reason
            $payment->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Payment record not found or not eligible for re-upload'], 404);
    }
}