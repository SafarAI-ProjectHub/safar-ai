<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AdminBillingController extends Controller
{
    public function subscriptions(Request $request)
    {
        if ($request->ajax()) {
            $query = UserSubscription::with(['user', 'payments'])->where('status', 'active');

            if ($request->has('daterange') && $request->daterange != '') {
                $dates = explode(' - ', $request->daterange);
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y h:i A', trim($dates[0]));
                $endDate = \Carbon\Carbon::createFromFormat('m/d/Y h:i A', trim($dates[1]))->endOfDay();
                $query->where('start_date', '>=', $startDate)->where('start_date', '<=', $endDate);
            }

            return DataTables::of($query)
                ->addColumn('user_name', function ($row) {
                    return $row->user->full_name;
                })
                ->addColumn('payment_status', function ($row) {
                    return $row->payments->isEmpty() ? 'N/A' : $row->payments()->latest()->first()->payment_status;
                })
                ->make(true);
        }

        return view('dashboard.admin.subscriptions.subscriptions');
    }

    public function payments(Request $request)
    {
        if ($request->ajax()) {
            $query = Payment::with(['user', 'subscription', 'userSubscription']);

            if ($request->has('daterange') && $request->daterange != '') {
                $dates = explode(' - ', $request->daterange);
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y h:i A', trim($dates[0]));
                $endDate = \Carbon\Carbon::createFromFormat('m/d/Y h:i A', trim($dates[1]))->endOfDay();
                $query->where('transaction_date', '>=', $startDate)->where('transaction_date', '<=', $endDate);
            }

            return DataTables::of($query)
                ->addColumn('user_name', function ($row) {
                    return $row->user && $row->user->full_name !== null ? $row->user->full_name : 'N/A';
                })
                ->addColumn('subscription_name', function ($row) {
                    return $row->subscription->product_name ?? 'N/A';
                })
                ->make(true);
        }

        return view('dashboard.admin.subscriptions.payments');
    }

    public function InactiveSubscriptions(Request $request)
    {
        if ($request->ajax()) {
            $query = UserSubscription::with(['user', 'payments'])->where('status', '!=', 'active');

            if ($request->has('daterange') && $request->daterange != '') {
                $dates = explode(' - ', $request->daterange);
                $startDate = \Carbon\Carbon::createFromFormat('m/d/Y h:i A', trim($dates[0]));
                $endDate = \Carbon\Carbon::createFromFormat('m/d/Y h:i A', trim($dates[1]))->endOfDay();
                $query->where('start_date', '>=', $startDate)->where('start_date', '<=', $endDate);
            }

            return DataTables::of($query)
                ->addColumn('user_name', function ($row) {
                    return $row->user->full_name;
                })
                ->addColumn('payment_status', function ($row) {
                    return $row->payments->isEmpty() ? 'N/A' : $row->payments()->latest()->first()->payment_status;
                })
                ->make(true);
        }

        return view('dashboard.admin.subscriptions.inactive_subscriptions');
    }
}