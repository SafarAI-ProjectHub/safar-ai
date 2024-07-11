@extends('layouts_dashboard.main')
@section('styles')
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet">

    <style>
        .bg-paypal {
            background-color: #009cde;
        }

        .bg-cliq {
            background-color: #f5a623;
        }
    </style>
@endsection
@section('content')
    <!--start page wrapper -->

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Active Subscriptions
                            </p>
                            <h4 class="my-1 text-info">
                                {{ $data['total_Active_subscription'] }}
                            </h4>
                            <p class="mb-0 font-13"
                                style="color: {{ $data['mom_active_subscription'] < 0 ? 'red' : 'green' }}">
                                {{ $data['mom_active_subscription'] < 0 ? '' : '+' }}
                                {{ number_format($data['mom_active_subscription'], 2) }}% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto">
                            <i class='bx bxs-cart'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Total Revenue
                            </p>
                            <h4 class="my-1 text-danger">
                                ${{ number_format($data['total_payment'], 2) }}
                            </h4>
                            <p class="mb-0 font-13" style="color: {{ $data['mom_payment'] < 0 ? 'red' : 'green' }}">
                                {{ $data['mom_payment'] < 0 ? '' : '+' }}
                                {{ number_format($data['mom_payment'], 2) }}% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto">
                            <i class='bx bxs-wallet'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Revenue Growth
                            </p>
                            <h4 class="my-1 text-success">
                                {{ number_format($data['mom_growth'], 2) }}%
                            </h4>
                            <p class="mb-0 font-13" style="color: {{ $data['mom_growth'] < 0 ? 'red' : 'green' }}">
                                {{ $data['mom_growth'] < 0 ? '' : '+' }}
                                {{ number_format($data['mom_growth'], 2) }}% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                            <i class='bx bxs-bar-chart-alt-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Total Students
                            </p>
                            <h4 class="my-1 text-warning">
                                {{ $data['total_student'] }}
                            </h4>
                            <p class="mb-0 font-13" style="color: {{ $data['mom_student'] < 0 ? 'red' : 'green' }}">
                                {{ $data['mom_student'] < 0 ? '-' : '+' }}
                                {{ number_format($data['mom_student'], 2) }}% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto">
                            <i class='bx bxs-group'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-12 col-lg-8 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">
                                Sales Overview
                            </h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center ms-auto font-13 gap-2 mb-3">
                        <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                style="color: #14abef"></i>Students</span>
                        <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                style="color: #ffc107"></i>Subscriptions</span>
                        <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                style="color: #ff8359"></i>Payments</span>
                    </div>
                    <div class="chart-container-1">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">
                                Payment Methods
                            </h6>
                        </div>
                        {{-- <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container-2">
                        <canvas id="chart2"></canvas>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li
                            class="list-group-item d-flex bg-transparent justify-content-between align-items-center border-top">
                            Cliq
                            <span class="badge bg-cliq rounded-pill">{{ $data['yearly_payments_cliq'] }}</span>
                        </li>
                        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">
                            Paypal
                            <span class="badge bg-paypal rounded-pill">{{ $data['yearly_payments_paypal'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->


    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Recent Subscriptions</h6>
                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Subscription ID</th>
                            <th>Status</th>
                            <th>Total Paid</th>
                            <th>Subscription Type</th>
                            <th>Start Date</th>
                            <th>Next Billing Date / End Date </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['recent_subscriptions'] as $subscription)
                            <tr>
                                <td>{{ $subscription->user->full_name }}</td>
                                <td>{{ $subscription->id }}</td>
                                <td><span
                                        class="badge {{ $subscription->status == 'active' ? 'bg-success' : 'bg-danger' }}">{{ $subscription->status }}</span>
                                </td>
                                <td>${{ number_format($subscription->payments->sum('amount'), 2) }}</td>
                                <td>
                                    @if ($subscription->latestPayment)
                                        <span
                                            class="badge {{ $subscription->latestPayment->payment_type == 'paypal' ? 'bg-success' : 'bg-warning' }}">
                                            {{ $subscription->latestPayment->payment_type == 'paypal' ? 'Monthly' : 'One Time' }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">No Payment</span>
                                    @endif
                                </td>
                                <td>{{ $subscription->created_at->format('d M Y') }}</td>
                                <td>{{ $subscription->next_billing_time ? $subscription->next_billing_time->format('d M Y') : 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-12 col-lg-7 col-xl-8 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">
                                Geographic Distribution
                            </h6>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-7 col-xl-8 border-end">
                            <div id="geographic-map-2" style="height: 400px;"></div>
                        </div>
                        <div class="col-lg-5 col-xl-4">
                            @foreach ($data['country_data'] as $country => $counts)
                                <div class="mb-4">
                                    <p class="mb-2">
                                        <i class="flag-icon flag-icon-{{ strtolower($country) }} me-1"></i>
                                        {{ $country }}
                                        <span
                                            class="float-end">{{ round((($counts['students'] + $counts['teachers']) /array_sum(array_map(function ($c) {return $c['students'] + $c['teachers'];}, $data['country_data']))) *100,2) }}%</span>
                                    </p>
                                    <div class="progress" style="height: 7px;">
                                        <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                                            style="width: {{ round((($counts['students'] + $counts['teachers']) /array_sum(array_map(function ($c) {return $c['students'] + $c['teachers'];}, $data['country_data']))) *100,2) }}%">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-12 col-lg-5 col-xl-4 d-flex">
            <div class="card w-100 radius-10">
                <div class="card-body">
                    <div class="card radius-10 border shadow-none mt-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">
                                        Total Courses
                                    </p>
                                    <h4 class="my-1">
                                        {{ $data['total_courses'] }}
                                    </h4>
                                    <p class="mb-0 font-13">
                                        +{{ number_format($data['wow_enrolled_users'], 2) }}%
                                        from
                                        last week
                                    </p>
                                </div>
                                <div class="widgets-icons-2 bg-gradient-cosmic text-white ms-auto">
                                    <i class='bx bxs-book'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card radius-10 border shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">
                                        Total Units
                                    </p>
                                    <h4 class="my-1">
                                        {{ $data['total_units'] }}
                                    </h4>
                                    <p class="mb-0 font-13">
                                        +{{ number_format($data['wow_enrolled_users'], 2) }}%
                                        from
                                        last week
                                    </p>
                                </div>
                                <div class="widgets-icons-2 bg-gradient-ibiza text-white ms-auto">
                                    <i class='bx bxs-bookmark-alt'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card radius-10 mb-0 border shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">
                                        Total Users Enrolled
                                    </p>
                                    <h4 class="my-1">
                                        {{ $data['enrolled_users_by_week'] }}
                                    </h4>
                                    <p class="mb-0 font-13">
                                        +{{ number_format($data['wow_enrolled_users'], 2) }}%
                                        from
                                        last week
                                    </p>
                                </div>
                                <div class="widgets-icons-2 bg-gradient-kyoto text-dark ms-auto">
                                    <i class='bx bxs-user-check'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->

    <div class="row row-cols-1 row-cols-lg-3">

        <div class="col-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <p class="font-weight-bold mb-1 text-secondary">
                        Monthly Revenue
                    </p>
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">
                                ${{ number_format($data['current_month_revenue2'], 2) }}
                            </h4>
                        </div>
                        <div class="">
                            <p
                                class="mb-0 align-self-center font-weight-bold {{ $data['revenue_change'] >= 0 ? 'text-success' : 'text-danger' }} ms-2">
                                {{ $data['revenue_change'] >= 0 ? '+' : '' }}{{ number_format($data['revenue_change'], 2) }}%
                                <i
                                    class="bx {{ $data['revenue_change'] >= 0 ? 'bxs-up-arrow-alt' : 'bxs-down-arrow-alt' }} mr-2"></i>
                            </p>
                        </div>
                    </div>
                    <div class="chart-container-0 mt-5">
                        <canvas id="chart3"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div><!--end row-->


    <!--end page wrapper -->
    <!--start overlay-->
    <div class="overlay toggle-icon"></div>
    <!--end overlay-->
    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
    {{-- <footer class="page-footer">
	<p class="mb-0">Copyright © 2022. All right reserved.</p>
</footer> --}}

    <!--end wrapper-->
    <script>
        var yearlyStudents = @json($data['yearly_students']);
        var yearlySubscriptions = @json($data['yearly_subscriptions']);
        var yearlyPayments = @json($data['yearly_payments']);
        var yearlyPaymentsPaypal = @json($data['yearly_payments_paypal']);
        var yearlyPaymentsCliq = @json($data['yearly_payments_cliq']);
        var countryData = @json($data['country_data']);
        var dailyRevenue = @json(array_values($data['daily_revenue']));
    </script>
@endsection
@section('scripts')
    <!-- jQuery -->
    <script src="{{ asset('assets/plugins/jquery/jquery-3.6.0.min.js') }}"></script>

    <!-- jVectorMap CSS -->

    <!-- jVectorMap JS -->
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/js/index.js') }}"></script>
@endsection
