<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminSubscriptionController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * عرض الاشتراكات في جدول (DataTable).
     */
    public function index(Request $request)
    {
        // إذا كان الطلب Ajax (من DataTables)
        if ($request->ajax()) {
            // نجلب سجلات user_subscriptions مع علاقتي subscription, user
            $userSubscriptions = UserSubscription::with(['subscription','user'])->get();

            return DataTables::of($userSubscriptions)

                ->addColumn('user_name', function($row) {
                    return optional($row->user)->full_name ?? 'N/A';
                })

                ->addColumn('subscription_id', function($row) {
                    return $row->subscription_id ?? 'N/A';
                })

                ->addColumn('status', function($row) {
                    return $row->status ?? 'N/A';
                })

                ->addColumn('start_date', function($row) {
                    return optional($row->start_date)->format('Y-m-d H:i') ?? 'N/A';
                })

                ->addColumn('next_billing_time', function($row) {
                    return optional($row->next_billing_time)->format('Y-m-d H:i') ?? 'N/A';
                })

                ->addColumn('payment_status', function($row) {
                    return $row->payment_status ?? 'N/A';
                })

                ->addColumn('product_name', function($row) {
                    return optional($row->subscription)->product_name ?? 'N/A';
                })
                ->addColumn('subscription_type', function($row) {
                    return optional($row->subscription)->subscription_type ?? 'N/A';
                })
                ->addColumn('description', function($row) {
                    return optional($row->subscription)->description ?? 'N/A';
                })
                ->addColumn('price', function($row) {
                    return optional($row->subscription)->price ?? 'N/A';
                })
                ->addColumn('features', function($row) {
                    $features = optional($row->subscription)->features;
                    return $features ? json_decode($features) : [];
                })

                ->make(true);
        }

        return view('dashboard.admin.subscriptions.index');
    }

    /**
     * إنشاء اشتراك جديد في جدول subscriptions (تعريف خطة).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',  // السماح بالسعر صفر أو أكثر
            'features'          => 'nullable|string',
            'subscription_type' => 'required|string|in:yolo,solo,tolo',
        ]);

        // جهّز الـ features كمصفوفة
        $featuresArray = $this->processFeatures($request->features);

        if ($request->price == 0) {
            Subscription::create([
                'product_name'      => $request->name,
                'paypal_plan_id'    => null, // لا حاجة لخطة باي بال
                'paypal_product_id' => null, // لا حاجة لمنتج باي بال
                'subscription_type' => $request->subscription_type,
                'price'             => 0,
                'user_id'           => Auth::id(),
                'description'       => $request->description,
                'is_active'         => false,
                'features'          => $featuresArray,
            ]);

            return response()->json([
                'success' => 'Free subscription plan created successfully.'
            ]);

        } else {
            $productResponse = $this->paypalService->createProduct(
                $request->name,
                $request->description
            );

            if (!isset($productResponse['id'])) {
                return response()->json([
                    'error' => 'Failed to create PayPal product.'
                ], 500);
            }

            // أنشئ الخطة في باي بال
            $planResponse = $this->paypalService->createPlan(
                $productResponse['id'],
                $request->name,
                $request->description,
                $request->price,
                $request->subscription_type
            );

            if (!isset($planResponse['id'])) {
                return response()->json([
                    'error' => 'Failed to create PayPal plan.'
                ], 500);
            }

            // حفظ في قاعدة البيانات
            Subscription::create([
                'product_name'      => $request->name,
                'paypal_plan_id'    => $planResponse['id'],
                'paypal_product_id' => $productResponse['id'],
                'subscription_type' => $request->subscription_type,
                'price'             => $request->price,
                'user_id'           => Auth::id(),
                'description'       => $request->description,
                'is_active'         => false,
                'features'          => $featuresArray,
            ]);

            return response()->json([
                'success' => 'Subscription plan created successfully.'
            ]);
        }
    }

    /**
     * تفعيل/تعطيل الاشتراك (تعريف الخطة) في جدول subscriptions.
     */
    public function toggleActive($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscriptionType = $subscription->subscription_type;

        if ($subscription->is_active) {
            // منع تعطيل آخر اشتراك من نفس النوع
            $activeSubscriptionCount = Subscription::where('is_active', true)
                ->where('subscription_type', $subscriptionType)
                ->count();

            if ($activeSubscriptionCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate all subscriptions. At least one subscription of each type must remain active.'
                ], 400);
            }

            $subscription->update(['is_active' => false]);
        } else {
            // عند التفعيل، عطّل غيره من نفس النوع
            Subscription::where('id', '!=', $id)
                ->where('subscription_type', $subscriptionType)
                ->update(['is_active' => false]);

            $subscription->update(['is_active' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Subscription status updated successfully.'
        ]);
    }

    /**
     * تفكيك الـ features إلى مصفوفة أسطر.
     */
    private function processFeatures($features)
    {
        if (!$features) {
            return [];
        }
        return preg_split('/\r\n|\r|\n/', $features);
    }
}
