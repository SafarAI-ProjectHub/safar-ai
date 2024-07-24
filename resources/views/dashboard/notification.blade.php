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
                                    <a href="{{ route('student.meetings.show', $notification->model_id) }}">See Details</a>
                                @elseif ($notification->type == 'subscription')
                                    <a href="{{ route('subscription.details') }}">See Details</a>
                                @elseif ($notification->type == 'admin-subscription')
                                    <a href="{{ route('showPendingPayments') }}">See Details</a>
                                @elseif ($notification->type == 'teacher-message')
                                    <a href="{{ route('contracts.myContract') }}#chat">See Details</a>
                                @elseif ($notification->type == 'admin-message')
                                    <a href="{{ route('contracts.edit', $notification->model_id) }}#chat">See Details</a>
                                @endif
                            </div>
                        </div>
                    </div>

                @empty
                    <div class="text-center">
                        <p>No notifications to display.</p>
                    </div>
                @endforelse
                @if ($notifications->isNotEmpty())
                    <dev class="mt-3 pagination">
                        {{ $notifications->links() }}
                    </dev>
                @endif
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
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            function fetchNotifications() {
                $.ajax({
                    url: "https://safar-ai.dev2.prodevr.com/notifications/get",
                    method: "GET",
                    success: function(response) {
                        console.log('Notifications:', response);
                        $('.alert-count').text(response.unread_count);
                        $('.msg-header-badge').text(response.unread_count + ' New');
                        $('#notification-list').empty();
                        response.notifications.forEach(function(notification) {
                            let truncatedMessage = truncateMessage(notification.message, 30);
                            let notificationUrl;

                            if (notification.type === 'meeting') {
                                notificationUrl = `/student/meetings/${notification.model_id}`;
                            } else if (notification.type === 'subscription') {
                                notificationUrl =
                                    `https://safar-ai.dev2.prodevr.com/student/subscription/details`;
                            } else if (notification.type === 'admin-subscription') {
                                notificationUrl =
                                    `https://safar-ai.dev2.prodevr.com/admin/pending-payments`;
                            } else if (notification.type === 'teacher-message') {
                                notificationUrl =
                                    `https://safar-ai.dev2.prodevr.com/teacher/my-contract`;
                            } else if (notification.type === 'admin-message') {
                                // Route::get('contracts/{contractId}/edit', [ContractController::class, 'edit'])->name('contracts.edit');
                                notificationUrl =
                                    `https://safar-ai.dev2.prodevr.com/admin/contracts/:contractId/edit`
                                    .replace(
                                        ':contractId', notification.model_id);
                            } else {
                                notificationUrl = '#';
                            }

                            let notificationItem = `
                            <a class="dropdown-item" href="${notificationUrl}">
                                <div class="d-flex align-items-center">
                                    <div class="notify bg-light-primary p-2 fs-4">
                                        <i class='bx ${notification.icon}'></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="msg-name">${notification.title}<span class="msg-time float-end">${timeAgo(notification.created_at)}</span></h6>
                                        <p class="msg-info">${truncatedMessage}</p>
                                    </div>
                                </div>
                            </a>
                        `;
                            $('#notification-list').append(notificationItem);
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching notifications:', error);
                    }
                });
            }

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
                        fetchNotifications()
                        swal({
                            title: "Success",
                            text: 'All notifications marked as seen.',
                            icon: "success",
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error marking notifications as seen:', error);
                    }
                });
            });

        });
    </script>
@endsection
