@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .feature-list {
            list-style: none;
            padding-left: 0;
        }

        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }

        .feature-list li:last-child {
            border-bottom: none;
        }
    </style>
@endsection

@section('content')
    @php
        $isSubscribed = Auth::user()->student->subscription_status == 'subscribed';
        $subscription = Auth::user()->userSubscriptions->where('status', 'active')->first();
        $subscription_id = $subscription ? $subscription->subscription_id : 'N/A';
        $subscription_date = $subscription ? $subscription->created_at->format('M d, Y') : 'N/A';
        $next_billing_date = $subscription ? $subscription->created_at->addMonth()->format('M d, Y') : 'N/A';
    @endphp

    <div class="card mb-4">

        <!-- Card body -->
        <div class="card-body">
            @if ($isSubscribed)
                <h2 class="fw-bold mb-0">${{ $planDetails->price }}/Monthly</h2>
                <p class="mb-0">
                    Your next monthly charge of
                    <span class="text-success">${{ $planDetails->price }}</span>
                    will be applied
                    <span class="text-success">July 20, 2020.</span>
                </p>
            @else
                <h2 class="fw-bold mb-0">Free Plan</h2>
                <p class="mb-0">
                    Your next monthly charge of
                    <span class="text-success">$0</span>
                    will be applied
                    <span class="text-success">N/A.</span>
                </p>
            @endif
        </div>
    </div>
    <div class="card border-0">
        <!-- Card header -->
        <div class="card-header d-lg-flex justify-content-between align-items-center">
            <div class="mb-3 mb-lg-0">
                <h3 class="mb-0">My Subscriptions</h3>
                <p class="mb-0">Here is list of package/product that you have subscribed.</p>
            </div>
            <div>
                <a id="upgrade" class="btn btn-success btn-sm">Upgrade Now — Go Pro ${{ $planDetails->price }}</a>
            </div>
        </div>
        <!-- Card body -->
        <div class="card-body">
            <div class="border-bottom pt-0 pb-5">
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-8 col-7 mb-2 mb-lg-0">
                        <span class="d-block">
                            <span class="h4">Monthly</span>
                            <span class="badge {{ $isSubscribed ? 'bg-success' : 'bg-danger' }} ms-2">
                                {{ $isSubscribed ? 'Active' : 'Inactive' }}
                            </span>
                        </span>
                        <p class="mb-0 fs-6">Subscription ID:
                            #{{ $isSubscribed ? '100010' . $subscription_id : $subscription_id }}</p>
                    </div>

                </div>
                <!-- Pricing data -->
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Started On</span>
                        <h6 class="mb-0">{{ $subscription_date }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Price</span>
                        <h6 class="mb-0">$ {{ $planDetails->price }} / Monthly</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Access</span>
                        <h6 class="mb-0">Access All Courses</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Billing Date</span>
                        <h6 class="mb-0">Next Billing on {{ $next_billing_date }}</h6>
                    </div>
                </div>
            </div>
            <div class="pt-5">
                <div class="row mb-4">
                    <div class="col mb-2 mb-lg-0">
                        <span class="d-block">
                            <span class="h4">Free Plan</span>
                            <span class="badge {{ $isSubscribed ? 'bg-danger' : 'bg-success' }} ms-2">
                                {{ $isSubscribed ? 'Inactive' : 'Active' }}
                            </span>
                        </span>
                        <p class="mb-0 fs-6">Subscription ID: #100010{{ $planDetails->id }}</p>
                    </div>
                    <div class="col-auto">
                        <a href="#" class="btn btn-light btn-sm disabled">Disabled</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Started On</span>
                        <h6 class="mb-0">{{ Auth::user()->created_at->format('M d, Y') }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Price</span>
                        <h6 class="mb-0">Free</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Access</span>
                        <h6 class="mb-0">Access YouTube Videos</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Billing Date</span>
                        <h6 class="mb-0">N/A</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#upgrade').click(function() {
                $.ajax({
                    url: '{{ route('subscriptions.create') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: '{{ Auth::id() }}',
                        email: '{{ Auth::user()->email }}',
                        plan_id: '{{ $planDetails->paypal_plan_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.approval_url;
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(error) {
                        alert('Failed to subscribe. Please try again.');
                    }
                });
            });


        });
    </script>
@endsection
