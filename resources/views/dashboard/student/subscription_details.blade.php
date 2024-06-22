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
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                Subscription Details
            </div>
            <div class="card-body">
                <h5 class="card-title">{{ $planDetails->name }}</h5>
                <h6 class="card-subtitle mb-2 text-muted">$ {{ $planDetails->price }} / month</h6>
                <p class="card-text">{{ $planDetails->description }}</p>
                <ul class="feature-list">
                    @foreach (json_decode($planDetails->features, true) as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
                <button id="subscribeButton" class="btn btn-primary">Subscribe Now</button>
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="subscription-card">
                    <div class="subscription-header">My Subscriptions</div>
                    <p>Here is the list of packages/products that you have subscribed.</p>

                    <!-- Active Subscription -->
                    <div class="border-bottom pb-3 mb-3">
                        <span class="badge bg-success status-active">Active</span>
                        <h5>Monthly - Subscription ID: #100010002</h5>
                        <div class="row">
                            <div class="col-md-3 subscription-info">Started On: Oct 1, 2020</div>
                            <div class="col-md-3 subscription-info">Price: Monthly</div>
                            <div class="col-md-3 subscription-info">Access: Access All Courses</div>
                            <div class="col-md-3 subscription-info">Billing Date: Next Billing on Nov 1, 2020</div>
                        </div>
                        <div class="mt-2">
                            <button class="btn btn-primary">Change Plan</button>
                            <span class="mx-2">|</span>
                            <label class="switch">
                                <input type="checkbox" checked>
                                <span class="slider round"></span>
                            </label> Auto Renewal
                        </div>
                    </div>

                    <!-- Expired Subscription -->
                    <div>
                        <span class="badge bg-danger status-expired">Expired</span>
                        <h5>Free Plan - Subscription ID: #100010001</h5>
                        <div class="row">
                            <div class="col-md-3 subscription-info">Started On: Sept 1, 2020</div>
                            <div class="col-md-3 subscription-info">Price: Free - Trial a Month</div>
                            <div class="col-md-3 subscription-info">Access: Access All Courses</div>
                            <div class="col-md-3 subscription-info">Billing Date: Disabled</div>
                        </div>
                    </div>
                </div>

                <button class="btn btn-upgrade">Upgrade Now — Go Pro $39.00</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#subscribeButton').click(function() {
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
