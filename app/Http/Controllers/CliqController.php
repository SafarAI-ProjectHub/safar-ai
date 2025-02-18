<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\UserSubscription;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Student;
use App\Models\Notification;
use App\Events\NotificationEvent;
use Carbon\Carbon;

class CliqController extends Controller
{
    /**
     * 1) رفع إثبات الدفع بالـCliq
     *
     *   رابط الاستدعاء (من الجافاسكربت):
     *   POST /pay-with-cliq?action={yolo|solo|tolo|extend-yolo|extend-solo|extend-tolo}
     */
    public function payWithCliq(Request $request)
    {
        // نلتقط قيمة الـ action من كويري السترينغ
        // مثلاً /pay-with-cliq?action=solo
        $action = $request->query('action'); 

        // التحقق من المدخلات
        $request->validate([
            'userName'       => 'required|string|max:255',
            'payment_image'  => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ], [
            'userName.required' => 'The user name is required.',
            'payment_image.required' => 'A payment image is required.',
        ]);

        // حفظ ملف الصورة في مجلد public/storage/payments
        $path = $request->file('payment_image')->store('payments', 'public');
        $path = 'storage/' . $path;

        // تحديد نوع الاشتراك من قيمة action
        $subscriptionType = null;
        if ($action === 'yolo' || $action === 'extend-yolo') {
            $subscriptionType = 'yolo';
        } elseif ($action === 'solo' || $action === 'extend-solo') {
            $subscriptionType = 'solo';
        } elseif ($action === 'tolo' || $action === 'extend-tolo') {
            $subscriptionType = 'tolo';
        }

        if (!$subscriptionType) {
            return response()->json(['success' => false, 'message' => 'Invalid subscription type'], 400);
        }

        // جلب الاشتراك الفعّال من الداتا (والذي يطابق subscription_type)
        $subscription = Subscription::where('is_active', true)
            ->where('subscription_type', $subscriptionType)
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false, 
                'message' => 'Active subscription of this type not found'
            ], 404);
        }

        // إنشاء أو تحديث سجل الـ userSubscription
        $userSubscription = UserSubscription::firstOrNew(['user_id' => auth()->id()]);
        if (!$userSubscription->exists) {
            // في حال لا يوجد سجل اشتراك من قبل
            $userSubscription->subscription_id = 'cliq-' . auth()->id();
            $userSubscription->subscriptionId  = $subscription->id;
            $userSubscription->status          = 'inactive';
            $userSubscription->start_date      = null;
        } else {
            // في حال موجود من قبل, فقط نضبط الـ subscription_id من جديد
            $userSubscription->subscription_id = 'cliq-' . auth()->id();
            $userSubscription->subscriptionId  = $subscription->id;
        }
        $userSubscription->save();

        // حفظ سجل الدفع Payment بالحالة pending
        $payment = Payment::create([
            'user_id'             => auth()->id(),
            'payment_image'       => $path,
            'payment_type'        => 'cliq',
            'payment_status'      => 'pending',
            'subscription_id'     => $subscription->id,
            'amount'              => $subscription->price,
            'transaction_date'    => now(),
            'user_subscription_id'=> $userSubscription->id,
            'rejection_reason'    => null,
        ]);

        // إشعار الأدمنز بوجود دفعة جديدة معلقة
        $pendingPaymentsCount = Payment::where('payment_type', 'cliq')
            ->where('payment_status', 'pending')
            ->count();

        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super Admin']);
        })->get();

        foreach ($admins as $admin) {
            $notification = Notification::create([
                'user_id'  => $admin->id,
                'title'    => 'New Cliq Payment',
                'message'  => 'User ' . auth()->user()->full_name 
                              . ' uploaded a new payment. Pending: ' . $pendingPaymentsCount,
                'icon'     => 'bx bx-upload',
                'type'     => 'admin-subscription',
                'is_seen'  => false,
                'model_id' => $payment->id,
            ]);
            event(new NotificationEvent($notification));
        }

        return response()->json(['success' => true]);
    }

    /**
     * 2) الموافقة على دفع كليك (من لوحة تحكم الأدمن)
     */
    public function approvePayment($id)
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['success' => false], 404);
        }

        // تغيير حالة الدفع completed
        $payment->payment_status = 'completed';
        $payment->save();

        // تفعيل المستخدم والاشتراك
        $user = User::find($payment->user_id);
        $user->status = 'active';
        $user->save();

        $student = Student::where('student_id', $user->id)->first();
        if ($student) {
            $student->subscription_status = 'subscribed';
            $student->save();
        }

        // جلب أو إنشاء userSubscription
        $userSubscription = UserSubscription::firstOrNew(['user_id' => $payment->user_id]);
        if (!$userSubscription->exists) {
            // سجل جديد
            $userSubscription->subscription_id = 'cliq-' . $payment->user_id;
            $userSubscription->subscriptionId  = $payment->subscription_id; 
            $userSubscription->start_date      = now();
        }

        // نحدّد عدد الأشهر التي سنضيفها: 12 شهر للتولو, أو 1 شهر لغيره
        $subType = $userSubscription->subscription 
            ? strtolower($userSubscription->subscription->subscription_type)
            : 'solo';  // افتراضًا

        $monthsToAdd = 1; 
        if ($subType === 'tolo') {
            $monthsToAdd = 12;
        }

        // إذا كان عنده next_billing_time لم يأتِ بعد, نضيف فوقه
        // وإلا نضبطه من اليوم + عدد الأشهر
        if ($userSubscription->next_billing_time && $userSubscription->next_billing_time > now()) {
            $userSubscription->next_billing_time = Carbon::parse($userSubscription->next_billing_time)
                ->addMonths($monthsToAdd);
        } else {
            $userSubscription->next_billing_time = now()->addMonths($monthsToAdd);
        }

        $userSubscription->status = 'active';
        $userSubscription->save();

        // إشعار المستخدم
        $notification = Notification::create([
            'user_id' => $payment->user_id,
            'title'   => 'Subscription Approved',
            'message' => 'Your payment has been approved. Your subscription is active until ' 
                         . $userSubscription->next_billing_time->format('F j, Y') . '.',
            'icon'    => 'bx bx-check',
            'type'    => 'subscription',
            'is_seen' => false,
            'model_id'=> $payment->id,
        ]);
        event(new NotificationEvent($notification));

        return response()->json(['success' => true]);
    }

    /**
     * 3) رفض دفع كليك من قبل الأدمن
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['success' => false], 404);
        }

        // تغيير الحالة إلى rejected
        $payment->payment_status    = 'rejected';
        $payment->rejection_reason = $request->reason;
        $payment->save();

        // إشعار المستخدم
        $notification = Notification::create([
            'user_id'  => $payment->user_id,
            'title'    => 'Subscription Rejected',
            'message'  => 'Your payment was rejected. Reason: ' . $request->reason,
            'icon'     => 'bx bx-x',
            'type'     => 'subscription',
            'is_seen'  => false,
            'model_id' => $payment->id,
        ]);
        event(new NotificationEvent($notification));

        return response()->json(['success' => true]);
    }

    /**
     * 4) إعادة الرفع في حال تم رفض الدفع
     */
    public function reuploadPaymentProof(Request $request, $id)
    {
        $request->validate([
            'userName'       => 'required|string|max:255',
            'payment_image'  => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $payment = Payment::find($id);
        if (!$payment || $payment->payment_status !== 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found or not eligible for re-upload'
            ], 404);
        }

        // تخزين الصورة الجديدة
        $path = $request->file('payment_image')->store('payments', 'public');
        $path = 'storage/' . $path;

        $payment->payment_image    = $path;
        $payment->payment_status   = 'pending';
        $payment->rejection_reason = null;
        $payment->save();

        // إشعار الأدمنز
        $pendingPaymentsCount = Payment::where('payment_type', 'cliq')
            ->where('payment_status', 'pending')
            ->count();

        $admins = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super Admin']);
        })->get();

        foreach ($admins as $admin) {
            $notification = Notification::create([
                'user_id'  => $admin->id,
                'title'    => 'Cliq Reuploaded',
                'message'  => 'User ' . $payment->user->full_name 
                               . ' reuploaded payment proof. Pending: ' . $pendingPaymentsCount,
                'icon'     => 'bx bx-upload',
                'type'     => 'admin-subscription',
                'is_seen'  => false,
                'model_id' => $payment->id,
            ]);
            event(new NotificationEvent($notification));
        }

        return response()->json(['success' => true]);
    }

    /**
     * 5) عرض الدفعات المعلقة للأدمن
     */
    public function showPendingPayments()
    {
        $pendingPayments = Payment::where('payment_type', 'cliq')
            ->where('payment_status', 'pending')
            ->with('user', 'userSubscription', 'userSubscription.subscription')
            ->get();

        return view('dashboard.admin.subscriptions.cliq_payments', compact('pendingPayments'));
    }
}
