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
            // نجلب سجلات subscriptions مع علاقة user (الشخص الذي أنشأ الخطة مثلاً)
            // مع ملاحظة أنّ عمود user_id موجود في جدول subscriptions
            $subscriptions = Subscription::with('user')->select('subscriptions.*');

            return DataTables::of($subscriptions)

                // العمود الأول (اسم الأدمن أو الشخص الذي أضاف الاشتراك)
                ->addColumn('adminName', function($row) {
                    return $row->user ? $row->user->full_name : 'N/A';
                })

                // بقية الحقول يمكن قراءتها مباشرةً لأننا جلبنا subscription.* في select
                // فبإمكان DataTables الوصول إلى (product_name, description, price, subscription_type) مباشرة
                // أما عمود is_active فيحتاج منا لتكوينه يدوياً من أجل زر التبديل
                ->addColumn('is_active', function($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    // نعيد HTML يحتوي على سويتش التفعيل
                    return '
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input activate-subscription" 
                                   data-id="'.$row->id.'" '.$checked.'>
                        </div>
                    ';
                })

                // نحتاج للسماح بعرض الحقل is_active على شكل HTML
                ->rawColumns(['is_active'])

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
            'price'             => 'required|numeric|min:0',
            'features'          => 'nullable|string',
            'subscription_type' => 'required|string|in:yolo,solo,tolo',
        ]);

        // جهّز الـ features كمصفوفة
        $featuresArray = $this->processFeatures($request->features);

        // إذا السعر صفر، ننشئ الخطة دون أن ننشئ منتج/خطة في باي بال
        if ($request->price == 0) {
            Subscription::create([
                'product_name'      => $request->name,
                'paypal_plan_id'    => null,
                'paypal_product_id' => null,
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
            // في حال كان السعر أكبر من الصفر نستخدم PayPalService
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
