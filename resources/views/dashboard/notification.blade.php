@extends('layouts_dashboard.main')

@section('styles')
    <style>
        @import url(https://fonts.googleapis.com/css?family=Roboto:300,400,700&display=swap);

        body {
            font-family: "Roboto", sans-serif;
            background: #EFF1F3;
            min-height: 100vh;
            position: relative;
        }

        .section-50 {
            padding: 50px 0;
        }

        .m-b-50 {
            margin-bottom: 50px;
        }

        .dark-link {
            color: #333;
        }

        .heading-line {
            position: relative;
            padding-bottom: 5px;
        }

        .heading-line:after {
            content: "";
            height: 4px;
            width: 75px;
            background-color: #c65ef3;
            position: absolute;
            bottom: 0;
            left: 0;
        }

        .notification-ui_dd-content {
            margin-bottom: 30px;
        }

        .notification-list {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            margin-bottom: 7px;
            background: #fff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.06);
        }

        .notification-list--unread {
            border-left: 2px solid #c65ef3;
        }

        .notification-list .notification-list_content {
            display: flex;
            align-items: center;
        }

        .notification-list .notification-list_content .notify {
            height: fit-content;
            width: fit-content;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-list .notification-list_content .notify i {
            font-size: 24px;
        }

        .notification-list .notification-list_content .notification-list_detail {
            margin-left: 15px;
        }

        .notification-list .notification-list_content .notification-list_detail p {
            margin-bottom: 5px;
            line-height: 1.2;
        }
    </style>
@endsection

@section('content')
    <section class="section-50">
        <div class="container">
            <h3 class="m-b-50 heading-line">Notifications <i class="fa fa-bell text-muted"></i></h3>

            <div class="notification-ui_dd-content">
                @forelse ($notifications as $notification)
                    <div class="notification-list card {{ $notification->is_seen ? '' : 'notification-list--unread' }}">
                        <div class="notification-list_content">
                            <div class="notify bg-light-primary p-2">
                                <i class='bx {{ $notification->icon }}'></i>
                            </div>
                            <div class="notification-list_detail">
                                <p><b>{{ $notification->title }}</b></p>
                                <p class="text">{{ $notification->message }}</p>
                                <p class="text"><small>{{ $notification->created_at->diffForHumans() }}</small></p>
                                @if ($notification->type == 'meeting')
                                    <a href="{{ route('student.meetings.show' ,$notification->model_id ) }}">See Details</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center">
                        <p>No notifications to display.</p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->isNotEmpty())
                <div class="text-center">
                    <a href="#!" id="mark-as-seen" class="dark-link">Mark all as seen</a>
                </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {


            $('#mark-as-seen').click(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('notifications.markAsSeen') }}",
                    method: "GET",
                    success: function(response) {
                        if (response.status === 'success') {
                            $('.notification-list--unread').removeClass(
                                'notification-list--unread');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking notifications as seen:', error);
                    }
                });
            });

        });
    </script>
@endsection
