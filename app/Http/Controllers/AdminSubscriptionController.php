<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use App\Models\Subscription;
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
        if ($request->ajax()) {
            // اجلب الاشتراكات مع علاقة المستخدم
            $subscriptions = Subscription::with('user')->get();

            return DataTables::of($subscriptions)
                // عمود adminName لإظهار اسم المستخدم أو 'N/A'
                ->addColumn('adminName', function ($subscription) {
                    if ($subscription->user && $subscription->user->full_name) {
                        return $subscription->user->full_name;
                    }
                    return 'N/A';
                })
                // تعديل عمود is_active ليعرض سويتش
                ->editColumn('is_active', function ($subscription) {
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input activate-subscription" type="checkbox"
                                       data-id="' . $subscription->id . '"
                                       ' . ($subscription->is_active ? 'checked' : '') . '>
                            </div>';
                })
                ->rawColumns(['is_active']) // حتى يتم تفسيرها كـHTML
                ->make(true);
        }

        // عند الفتح العادي للصفحة
        return view('dashboard.admin.subscriptions.index');
    }

    /**
     * إنشاء اشتراك جديد.
     */
    public function store(Request $request)
    {
        // تحقق من القيم الواردة
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',  // السماح بالسعر صفر أو أكثر
            'features'          => 'nullable|string',
            'subscription_type' => 'required|string|in:yolo,solo,tolo',
        ]);

        // جهّز الـfeatures كمصفوفة
        $featuresArray = $this->processFeatures($request->features);

        // إذا السعر = 0، نعتبره اشتراك مجاني ولا ننشئ منتج/خطة في باي بال
        if ($request->price == 0) {
            Subscription::create([
                'product_name'      => $request->name,
                'paypal_plan_id'    => null,  // لا حاجة لخطة باي بال
                'paypal_product_id' => null,  // لا حاجة لمنتج باي بال
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
            // السعر > 0 -> إنشاء المنتج في PayPal
            $productResponse = $this->paypalService->createProduct(
                $request->name,
                $request->description
            );

            if (!isset($productResponse['id'])) {
                return response()->json([
                    'error' => 'Failed to create PayPal product.'
                ], 500);
            }

            // أنشئ الخطة في PayPal
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

            // احفظ في قاعدة البيانات
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
     * تفعيل/تعطيل الاشتراك.
     */
    public function toggleActive($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscriptionType = $subscription->subscription_type;

        if ($subscription->is_active) {
            // لو تريد منع تعطيل آخر اشتراك من نفس النوع
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
     * تفكيك الـfeatures إلى مصفوفة أسطر.
     */
    private function processFeatures($features)
    {
        if (!$features) {
            return [];
        }
        // split by new line
        return preg_split('/\r\n|\r|\n/', $features);
    }
}
