@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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

        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .loader-message {
            margin-top: 15px;
            font-size: 1.2rem;
            color: #007bff;
        }
    </style>
@endsection

@section('content')
    @php
        $subscriptionStatus = $subscription ? $subscription->status : 'free';
        $subscriptionDate = $subscription ? $subscription->start_date->format('M d, Y') : 'N/A';
        $nextBillingDate = $subscription ? $subscription->next_billing_time->format('M d, Y') : 'N/A';
        $features = $planDetails ? json_decode($planDetails->features, true) : [];
        $payment = $payment ?? null;

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

                @if (auth()->user()->country_location == 'Jordan')
                    <button id="pay-with-cliq" class="btn btn-primary btn-sm">Pay with Cliq</button>
                @endif
            </div>
        </div>
        <div class="card-body">
            <div class="border-bottom pt-0 pb-2">
                <div class="row mb-4">
                    <div class="col-lg-6 col-md-8 col-7 mb-2 mb-lg-0">
                        <span class="d-block">
                            <span class="h4"> Monthly</span>
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
                        <h6 class="mb-0">{{ $subscriptionStatus == 'active' ? $subscriptionDate : 'N/A' }}</h6>
                    </div>
                    <div class="col-lg-3 col-md-3 col-6 mb-2 mb-lg-0">
                        <span class="fs-6">Price</span>
                        <h6 class="mb-0">
                            {{ '$ ' . $planDetails->price . ' / Monthly' }}</h6>
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
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.min.js"></script>
    <script>
        // Initialize Pusher
        Pusher.logToConsole = true;

        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ env('PUSHER_APP_KEY') }}',
            cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    Authorization: 'Bearer ' + '{{ csrf_token() }}',
                },
            },
        });

        function showLoader(message) {
            var loaderHtml = `
                <div class="loader-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only"></span>
                    </div>
                    <div class="loader-message">${message}</div>
                </div>
            `;
            $('body').append(loaderHtml);
        }

        function hideLoader() {
            $('.loader-overlay').remove();
        }

        function showAlert(type, message, icon) {
            Swal.fire({
                icon: type,
                title: type.charAt(0).toUpperCase() + type.slice(1),
                text: message,
            });
        }

        $(document).ready(function() {
            function handleTimeout() {
                hideLoader();
                showAlert('warning', 'Timeout waiting for subscription confirmation. Redirecting...', 'bx-time');
                setTimeout(() => {
                    window.location.href = '{{ route('student.dashboard') }}';
                }, 2000);
            }

            function listenForEvent(type, userId) {
                let eventReceived = false;

                Echo.private('subscriptions.' + userId)
                    .listen('SubscriptionEvent', (e) => {
                        if (e.type === type) {
                            eventReceived = true;
                            hideLoader();
                            showAlert('success', `Subscription ${type} successfully.`, 'bx-check-circle');
                            location.reload();
                        }
                    });

                setTimeout(() => {
                    if (!eventReceived) {
                        handleTimeout();
                    }
                }, 30000); // 30 seconds timeout
            }

            $('#upgrade').click(function() {
                showLoader('Processing your subscription, please wait...');
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
                            hideLoader();
                            showAlert('danger', response.message, 'bx-error');
                        }
                    },
                    error: function(error) {
                        hideLoader();
                        showAlert('danger', 'Failed to subscribe. Please try again.',
                            'bx-error');
                    }
                });
            });

            $('#cancel-subscription').click(function() {
                showLoader('Cancelling your subscription, please wait...');
                $.ajax({
                    url: '{{ route('subscriptions.cancel') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        subscription_id: '{{ $subscription->subscription_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            listenForEvent('cancelled', '{{ Auth::id() }}');
                        } else {
                            hideLoader();
                            showAlert('danger', response.message, 'bx-error');
                        }
                    },
                    error: function(error) {
                        hideLoader();
                        showAlert('danger', 'Failed to cancel subscription. Please try again.',
                            'bx-error');
                    }
                });
            });

            $('#reactivate-subscription').click(function() {
                showLoader('Reactivating your subscription, please wait...');
                $.ajax({
                    url: '{{ route('subscriptions.reactivate') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        subscription_id: '{{ $subscription->paypal_subscription_id }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            listenForEvent('reactivated', '{{ Auth::id() }}');
                        } else {
                            hideLoader();
                            showAlert('danger', response.message, 'bx-error');
                        }
                    },
                    error: function(error) {
                        hideLoader();
                        showAlert('danger',
                            'Failed to reactivate subscription. Please try again.',
                            'bx-error');
                    }
                });
            });

            $('#pay-with-cliq').click(function() {
                const paymentStatus = '{{ $payment ? $payment->status : 'none' }}';
                const rejectionReason = '{{ $payment ? $payment->rejection_reason : '' }}';

                let htmlContent = `
                <form id="cliqPaymentForm">
            <div class="mb-3 text-center">
                <div>
                    <h3><strong>Pay with</strong> <img src="{{ asset('img/cliq.svg') }}" alt="Cliq Logo" style="max-width: 50px;"></h3>
                </div>
                <div>
                    <h2 id="cliqUserName" class="border fw-bold d-inline-block">{{ $cliqUserName }} <i class="bi bi-clipboard" style="cursor: pointer;" onclick="copyCliqUserName()"></i></h2>
                </div>
            </div>
            <div class="mb-3">
                <label for="userName" class="form-label">Your Full Name</label>
                <input type="text" class="form-control" id="userName" name="userName" required>
            </div>
            <div class="mb-3">
                <label for="payment_image" class="form-label">Proof of Payment</label>
                <input type="file" class="form-control" id="payment_image" name="payment_image" accept="image/*" required>
            </div>`;

                if (paymentStatus === 'pending') {
                    htmlContent += `<div class="alert alert-info" role="alert">
                    Your payment is still pending approval.
                </div>`;
                } else if (paymentStatus === 'rejected') {
                    htmlContent += `<div class="alert alert-danger" role="alert">
                    Your payment was rejected. Reason: ${rejectionReason}
                </div>`;
                }

                htmlContent += `</form>`;

                Swal.fire({
                    title: '',
                    html: htmlContent,
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    preConfirm: () => {
                        const form = document.getElementById('cliqPaymentForm');
                        const formData = new FormData(form);

                        return fetch('/pay-with-cliq', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) {
                                    throw new Error(data.message ||
                                        'There was an error submitting your payment proof.'
                                    );
                                }
                                return data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire('Submitted!',
                            'Your payment proof has been submitted. Await admin approval.',
                            'success');
                    }
                });
            });
        });
    </script>
@endsection
