<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\UserSubscription;
use Auth;
use App\Models\Student;

class SubscriptionController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * صفحة تفاصيل الاشتراك للطالب
     */
    public function showSubscriptionDetails()
    {
        $user = Auth::user();

        // مثال: منع فئة عمرية
        if ($user->getAgeGroup() == '1-5') {
            return redirect()->route('student.dashboard')
                ->with('error', 'You are not eligible to subscribe to any plan.');
        }

        // اشتراك المستخدم الحالي
        $subscription = UserSubscription::where('user_id', $user->id)->first();

        $planDetails = null;
        $payment     = null;

        if ($subscription) {
            // إذا كان الاشتراك فعال أو متوقف
            if ($subscription->status == 'active' || $subscription->status == 'suspend') {
                // لو كان الاشتراك via Cliq
                if ($subscription->subscription_id == 'cliq-' . $user->id) {
                    $planDetails = Subscription::find($subscription->subscriptionId);
                    $payment     = Payment::where('user_id', $user->id)
                                          ->where('payment_type', 'cliq')
                                          ->latest()
                                          ->first();
                } else {
                    // via PayPal
                    $planDetails = Subscription::where('paypal_plan_id', $subscription->subscription_id)->first();
                    $payment     = Payment::where('user_id', $user->id)
                                          ->where('payment_type', 'paypal')
                                          ->latest()
                                          ->first();
                }
            } else {
                // اشتراك موجود ولكنه غير مفعل
                $planDetails = Subscription::where('is_active', true)->first();
                $payment     = Payment::where('user_id', $user->id)->latest()->first();
            }
        } else {
            // لا يوجد اشتراك مسبق
            $planDetails  = Subscription::where('is_active', true)->first();
            $payment      = new Payment();
            $subscription = new UserSubscription();
        }

        // لو أردت إظهار بلان أخرى بجانب الخطة الأساسية
        $otherPlan = null;
        if ($planDetails) {
            $otherPlan = Subscription::where('is_active', true)
                ->where('subscription_type', '!=', $planDetails->subscription_type)
                ->first();
        }

        // جميع الخطط الفعالة
        $activePlans = Subscription::where('is_active', true)->get();

        // اسم حساب Cliq الخاص بك
        $cliqUserName = config('cliq.username', 'YourCliqAccountHere');

        return view('dashboard.student.subscription_details', [
            'subscription'    => $subscription,
            'planDetails'     => $planDetails,
            'payment'         => $payment,
            'otherPlan'       => $otherPlan,
            'activePlans'     => $activePlans,
            'cliqUserName'    => $cliqUserName,
        ]);
    }

    /**
     * إنشاء اشتراك مع باي بال (طلب الاشتراك واستخراج رابط الموافقة)
     */
    public function create(Request $request)
    {
        $user   = Auth::user();
        $planId = $request->input('plan_id');
        $email  = $request->input('email');

        // البحث عن الخطة
        $subscriptionRecord = Subscription::where('paypal_plan_id', $planId)->first();
        if (!$subscriptionRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid plan ID.'
            ]);
        }

        // تحديث / إنشاء userSubscription
        $userSubscription = UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'subscription_id' => $planId,
                'status'          => 'inactive',
                'subscriptionId'  => $subscriptionRecord->id
            ]
        );

        // إنشاء الاشتراك عبر سيرفيس باي بال
        $customId           = $user->id;
        $subscriptionResult = $this->paypalService->createSubscription($planId, $email, $customId);

        Log::info('PayPal Subscription Response:', $subscriptionResult);

        if (isset($subscriptionResult['status']) && $subscriptionResult['status'] === 'APPROVAL_PENDING') {
            $approvalLink = collect($subscriptionResult['links'])->where('rel', 'approve')->first();
            $approvalUrl  = $approvalLink['href'] ?? null;

            $user->paypal_subscription_id = $subscriptionResult['id'];
            $user->save();

            return response()->json([
                'success'      => true,
                'approval_url' => $approvalUrl
            ]);
        } else {
            Log::error('Failed to create PayPal Subscription:', $subscriptionResult);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription.'
            ]);
        }
    }

    /**
     * اشتراك مجاني لـ YOLO
     */
    public function subscribeFree(Request $request)
    {
        $user = Auth::user();

        // البحث عن الخطة المجانية yolo
        $subscriptionRecord = Subscription::where('subscription_type', 'yolo')
            ->where('is_active', true)
            ->first();

        if (!$subscriptionRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No free YOLO plan found.'
            ]);
        }

        // انشاء أو تحديث الاشتراك
        UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'subscription_id'   => 'yolo-free-' . $user->id,
                'status'            => 'active',
                'subscriptionId'    => $subscriptionRecord->id,
                'start_date'        => now(),
                'next_billing_time' => null
            ]
        );

        // تحديت حالة الطالب (إن وجد)
        if ($user->student) {
            $user->student->subscription_status = 'subscribed';
            $user->student->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully subscribed to the free YOLO plan.'
        ]);
    }

    /**
     * يحدث بعد موافقة المستخدم في صفحة باي بال
     */
    public function handleReturn(Request $request)
    {
        return redirect()->route('student.dashboard')
            ->with('success', 'Subscription successful.');
    }

    /**
     * في حال ألغى من صفحة باي بال
     */
    public function handleCancel(Request $request)
    {
        return redirect()->route('student.dashboard')
            ->with('error', 'Subscription cancelled.');
    }

    /**
     * إلغاء الاشتراك (cancel) من الموقع
     */
    public function cancel(Request $request)
    {
        $user           = Auth::user();
        $subscriptionId = $user->paypal_subscription_id;

        if (!$subscriptionId) {
            return response()->json([
                'success' => false,
                'message' => 'No PayPal subscription ID found.'
            ]);
        }

        $response = $this->paypalService->cancelSubscription($subscriptionId);
        Log::info('PayPal Subscription Cancel Response:', [$subscriptionId, $response]);

        // تحديث الحالة في DB
        UserSubscription::where('user_id', $user->id)->update(['status' => 'cancelled']);
        if ($user->student) {
            $user->student->subscription_status = 'cancelled';
            $user->student->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully.'
        ]);
    }

    /**
     * إعادة التفعيل
     */
    public function reactivate(Request $request)
    {
        $user           = Auth::user();
        $subscriptionId = $user->paypal_subscription_id;

        if (!$subscriptionId) {
            return response()->json([
                'success' => false,
                'message' => 'No PayPal subscription ID found.'
            ]);
        }

        $response = $this->paypalService->reactivateSubscription($subscriptionId);
        Log::info('PayPal Subscription Reactivation Response:', $response);

        if (isset($response['status']) && ($response['status'] === 'ACTIVE' || $response['status'] === 'APPROVAL_PENDING')) {
            UserSubscription::where('user_id', $user->id)->update(['status' => 'active']);
            if ($user->student) {
                $user->student->subscription_status = 'subscribed';
                $user->student->save();
            }
            return response()->json([
                'success' => true,
                'message' => 'Subscription reactivated successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reactivate subscription.'
            ]);
        }
    }
}
