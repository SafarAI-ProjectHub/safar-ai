@extends('layouts_dashboard.main')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
@endsection

@section('content')
    @if (Auth::user()->status == 'pending')
        {{-- Alert the student that their account is pending until they take the level test --}}
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Your account is pending</h4>
            <p>You need to take the level test to start your learning journey</p>
            <hr>
            <p class="mb-0">Please take the level test</p>
        </div>
    @else
        @if (Auth::user()->student->subscription_status === 'subscribed')
            <div class="row">
                <div class="col-md-12">
                    <h1>Available Courses</h1>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        @foreach ($courses as $course)
                            <div class="col-md-4">
                                <div class="card" style="width: 18rem;">
                                    {{-- Center the image --}}
                                    <div class="text-center">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4762/4762311.png"
                                            class="card-img-top" alt="Course Image" style="width: 80%;">
                                    </div>
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $course->title }}</h5>
                                        {{-- <p class="card-text">{{ $course->description }}</p> --}}
                                        <a href="{{ route('admin.showcourse', $course->id) }}"
                                            class="btn btn-primary">Start</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            {{-- Show YouTube playlist content --}}
            <div class="row">
                <div class="col-md-12">
                    <h1>Watch Our YouTube Playlist</h1>
                </div>
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card" style="width: 100%;">
                                <div class="text-center">
                                    <iframe width="100%" height="315"
                                        src="https://www.youtube.com/embed/videoseries?list=PLWz5rJ2EKKc8j2fah8n19gP9IpRhol_Ip"
                                        frameborder="0" allowfullscreen></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal for suggesting subscription -->
            <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="subscriptionModalLabel">Subscribe Now</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <section id="subscription" class="pricing">
                                <div class="container" data-aos="fade-up">
                                    <header class="section-header">
                                        <h2>Subscribe</h2>
                                        <p>Subscribe to our Plan</p>
                                    </header>
                                    <div class="row gy-4">
                                        <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                                            <div class="box">
                                                <h3 style="color: #07d5c0;">Free Plan</h3>
                                                <div class="price"><sup>$</sup>0<span> / mo</span></div>
                                                <img src="assets/img/pricing-free.png" class="img-fluid"
                                                    alt="Free Plan Image">
                                                <ul>
                                                    <li>Aida dere</li>
                                                    <li>Nec feugiat nisl</li>
                                                    <li>Nulla at volutpat dola</li>
                                                    <li class="na">Pharetra massa</li>
                                                    <li class="na">Massa ultricies mi</li>
                                                </ul>
                                                <button type="button" class="btn-buy" data-bs-dismiss="modal">Use Free
                                                    Plan</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                                            <div class="box">
                                                <h3 style="color: #07d5c0;">{{ $planDetails->product_name }}</h3>
                                                <div class="price"><sup>$</sup>{{ $planDetails->price }}<span> / mo</span>
                                                </div>
                                                <img src="{{ $planDetails->image_url }}" class="img-fluid" alt="Plan Image">
                                                <ul>
                                                    @foreach (json_decode($planDetails->features) as $feature)
                                                        <li>{{ $feature }}</li>
                                                    @endforeach
                                                </ul>
                                                <a href="#" id="subscribeButton" class="btn-buy">Subscribe</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            @if (Auth::user()->student->subscription_status === 'free')
                $('#subscriptionModal').modal('show');
            @endif

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
