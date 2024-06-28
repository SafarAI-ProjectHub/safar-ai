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
        $subscriptionStatus = $subscription ? $subscription->status : 'free';
        $subscriptionDate = $subscription ? $subscription->start_date->format('M d, Y') : 'N/A';
        $nextBillingDate = $subscription ? $subscription->next_billing_time->format('M d, Y') : 'N/A';
        $features = $planDetails ? json_decode($planDetails->features, true) : [];
    @endphp

    <div class="card mb-4">
        <div class="card-body">
            @if ($subscriptionStatus == 'active')
                <h2 class="fw-bold mb-0">${{ $planDetails->price }}/Monthly</h2>
                <p class="mb-0">
                    Your next monthly charge of
                    <span class="text-success">${{ $planDetails->price }}</span>
                    will be applied on
                    <span class="text-success">{{ $nextBillingDate }}</span>.
                </p>
            @else
                <h2 class="fw-bold mb-0">Free Plan</h2>
                <p class="mb-0">
                    Your next monthly charge of
                    <span class="text-success">$0</span>
                    will be applied
                    <span class="text-success">N/A</span>.
                </p>
            @endif
        </div>
    </div>
    <div class="card border-0">
        <div class="card-header d-lg-flex justify-content-between align-items-center">
            <div class="mb-3 mb-lg-0">
                <h3 class="mb-0">My Subscriptions</h3>
                <p class="mb-0">Here is a list of packages/products that you have subscribed to.</p>
            </div>
            <div>
                @if ($subscriptionStatus == 'active')
                    <button id="cancel-subscription" class="btn btn-danger btn-sm">Cancel Subscription</button>
                @elseif ($subscriptionStatus == 'suspended')
                    <button id="reactivate-subscription" class="btn btn-success btn-sm">Reactivate Subscription</button>
                    <button id="upgrade" class="btn btn-success btn-sm">Upgrade Now — Go Pro
                        ${{ $activePlan->price }}</button>
                @else
                    <button id="upgrade" class="btn btn-success btn-sm">Upgrade Now — Go Pro
                        ${{ $activePlan->price }}</button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="border-bottom pt-0 pb-2">
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-8 col-7 mb-2 mb-lg-0">
                        <span class="d-block">
                            <span class="h4">{{ $subscriptionStatus == 'active' ? 'Monthly' : 'Free Plan' }}</span>
                            <span
                                class="badge {{ $subscriptionStatus == 'active' ? 'bg-success' : ($subscriptionStatus == 'suspended' ? 'bg-warning' : 'bg-danger') }} ms-2">
                                {{ ucfirst($subscriptionStatus) }}
                            </span>
                        </span>
                        <p class="mb-0 fs-6">Subscription ID:
                            #{{ $subscription ? '100010' . $subscription->subscription_id : 'N/A' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Started On</span>
                        <h6 class="mb-0">{{ $subscriptionDate }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Price</span>
                        <h6 class="mb-0">
                            {{ $subscriptionStatus == 'active' ? '$ ' . $planDetails->price . ' / Monthly' : 'Free' }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Access</span>
                        <h6 class="mb-0">
                            {{ $subscriptionStatus == 'active' ? 'Access All Courses' : 'Access YouTube Videos' }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Billing Date</span>
                        <h6 class="mb-0">
                            {{ $subscriptionStatus == 'active' ? 'Next Billing on ' . $nextBillingDate : 'N/A' }}</h6>
                    </div>
                </div>
                @if ($features)
                    <div class="mt-4">
                        <h4>Features</h4>
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <ul class="list-unstyled d-flex flex-wrap">
                                        @foreach ($features as $index => $feature)
                                            <div class="col-lg-6 col-md-6 col-12 mb-2">
                                                <li class="d-flex align-items-start">
                                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                    <span>{{ $feature }}</span>
                                                </li>
                                            </div>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="pt-5">
                <div class="row mb-4">
                    <div class="col mb-2 mb-lg-0">
                        <span class="d-block">
                            <span class="h4">Free Plan</span>
                            <span class="badge {{ $subscriptionStatus == 'active' ? 'bg-danger' : 'bg-success' }} ms-2">
                                {{ $subscriptionStatus == 'active' ? 'Inactive' : 'Active' }}
                            </span>
                        </span>
                        <p class="mb-0 fs-6">Subscription ID: #100010{{ $planDetails ? $planDetails->id : 'N/A' }}</p>
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

        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    </div>
@endsection


@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showAlert(type, message, icon) {
            var alertHtml = `
                <div class="alert alert-${type} border-0 bg-${type} alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3" role="alert">
                    <div class="d-flex align-items-center">
                    <div class="font-35 text-white">
                        <i class="bx ${icon}"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0 text-white">${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                        <div class="text-white">${message}</div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
            $('body').append(alertHtml);
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }

        $(document).ready(function() {
            $('#upgrade').click(function() {
                $.ajax({
                    url: '{{ route('subscriptions.create') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_id: '{{ Auth::id() }}',
                        email: '{{ Auth::user()->email }}',
                        plan_id: '{{ $activePlan->paypal_plan_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.approval_url;
                        } else {
                            showAlert('danger', response.message, 'bx-error');
                        }
                    },
                    error: function(error) {
                        showAlert('danger', 'Failed to subscribe. Please try again.',
                            'bx-error');
                    }
                });
            });

            $('#cancel-subscription').click(function() {
                $.ajax({
                    url: '{{ route('subscriptions.cancel') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        subscription_id: '{{ $subscription->subscription_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', 'Subscription canceled successfully.',
                                'bx-check-circle');
                            location.reload();
                        } else {
                            showAlert('danger', response.message, 'bx-error');
                        }
                    },
                    error: function(error) {
                        showAlert('danger', 'Failed to cancel subscription. Please try again.',
                            'bx-error');
                    }
                });
            });

            $('#reactivate-subscription').click(function() {
                $.ajax({
                    url: '{{ route('subscriptions.reactivate') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        subscription_id: '{{ $subscription->paypal_subscription_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('success', 'Subscription reactivated successfully.',
                                'bx-check-circle');
                            location.reload();
                        } else {
                            showAlert('danger', response.message, 'bx-error');
                        }
                    },
                    error: function(error) {
                        showAlert('danger',
                            'Failed to reactivate subscription. Please try again.',
                            'bx-error');
                    }
                });
            });
        });
    </script>
@endsection
