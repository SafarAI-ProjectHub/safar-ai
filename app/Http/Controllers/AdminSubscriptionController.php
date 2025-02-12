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

    public function index()
    {
        if (request()->ajax()) {
            $subscriptions = Subscription::with('user')->get();
            return DataTables::of($subscriptions)
                ->addColumn('adminName', function ($subscription) {
                    return $subscription->user && $subscription->user->full_name != null ? $subscription->user->full_name : 'N/A';
                })
                ->editColumn('is_active', function ($subscription) {
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input activate-subscription" type="checkbox" data-id="' . $subscription->id . '" ' . ($subscription->is_active ? 'checked' : '') . '>
                            </div>';
                })
                ->rawColumns(['actions', 'is_active'])
                ->make(true);
        }
        return view('dashboard.admin.subscriptions.index');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'features' => 'nullable|string',
            'subscription_type' => 'required|string|in:monthly,yearly',
        ]);

        $featuresArray = $this->processFeatures($request->features);

        $productResponse = $this->paypalService->createProduct($request->name, $request->description);

        if (isset($productResponse['id'])) {
            $planResponse = $this->paypalService->createPlan($productResponse['id'], $request->name, $request->description, $request->price, $request->subscription_type);
            if (isset($planResponse['id'])) {
                Subscription::create([
                    'name' => $request->name,
                    'paypal_plan_id' => $planResponse['id'],
                    'paypal_product_id' => $productResponse['id'],
                    'product_name' => $request->name,
                    'subscription_type' => $request->subscription_type,
                    'price' => $request->price,
                    'user_id' => Auth::user()->id,
                    'description' => $productResponse['description'],
                    'is_active' => 0,
                    'features' => json_encode($featuresArray),
                ]);

                return response()->json(['success' => 'Subscription plan created successfully.']);
            } else {
                return response()->json(['error' => 'Failed to create PayPal plan.'], 500);
            }
        } else {
            return response()->json(['error' => 'Failed to create PayPal product.'], 500);
        }
    }

    public function toggleActive($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscriptionType = $subscription->subscription_type;

        if ($subscription->is_active) {
            $activeSubscriptionCount = Subscription::where('is_active', true)
                ->where('subscription_type', $subscriptionType)
                ->count();

            if ($activeSubscriptionCount <= 1) {
                return response()->json(['success' => false, 'message' => 'You cannot deactivate all subscriptions. At least one subscription of each type must be active.'], 400);
            }

            $subscription->update(['is_active' => false]);
        } else {
            Subscription::where('id', '!=', $id)
                ->where('subscription_type', $subscriptionType)
                ->update(['is_active' => false]);
            $subscription->update(['is_active' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Subscription status updated successfully.']);
    }



    private function processFeatures($features)
    {
        return preg_split('/\r\n|\r|\n/', $features);
    }
}