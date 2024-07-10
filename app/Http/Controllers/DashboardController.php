<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Unit;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Cache;


class DashboardController extends Controller
{
    public function index()
    {
        $count_Active_subscription = Cache::remember('count_Active_subscription', 60, function () {
            return UserSubscription::where('status', 'active')->count();
        });

        $total_InActive_subscription = Cache::remember('total_InActive_subscription', 60, function () {
            return UserSubscription::where('status', '!=', 'active')->count();
        });

        $total_payment = Cache::remember('total_payment', 60, function () {
            return Payment::where('payment_status', 'completed')->sum('amount');
        });

        $total_payment_paypal = Cache::remember('total_payment_paypal', 60, function () {
            return Payment::where('payment_status', 'completed')->where('payment_type', 'paypal')->sum('amount');
        });

        $total_payment_cliq = Cache::remember('total_payment_cliq', 60, function () {
            return Payment::where('payment_status', 'completed')->where('payment_type', 'cliq')->sum('amount');
        });

        $total_student = Cache::remember('total_student', 60, function () {
            return Student::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments');
            })->count();
        });

        $total_teacher = Cache::remember('total_teacher', 60, function () {
            return Teacher::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments');
            })->count();
        });

        $total_course = Cache::remember('total_course', 60, function () {
            return Course::count();
        });

        $total_unit = Cache::remember('total_unit', 60, function () {
            return Unit::count();
        });

        $expected_revenue = Cache::remember('expected_revenue', 60, function () {
            return UserSubscription::where('status', 'active')
                ->where('next_billing_time', '>', now())
                ->whereHas('payments', function ($query) {
                    $query->where('payment_type', 'paypal');
                })
                ->with([
                    'payments' => function ($query) {
                        $query->select('user_subscription_id', \DB::raw('SUM(amount) as total_amount'))
                            ->groupBy('user_subscription_id');
                    }
                ])
                ->get()
                ->sum('payments.*.total_amount');
        });

        // Calculate current month and previous month revenue
        $current_month_revenue = Cache::remember('current_month_revenue', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount');
        });

        $previous_month_revenue = Cache::remember('previous_month_revenue', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->sum('amount');
        });

        // Calculate month-over-month growth
        $mom_growth = Cache::remember('mom_growth', 60, function () use ($current_month_revenue, $previous_month_revenue) {
            if ($previous_month_revenue == 0) {
                return 0;
            }
            return (($current_month_revenue - $previous_month_revenue) / $previous_month_revenue) * 100;
        });

        // Calculate week-over-week changes
        $previous_week_active_subscription = Cache::remember('previous_week_active_subscription', 60, function () {
            return UserSubscription::where('status', 'active')
                ->whereBetween('updated_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
                ->count();
        });

        $previous_week_payment = Cache::remember('previous_week_payment', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
                ->sum('amount');
        });

        $previous_week_student = Cache::remember('previous_week_student', 60, function () {
            return Student::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            })->count();
        });

        $previous_week_teacher = Cache::remember('previous_week_teacher', 60, function () {
            return Teacher::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
            })->count();
        });

        $current_week_active_subscription = Cache::remember('current_week_active_subscription', 60, function () {
            return UserSubscription::where('status', 'active')
                ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();
        });

        $current_week_payment = Cache::remember('current_week_payment', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->sum('amount');
        });

        $current_week_student = Cache::remember('current_week_student', 60, function () {
            return Student::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
            })->count();
        });

        $current_week_teacher = Cache::remember('current_week_teacher', 60, function () {
            return Teacher::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()]);
            })->count();
        });

        $wow_active_subscription = Cache::remember('wow_active_subscription', 60, function () use ($current_week_active_subscription, $previous_week_active_subscription) {
            if ($previous_week_active_subscription == 0) {
                return 0;
            }
            return (($current_week_active_subscription - $previous_week_active_subscription) / $previous_week_active_subscription) * 100;
        });

        $wow_payment = Cache::remember('wow_payment', 60, function () use ($current_week_payment, $previous_week_payment) {
            if ($previous_week_payment == 0) {
                return 0;
            }
            return (($current_week_payment - $previous_week_payment) / $previous_week_payment) * 100;
        });

        $wow_student = Cache::remember('wow_student', 60, function () use ($current_week_student, $previous_week_student) {
            if ($previous_week_student == 0) {
                return 0;
            }
            return (($current_week_student - $previous_week_student) / $previous_week_student) * 100;
        });

        $wow_teacher = Cache::remember('wow_teacher', 60, function () use ($current_week_teacher, $previous_week_teacher) {
            if ($previous_week_teacher == 0) {
                return 0;
            }
            return (($current_week_teacher - $previous_week_teacher) / $previous_week_teacher) * 100;
        });

        $data = [
            'total_Active_subscription' => $count_Active_subscription,
            'total_InActive_subscription' => $total_InActive_subscription,
            'total_payment' => $total_payment,
            'total_payment_paypal' => $total_payment_paypal,
            'total_payment_cliq' => $total_payment_cliq,
            'total_student' => $total_student,
            'total_teacher' => $total_teacher,
            'total_course' => $total_course,
            'total_unit' => $total_unit,
            'expacted_revenue' => $expected_revenue,
            'current_month_revenue' => $current_month_revenue,
            'previous_month_revenue' => $previous_month_revenue,
            'mom_growth' => $mom_growth,
            'wow_active_subscription' => $wow_active_subscription,
            'wow_payment' => $wow_payment,
            'wow_student' => $wow_student,
            'wow_teacher' => $wow_teacher,
        ];

        return view('dashboard.index', compact('data'));
    }



}