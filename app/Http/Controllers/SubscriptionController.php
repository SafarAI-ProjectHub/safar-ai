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

    /**
     * عرض تفاصيل الاشتراك
     */
    public function showSubscriptionDetails()
    {
        $user = Auth::user();

        // إذا كان عمر المستخدم 1-5 لا يسمح بالاشتراك
        if ($user->getAgeGroup() == '1-5') {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'You are not eligible to subscribe to any plan.');
        }

        // جلب اشتراك المستخدم إن وجد
        $subscription = UserSubscription::where('user_id', $user->id)->first();

        // متغيرات سيعاد تعبئتها
        $planDetails = null;
        $payment     = null;

        if ($subscription) {
            // الاشتراك موجود
            if ($subscription->status == 'active' || $subscription->status == 'suspend') {
                // إذا كان اشتراكه عبر Cliq
                if ($subscription->subscription_id == 'cliq-' . $user->id) {
                    // ابحث عن الخطة بالـ id
                    $planDetails = Subscription::where('id', $subscription->subscriptionId)->first();
                    $payment     = Payment::where('user_id', $user->id)->latest()->first();
                } else {
                    // ابحث عن الخطة بالـ paypal_plan_id
                    $planDetails = Subscription::where('paypal_plan_id', $subscription->subscription_id)->first();
                    $payment     = Payment::where('user_id', $user->id)->latest()->first();
                }
            } else {
                // الاشتراك موجود لكنه ليس active أو suspend
                // افتراضياً، نأتي بأي خطة فعّالة
                $planDetails = Subscription::where('is_active', true)->first();
                $payment     = Payment::where('user_id', $user->id)->latest()->first();
            }
        } else {
            // لا يوجد اشتراك للمستخدم
            $planDetails   = Subscription::where('is_active', true)->first();
            $payment       = new Payment();
            $subscription  = new UserSubscription(); 
        }

        // الآن، إذا $planDetails == null، سيتسبب الوصول إلى subscription_type في خطأ
        // نعالجه بالتحقق أولاً
        $otherPlan = null;
        if ($planDetails) {
            // إذا وجدنا خطة، نأتي بخطط أخرى لها subscription_type مختلف
            $otherPlan = Subscription::where('is_active', true)
                ->where('subscription_type', '!=', $planDetails->subscription_type)
                ->first();
        }
        // إن لم نجد أي خطة، سيظل $otherPlan = null

        // بقية المتغيرات
        $cliqUserName      = config('cliq.username');
        $activePlan        = Subscription::where('is_active', true)->first();
        $monthlyActivePlan = Subscription::where('is_active', true)
                                ->where('subscription_type', 'monthly')
                                ->first();
        $yearlyActivePlan  = Subscription::where('is_active', true)
                                ->where('subscription_type', 'yearly')
                                ->first();

        return view('dashboard.student.subscription_details', compact(
            'planDetails',
            'subscription',
            'cliqUserName',
            'payment',
            'otherPlan',
            'monthlyActivePlan',
            'yearlyActivePlan',
            'activePlan'
        ));
    }

    /**
     * إنشاء اشتراك (PayPal) جديد
     */
    public function create(Request $request)
    {
        $user   = Auth::user();
        $planId = $request->input('plan_id');
        $email  = $request->input('email');

        // ابحث عن الـ subscription في جدول subscriptions
        $subscriptionRecord = Subscription::where('paypal_plan_id', $planId)->first();
        if (!$subscriptionRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid plan ID.'
            ]);
        }

        // حفظ اشتراك المستخدم في user_subscriptions
        $userSubscription = UserSubscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'subscription_id' => $planId,
                'status'          => 'inactive',
                'subscriptionId'  => $subscriptionRecord->id
            ]
        );

        // إنشاء الاشتراك عبر PayPal
        $customId           = $user->id;
        $subscriptionResult = $this->paypalService->createSubscription($planId, $email, $customId);

        Log::info('PayPal Subscription Response:', $subscriptionResult);

        if (isset($subscriptionResult['status']) && $subscriptionResult['status'] === 'APPROVAL_PENDING') {
            $approvalLink = collect($subscriptionResult['links'])->where('rel', 'approve')->first();
            $approvalUrl  = $approvalLink['href'] ?? null;

            // حفظ رقم الاشتراك في user
            $user->paypal_subscription_id = $subscriptionResult['id'];
            $user->save();

            return response()->json([
                'success'      => true,
                'approval_url' => $approvalUrl
            ]);
        } else {
            // فشل إنشاء الاشتراك
            Log::error('Failed to create PayPal Subscription:', $subscriptionResult);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription.'
            ]);
        }
    }

    /**
     * إرجاع المستخدم من باي بال
     */
    public function handleReturn(Request $request)
    {
        // يتم استدعاؤه بعد موافقة الاشتراك على باي بال
        return redirect()
            ->route('student.dashboard')
            ->with('success', 'Subscription successful.');
    }

    /**
     * عند إلغاء الدفع على باي بال
     */
    public function handleCancel(Request $request)
    {
        return redirect()
            ->route('student.dashboard')
            ->with('error', 'Subscription cancelled.');
    }

    /**
     * إلغاء الاشتراك (PayPal)
     */
    public function cancel(Request $request)
    {
        $user          = Auth::user();
        $subscriptionId = $user->paypal_subscription_id;

        $response = $this->paypalService->cancelSubscription($subscriptionId);
        Log::info('PayPal Subscription Cancel Response:', [$subscriptionId, $response]);

        // تحدّث user_subscriptions
        UserSubscription::where('user_id', $user->id)->update(['status' => 'cancelled']);
        // تحدّث subscription_status في جدول students
        $user->student->subscription_status = 'cancelled';
        $user->student->save();

        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully.'
        ]);
    }

    /**
     * إعادة تفعيل الاشتراك (PayPal)
     */
    public function reactivate(Request $request)
    {
        $user          = Auth::user();
        $subscriptionId = $user->paypal_subscription_id;

        $response = $this->paypalService->reactivateSubscription($subscriptionId);
        Log::info('PayPal Subscription Reactivation Response:', $response);

        if (isset($response['status']) && $response['status'] === 'ACTIVE') {
            UserSubscription::where('user_id', $user->id)->update(['status' => 'active']);
            $user->student->subscription_status = 'subscribed';
            $user->student->save();

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
