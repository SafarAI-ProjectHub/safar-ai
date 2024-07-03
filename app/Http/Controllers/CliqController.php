<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Notification;
use App\Events\NotificationEvent;
use Carbon\Carbon;
use App\Models\Subscription;
use App\Models\Student;

class CliqController extends Controller
{
    public function payWithCliq(Request $request, $action = 'initial')
    {
        $customMessages = [
            'userName.required' => 'The user name is required.',
            'userName.string' => 'The user name must be a string.',
            'userName.max' => 'The user name may not be greater than 255 characters.',
            'payment_image.required' => 'A payment image is required.',
            'payment_image.image' => 'The payment image must be an image.',
            'payment_image.mimes' => 'The payment image must be a file of type: jpeg, png, jpg, gif.',
            'payment_image.max' => 'The payment image may not be greater than 10 megabytes',
        ];
        $request->validate([
            'userName' => 'required|string|max:255',
            'payment_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], $customMessages);

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

        // Notify all admins
        $pendingPaymentsCount = Payment::where('payment_type', 'cliq')->where('payment_status', 'pending')->count();
        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super Admin']);
        })->get();

        foreach ($admins as $admin) {
            $notification = Notification::create([
                'user_id' => $admin->id,
                'title' => 'New Cliq Payment',
                'message' => 'User ' . auth()->user()->full_name . ' has uploaded a new payment proof. There are ' . $pendingPaymentsCount . ' pending payments.',
                'icon' => 'bx bx-upload',
                'type' => 'admin-subscription',
                'is_seen' => false,
                'model_id' => $payment->id,
                'reminder' => false,
                'reminder_time' => null,
            ]);
            event(new NotificationEvent($notification));
        }

        return response()->json(['success' => true]);
    }

    public function approvePayment($id)
    {
        $payment = Payment::find($id);
        if ($payment) {
            $payment->payment_status = 'completed';
            $payment->save();

            $user = User::find($payment->user_id);
            $user->status = 'active';
            $user->save();

            $student = Student::where('student_id', $user->id)->first();
            $student->subscription_status = 'subscribed';
            $student->save();


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

            // Send notification to the user
            $notification = Notification::create([
                'user_id' => $payment->user_id,
                'title' => 'Subscription Approved',
                'message' => 'Your subscription payment has been approved. Your subscription will end on ' . $userSubscription->next_billing_time->format('F j, Y') . '.',
                'icon' => 'bx bx-check',
                'type' => 'subscription',
                'is_seen' => false,
                'model_id' => $payment->id,
                'reminder' => false,
                'reminder_time' => null,
            ]);
            event(new NotificationEvent($notification));

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

            // Send notification to the user
            $notification = Notification::create([
                'user_id' => $payment->user_id,
                'title' => 'Subscription Rejected',
                'message' => 'Your subscription payment has been rejected. Reason: ' . $request->reason,
                'icon' => 'bx bx-x',
                'type' => 'subscription',
                'is_seen' => false,
                'model_id' => $payment->id,
                'reminder' => false,
                'reminder_time' => null,
            ]);
            event(new NotificationEvent($notification));

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }


    public function reuploadPaymentProof(Request $request, $id)
    {
        $customMessages = [
            'userName.required' => 'The user name is required.',
            'userName.string' => 'The user name must be a string.',
            'userName.max' => 'The user name may not be greater than 255 characters.',
            'payment_image.required' => 'A payment image is required.',
            'payment_image.image' => 'The payment image must be an image.',
            'payment_image.mimes' => 'The payment image must be a file of type: jpeg, png, jpg, gif.',
            'payment_image.max' => 'The payment image may not be greater than 5 megabytes',
        ];

        // Validate the request
        $request->validate([
            'userName' => 'required|string|max:255',
            'payment_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], $customMessages);

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

            // Notify all admins
            $pendingPaymentsCount = Payment::where('payment_type', 'cliq')->where('payment_status', 'pending')->count();
            $admins = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'Super Admin']);
            })->get();

            foreach ($admins as $admin) {
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'title' => 'Cliq Reuploaded',
                    'message' => 'User ' . $payment->user->full_name . ' has reuploaded their payment proof. There are ' . $pendingPaymentsCount . ' pending payments.',
                    'icon' => 'bx bx-upload',
                    'type' => 'admin-subscription',
                    'is_seen' => false,
                    'model_id' => $payment->id,
                    'reminder' => false,
                    'reminder_time' => null,
                ]);
                event(new NotificationEvent($notification));
            }

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