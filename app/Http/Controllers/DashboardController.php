<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Student;
use App\Models\CourseStudent;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Unit;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

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
                ->map(function ($subscription) {
                    return $subscription->payments->sum('total_amount');
                })
                ->sum();
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

        // Calculate month-over-month changes
        $previous_month_active_subscription = Cache::remember('previous_month_active_subscription', 60, function () {
            return UserSubscription::where('status', 'active')
                ->whereBetween('updated_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->count();
        });

        $previous_month_payment = Cache::remember('previous_month_payment', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                ->sum('amount');
        });

        $previous_month_student = Cache::remember('previous_month_student', 60, function () {
            return Student::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
            })->count();
        });

        $previous_month_teacher = Cache::remember('previous_month_teacher', 60, function () {
            return Teacher::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
            })->count();
        });

        $current_month_active_subscription = Cache::remember('current_month_active_subscription', 60, function () {
            return UserSubscription::where('status', 'active')
                ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count();
        });

        $current_month_payment = Cache::remember('current_month_payment', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount');
        });

        $current_month_student = Cache::remember('current_month_student', 60, function () {
            return Student::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()]);
            })->count();
        });

        $current_month_teacher = Cache::remember('current_month_teacher', 60, function () {
            return Teacher::whereHas('user', function ($query) {
                $query->whereHas('levelTestAssessments')
                    ->whereBetween('updated_at', [now()->startOfMonth(), now()->endOfMonth()]);
            })->count();
        });

        $mom_active_subscription = Cache::remember('mom_active_subscription', 60, function () use ($current_month_active_subscription, $previous_month_active_subscription) {
            if ($previous_month_active_subscription == 0) {
                return 0;
            }
            return (($current_month_active_subscription - $previous_month_active_subscription) / $previous_month_active_subscription) * 100;
        });

        $mom_payment = Cache::remember('mom_payment', 60, function () use ($current_month_payment, $previous_month_payment) {
            if ($previous_month_payment == 0) {
                return 0;
            }
            return (($current_month_payment - $previous_month_payment) / $previous_month_payment) * 100;
        });

        $mom_student = Cache::remember('mom_student', 60, function () use ($current_month_student, $previous_month_student) {
            if ($previous_month_student == 0) {
                return 0;
            }
            return (($current_month_student - $previous_month_student) / $previous_month_student) * 100;
        });

        $mom_teacher = Cache::remember('mom_teacher', 60, function () use ($current_month_teacher, $previous_month_teacher) {
            if ($previous_month_teacher == 0) {
                return 0;
            }
            return (($current_month_teacher - $previous_month_teacher) / $previous_month_teacher) * 100;
        });

        // Fetch data for charts
        $yearly_students = Cache::remember('yearly_students', 60, function () {
            return Student::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('count', 'month');
        });

        $yearly_subscriptions = Cache::remember('yearly_subscriptions', 60, function () {
            return UserSubscription::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('count', 'month');
        });

        $yearly_payments = Cache::remember('yearly_payments', 60, function () {
            return Payment::selectRaw('MONTH(created_at) as month, count(amount) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month');
        });

        $yearly_payments_paypal = Cache::remember('yearly_payments_paypal', 60, function () {
            return Payment::selectRaw('MONTH(created_at) as month, sum(amount) as sum')
                ->where('payment_type', 'paypal')
                ->whereYear('created_at', now()->year)
                ->sum('amount');
        });

        $yearly_payments_cliq = Cache::remember('yearly_payments_cliq', 60, function () {
            return Payment::selectRaw('MONTH(created_at) as month, sum(amount) as sum')
                ->where('payment_type', 'cliq')
                ->whereYear('created_at', now()->year)
                ->sum('amount');
        });

        // Fetch recent subscriptions
        $recent_subscriptions = Cache::remember('recent_subscriptions', 60, function () {
            return UserSubscription::with('user', 'payments')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
        });

        // Fetch country data for students and teachers
        $countryData = Cache::remember('country_data', 60, function () {
            $students = Student::with('user')->get()->groupBy('user.country_location');
            $teachers = Teacher::with('user')->get()->groupBy('user.country_location');

            $studentCounts = $students->map->count()->toArray();
            $teacherCounts = $teachers->map->count()->toArray();

            $combinedCounts = [];
            foreach ($studentCounts as $country => $count) {
                if (!isset($combinedCounts[$country])) {
                    $combinedCounts[$country] = ['students' => 0, 'teachers' => 0];
                }
                $combinedCounts[$country]['students'] += $count;
            }

            foreach ($teacherCounts as $country => $count) {
                if (!isset($combinedCounts[$country])) {
                    $combinedCounts[$country] = ['students' => 0, 'teachers' => 0];
                }
                $combinedCounts[$country]['teachers'] += $count;
            }

            return $combinedCounts;
        });
        // Total Courses
        $total_courses = Cache::remember('total_courses', 60, function () {
            return Course::count();
        });

        // Total Units
        $total_units = Cache::remember('total_units', 60, function () {
            return Unit::count();
        });

        // Total Users Enrolled in Courses (by week)
        $enrolled_users_by_week = Cache::remember('enrolled_users_by_week', 60, function () {
            return CourseStudent::whereBetween('enrollment_date', [now()->startOfWeek(), now()->endOfWeek()])->count();
        });

        // Weekly Percentage Change
        $previous_week_enrolled_users = Cache::remember('previous_week_enrolled_users', 60, function () {
            return CourseStudent::whereBetween('enrollment_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        });

        $wow_enrolled_users = Cache::remember('wow_enrolled_users', 60, function () use ($enrolled_users_by_week, $previous_week_enrolled_users) {
            if ($previous_week_enrolled_users == 0) {
                return 0;
            }
            return (($enrolled_users_by_week - $previous_week_enrolled_users) / $previous_week_enrolled_users) * 100;
        });

        // Fetch daily revenue for the current month
        $daily_revenue = Cache::remember('daily_revenue', 60, function () {
            $daysInMonth = now()->daysInMonth;
            $payments = Payment::where('payment_status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->selectRaw('DAY(created_at) as day, SUM(amount) as total_amount')
                ->groupBy('day')
                ->get();

            $revenue = array_fill(1, $daysInMonth, 0); // Initialize all days with 0
            foreach ($payments as $payment) {
                $revenue[$payment->day] = $payment->total_amount;
            }
            return $revenue;
        });

        $current_month_revenue2 = array_sum($daily_revenue);

        // Fetch total revenue for the previous month
        $previous_month_revenue = Cache::remember('previous_month_revenue', 60, function () {
            return Payment::where('payment_status', 'completed')
                ->whereMonth('created_at', now()->subMonth()->month)
                ->sum('amount');
        });

        // Calculate the percentage change in revenue
        $revenue_change = 0;
        if ($previous_month_revenue > 0) {
            $revenue_change = (($current_month_revenue - $previous_month_revenue) / $previous_month_revenue) * 100;
        }

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
            'expected_revenue' => $expected_revenue,
            'current_month_revenue' => $current_month_revenue,
            'previous_month_revenue' => $previous_month_revenue,
            'mom_growth' => $mom_growth,
            'mom_active_subscription' => $mom_active_subscription,
            'mom_payment' => $mom_payment,
            'mom_student' => $mom_student,
            'mom_teacher' => $mom_teacher,
            'yearly_students' => $this->formatYearlyData($yearly_students),
            'yearly_subscriptions' => $this->formatYearlyData($yearly_subscriptions),
            'yearly_payments' => $this->formatYearlyData($yearly_payments),
            'yearly_payments_paypal' => $yearly_payments_paypal,
            'yearly_payments_cliq' => $yearly_payments_cliq,
            'recent_subscriptions' => $recent_subscriptions,
            'country_data' => $countryData,
            'total_courses' => $total_courses,
            'total_units' => $total_units,
            'enrolled_users_by_week' => $enrolled_users_by_week,
            'wow_enrolled_users' => $wow_enrolled_users,
            'daily_revenue' => $daily_revenue,
            'current_month_revenue2' => $current_month_revenue,
            'revenue_change' => $revenue_change,
        ];

        return view('dashboard.index', compact('data'));
    }

    private function formatYearlyData($data)
    {
        $formatted = array_fill(1, 12, 0);
        foreach ($data as $month => $value) {
            $formatted[$month] = $value;
        }
        return array_values($formatted);
    }
}