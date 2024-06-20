<?php

namespace App\Http\Controllers;

use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AdminSubscriptionController extends Controller
{
    protected $paypalProvider;

    public function __construct()
    {
        $this->paypalProvider = new PayPalClient;
        $this->paypalProvider->setApiCredentials(config('paypal'));
    }

    public function index()
    {
        if (request()->ajax()) {
            $subscriptions = Subscription::all();
            return DataTables::of($subscriptions)
                ->addColumn('actions', function ($subscription) {
                    return '<div class="d-flex justify-content-between">
                                <button data-id="' . $subscription->id . '" class="btn btn-sm btn-primary edit-subscription">Edit</button>
                                <button data-id="' . $subscription->id . '" class="btn btn-sm btn-danger delete-subscription">Delete</button>
                            </div>';
                })
                ->editColumn('is_active', function ($subscription) {
                    return '<div class="form-check form-switch">
                                <input class="form-check-input activate-subscription" type="checkbox" data-id="' . $subscription->id . '" ' . ($subscription->is_active ? 'checked' : '') . '>
                            </div>';
                })
                ->rawColumns(['actions', 'is_active'])
                ->make(true);
        }
        return view('dashboard.admin.subscriptions.index');
    }

    public function create()
    {
        return view('dashboard.admin.subscriptions.form', ['subscription' => new Subscription()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'is_active' => 'required|boolean',
            'features' => 'nullable|string',
        ]);

        $featuresArray = $this->processFeatures($request->features);

        $this->paypalProvider->getAccessToken();
        $productResponse = $this->paypalProvider->addProduct($request->name, $request->description, 'SERVICE', 'SOFTWARE');

        if (isset($productResponse['id'])) {
            $planResponse = $this->paypalProvider->addMonthlyPlan($request->name, $request->description, $request->price, $productResponse['id']);

            if (isset($planResponse['id'])) {
                Subscription::create([
                    'name' => $request->name,
                    'paypal_plan_id' => $planResponse['id'],
                    'price' => $request->price,
                    'duration' => $request->duration,
                    'description' => $request->description,
                    'is_active' => $request->is_active,
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

    public function edit(Subscription $subscription)
    {
        $subscription->features = implode("\n", json_decode($subscription->features));
        return view('dashboard.admin.subscriptions.form', compact('subscription'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'is_active' => 'required|boolean',
            'features' => 'nullable|string',
        ]);

        $featuresArray = $this->processFeatures($request->features);

        $this->paypalProvider->getAccessToken();
        $this->paypalProvider->updatePlan($subscription->paypal_plan_id, [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        $subscription->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'duration' => $request->duration,
            'is_active' => $request->is_active,
            'features' => json_encode($featuresArray),
        ]);

        return response()->json(['success' => 'Subscription plan updated successfully.']);
    }

    public function destroy(Subscription $subscription)
    {
        $this->paypalProvider->getAccessToken();
        $this->paypalProvider->deactivatePlan($subscription->paypal_plan_id);
        $subscription->delete();

        return response()->json(['success' => 'Subscription plan deleted successfully.']);
    }

    public function toggleActive($id)
    {
        $subscription = Subscription::findOrFail($id);
        Subscription::where('id', '!=', $id)->update(['is_active' => false]);
        $subscription->update(['is_active' => !$subscription->is_active]);

        return response()->json(['success' => 'Subscription status updated successfully.']);
    }

    private function processFeatures($features)
    {
        return preg_split('/\r\n|\r|\n/', $features);
    }
}