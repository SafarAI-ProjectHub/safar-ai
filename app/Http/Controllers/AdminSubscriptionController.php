<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class AdminSubscriptionController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * عرض قائمة الخطط (subscriptions) في DataTable
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Subscription::query()->select(
                'id',
                'product_name',
                'description',
                'price',
                'subscription_type',
                'is_active'
            );

            return DataTables::of($query)

                // نعدل عمود is_active ليعيد HTML يحتوي Checkbox (Toggle)
                ->editColumn('is_active', function ($row) {
                    $checked = $row->is_active ? 'checked' : '';
                    // نضع كلاس activate-subscription حتى نلتقط الحدث بالـJS
                    // ونستدعي التفعيل/التعطيل عبر Ajax
                    return "
                        <div class='form-switch d-flex align-items-center'>
                            <input type='checkbox'
                                   class='form-check-input activate-subscription'
                                   data-id='{$row->id}'
                                   style='cursor: pointer;'
                                   {$checked}>
                        </div>
                    ";
                })

                // مهم حتى لا يتم ترميز الـHTML وإظهاره كنص
                ->rawColumns(['is_active'])

                ->make(true);
        }

        return view('dashboard.admin.subscriptions.index');
    }

    /**
     * إرجاع بيانات خطة (للتعديل)
     * GET /admin/subscriptions/{id}
     */
    public function show($id)
    {
        $subscription = Subscription::findOrFail($id);
        return response()->json(['subscription' => $subscription]);
    }

    /**
     * إضافة خطة جديدة
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',
            'features'          => 'nullable|string',
            'subscription_type' => 'required|string|in:yolo,solo,tolo',
            'is_active'         => 'required|in:0,1', 
        ]);

        $featuresArray = $this->processFeatures($request->features);

        // خطة مجانية إن كان السعر = 0
        if ($request->price == 0) {
            $sub = Subscription::create([
                'product_name'      => $request->name,
                'paypal_plan_id'    => null,
                'paypal_product_id' => null,
                'subscription_type' => $request->subscription_type,
                'price'             => 0,
                'user_id'           => Auth::id(),
                'description'       => $request->description,
                'is_active'         => $request->boolean('is_active'), 
                'features'          => $featuresArray,
                'payment_method'    => 'card',
            ]);

            return response()->json(['success' => 'تم إنشاء الخطة المجانية بنجاح.']);
        }

        // خطة مدفوعة (إنشاء منتج + خطة في باي بال)
        $productResponse = $this->paypalService->createProduct(
            $request->name,
            $request->description
        );
        if (!isset($productResponse['id'])) {
            return response()->json(['error' => 'فشل في إنشاء المنتج في PayPal.'], 500);
        }

        $planResponse = $this->paypalService->createPlan(
            $productResponse['id'],
            $request->name,
            $request->description,
            $request->price,
            $request->subscription_type
        );
        if (!isset($planResponse['id'])) {
            return response()->json(['error' => 'فشل في إنشاء الخطة في PayPal.'], 500);
        }

        $sub = Subscription::create([
            'product_name'      => $request->name,
            'paypal_plan_id'    => $planResponse['id'],
            'paypal_product_id' => $productResponse['id'],
            'subscription_type' => $request->subscription_type,
            'price'             => $request->price,
            'user_id'           => Auth::id(),
            'description'       => $request->description,
            'is_active'         => $request->boolean('is_active'),
            'features'          => $featuresArray,
            'payment_method'    => 'card',
        ]);

        return response()->json(['success' => 'تم إنشاء الخطة المدفوعة بنجاح.']);
    }

    /**
     * تحديث الخطة
     * PUT /admin/subscriptions/{id}
     */
    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',
            'features'          => 'nullable|string',
            'subscription_type' => 'required|string|in:yolo,solo,tolo',
            'is_active'         => 'required|in:0,1',
        ]);

        $featuresArray = $this->processFeatures($request->features);

        // لو أردت تحديث خطة PayPal عند تغيّر السعر، يمكنك إضافة منطق هنا
        $subscription->update([
            'product_name'      => $request->name,
            'subscription_type' => $request->subscription_type,
            'price'             => $request->price,
            'description'       => $request->description,
            'features'          => $featuresArray,
            'is_active'         => $request->boolean('is_active'),
        ]);

        return response()->json(['success' => 'Subscription updated successfully.']);
    }

    /**
     * حذف الخطة
     * DELETE /admin/subscriptions/{id}
     */
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();
        return response()->json(['success' => 'Subscription deleted successfully.']);
    }

    /**
     * تفعيل/تعطيل الخطة (عند الضغط على التوجّل)
     */
    public function toggleActive($id)
    {
        $subscription = Subscription::findOrFail($id);

        // لو ترغب في منع تعطيل آخر خطة مفعلة من نوع معيّن، أضف المنطق هنا
        if ($subscription->is_active) {
            $subscription->update(['is_active' => false]);
        } else {
            $subscription->update(['is_active' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الاشتراك بنجاح.'
        ]);
    }

    /**
     * تحويل الـ features (نص) إلى مصفوفة أسطر
     */
    private function processFeatures($features)
    {
        if (!$features) {
            return [];
        }
        return preg_split('/\r\n|\r|\n/', $features);
    }
}
