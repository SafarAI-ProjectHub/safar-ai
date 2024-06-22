@extends('layouts_dashboard.main')

@section('styles')
    <style>
        .modal-dialog {
            max-width: 600px;
            margin: 30px auto;
        }

        .modal-content {
            padding: 20px;
            text-align: center;
        }

        .modal-header {
            border-bottom: none;
        }

        .btn-close {
            float: right;
        }

        .btn-detail {
            background-color: #007BFF;
            /* Bootstrap primary color */
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            margin-top: 15px;
        }

        .btn-detail:hover {
            background-color: #0056b3;
        }
    </style>
@endsection

@section('content')
    @if (Auth::user()->status == 'pending')
        <div class="alert alert-warning" role="alert">
            <h4 class="alert-heading">Your account is pending</h4>
            <p>You need to take the level test to start your learning journey</p>
            <hr>
            <p class="mb-0">Please take the level test</p>
        </div>
    @elseif (Auth::user()->student->subscription_status === 'subscribed')
        <div class="row">
            @foreach ($courses as $course)
                <div class="col-md-4">
                    <div class="card" style="width: 18rem;">
                        <div class="text-center">
                            <img src="{{ $course->image_url }}" class="card-img-top" alt="Course Image" style="width: 80%;">
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">{{ $course->title }}</h5>
                            <a href="{{ route('admin.showcourse', $course->id) }}" class="btn btn-primary">Start</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <h1>Watch Our YouTube Playlist</h1>
            </div>
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

        <!-- New Modal for suggesting subscription -->
        <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="modal-title">Unlock Premium Features</h5>
                        <p>Get exclusive access to advanced courses and resources. Enhance your learning with our Premium
                            Plan!</p>
                        <button class="btn btn-detail btn-primary"
                            onclick="location.href='{{ route('subscription.details', ['id' => $planDetails->id]) }}'">See
                            Details</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Trigger the modal for users without a subscription
            @if (Auth::user()->student->subscription_status !== 'subscribed')
                $('#subscriptionModal').modal('show');
            @endif
        });
    </script>
@endsection
