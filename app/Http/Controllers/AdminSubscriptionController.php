<?php

namespace App\Http\Controllers;

use App\Services\PayPalService;
use App\Models\Subscription;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AdminSubscriptionController extends Controller
{
    protected $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * عرض قائمة الاشتراكات في الـ DataTable.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            // سجل (Log) بيانات الفلتر بالتاريخ
            Log::info('طلب AJAX للـ admin.subscriptions مع daterange=', [
                'daterange' => $request->daterange
            ]);

            // نختار كل الاشتراكات مع العلاقة subscription
            $query = UserSubscription::with(['user', 'subscription']);

            // فلترة بالتاريخ (إن وجد)
            if ($request->filled('daterange')) {
                $dateRange = explode(' - ', $request->daterange);
                if (count($dateRange) === 2) {
                    try {
                        $start = Carbon::parse($dateRange[0]);
                        $end   = Carbon::parse($dateRange[1]);
                        $query->whereBetween('start_date', [$start, $end]);

                        Log::info('تم تطبيق فلترة التاريخ:', [
                            'start' => $start->toDateTimeString(),
                            'end'   => $end->toDateTimeString(),
                        ]);
                    } catch (\Exception $e) {
                        Log::error("Date parsing error: " . $e->getMessage());
                    }
                }
            }

            // نبني الـ DataTable
            return DataTables::of($query)
                ->addColumn('user_name', function ($row) {
                    return optional($row->user)->first_name . ' ' . optional($row->user)->last_name;
                })
                ->addColumn('subscription_id', function ($row) {
                    return $row->subscription_id;
                })
                ->addColumn('status', function ($row) {
                    return $row->status;
                })
                ->addColumn('start_date', function ($row) {
                    return $row->start_date
                        ? $row->start_date->format('Y-m-d H:i')
                        : 'N/A';
                })
                ->addColumn('next_billing_time', function ($row) {
                    return $row->next_billing_time
                        ? $row->next_billing_time->format('Y-m-d H:i')
                        : 'N/A';
                })
                ->addColumn('payment_status', function ($row) {
                    // مجرد مثال
                    return 'N/A';
                })
                // عمود يرسل معلومات الاشتراك (subscription) للـ Frontend
                ->addColumn('subscription_data', function ($row) {
                    // سجّل Log للبيانات حتى نعرف هل تُجلب بنجاح:
                    if ($row->subscription) {
                        Log::info("subscription_data for UserSubscription #{$row->id}:", [
                            'subscription_id' => $row->subscription->id,
                            'subscription_array' => $row->subscription->toArray(),
                        ]);
                        return $row->subscription->toArray();
                    } else {
                        Log::warning("لا توجد علاقة subscription لهذا الـ UserSubscription #{$row->id}");
                        return null;
                    }
                })
                ->addColumn('details', function ($row) {
                    return '<button class="btn btn-info btn-sm view-details"
                            data-id="'.$row->id.'">View</button>';
                })
                ->rawColumns(['details'])
                ->make(true);
        }

        // إن لم يكن طلب AJAX نعيد الفيو
        return view('dashboard.admin.subscriptions.index');
    }

    /**
     * إنشاء اشتراك جديد في جدول subscriptions (تعريف خطة).
     */
    public function store(Request $request)
    {
        Log::info("طلب إنشاء اشتراك جديد:", $request->all());

        $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'required|string',
            'price'             => 'required|numeric|min:0',
            'features'          => 'nullable|string',
            'subscription_type' => 'required|string|in:yolo,solo,tolo',
        ]);

        // جهّز الـ features كمصفوفة
        $featuresArray = $this->processFeatures($request->features);

        Log::info("featuresArray:", $featuresArray);

        if ($request->price == 0) {
            // لو الاشتراك مجاني:
            $sub = Subscription::create([
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

            Log::info("تم إنشاء خطة مجانية بنجاح", [$sub]);

            return response()->json([
                'success' => 'تم إنشاء الخطة المجانية بنجاح.'
            ]);
        } else {
            // في حال السعر أكبر من الصفر، ننشئ منتج وخطة في PayPal
            $productResponse = $this->paypalService->createProduct(
                $request->name,
                $request->description
            );

            Log::info("نتيجة إنشاء المنتج في باي بال:", $productResponse);

            if (!isset($productResponse['id'])) {
                Log::error("فشل إنشاء المنتج في باي بال");
                return response()->json([
                    'error' => 'فشل في إنشاء المنتج في PayPal.'
                ], 500);
            }

            $planResponse = $this->paypalService->createPlan(
                $productResponse['id'],
                $request->name,
                $request->description,
                $request->price,
                $request->subscription_type
            );

            Log::info("نتيجة إنشاء الخطة في باي بال:", $planResponse);

            if (!isset($planResponse['id'])) {
                Log::error("فشل إنشاء الخطة في باي بال");
                return response()->json([
                    'error' => 'فشل في إنشاء الخطة في PayPal.'
                ], 500);
            }

            $sub = Subscription::create([
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

            Log::info("تم إنشاء خطة مدفوعة بنجاح", [$sub]);

            return response()->json([
                'success' => 'تم إنشاء الخطة المدفوعة بنجاح.'
            ]);
        }
    }

    /**
     * تفعيل/تعطيل اشتراك (تعريف خطة).
     */
    public function toggleActive($id)
    {
        Log::info("طلب toggleActive للاشتراك رقم: $id");
        $subscription = Subscription::findOrFail($id);
        $subscriptionType = $subscription->subscription_type;
        Log::info("الاشتراك قبل التغيير:", $subscription->toArray());

        if ($subscription->is_active) {
            // منع تعطيل آخر اشتراك من نفس النوع
            $activeSubscriptionCount = Subscription::where('is_active', true)
                ->where('subscription_type', $subscriptionType)
                ->count();

            if ($activeSubscriptionCount <= 1) {
                Log::warning("محاولة تعطيل آخر اشتراك فعال من النوع نفسه: $subscriptionType");
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تعطيل آخر اشتراك من هذا النوع. يجب أن يبقى واحد على الأقل مفعّل.'
                ], 400);
            }

            $subscription->update(['is_active' => false]);
            Log::info("تم تعطيل الاشتراك رقم: $id");
        } else {
            // عند التفعيل، نعطّل غيره من نفس النوع
            Subscription::where('id', '!=', $id)
                ->where('subscription_type', $subscriptionType)
                ->update(['is_active' => false]);

            $subscription->update(['is_active' => true]);
            Log::info("تم تفعيل الاشتراك رقم: $id وتعطيل الباقي من نفس النوع: $subscriptionType");
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الاشتراك بنجاح.'
        ]);
    }

    /**
     * تفكيك الـ features إلى مصفوفة أسطر (سطر سطر).
     */
    private function processFeatures($features)
    {
        if (!$features) {
            return [];
        }
        return preg_split('/\r\n|\r|\n/', $features);
    }
}
