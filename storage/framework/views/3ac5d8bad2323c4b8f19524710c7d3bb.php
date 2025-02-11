<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <section class="section-50">
        <div class="container">
            <h3 class="m-b-50 heading-line">Notifications <i class="fa fa-bell text-muted"></i></h3>

            <div class="notification-ui_dd-content">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="notification-list card <?php echo e($notification->is_seen ? '' : 'notification-list--unread'); ?>">
                        <div class="notification-list_content">
                            <div class="notify bg-light-primary p-2">
                                <i class='bx <?php echo e($notification->icon); ?>'></i>
                            </div>
                            <div class="notification-list_detail">
                                <p><b><?php echo e($notification->title); ?></b></p>
                                <p class="text"><?php echo e($notification->message); ?></p>
                                <p class="text"><small><?php echo e($notification->created_at->diffForHumans()); ?></small></p>
                                <?php if($notification->type == 'meeting'): ?>
                                    <a href="<?php echo e(route('student.meetings.show', $notification->model_id)); ?>">See Details</a>
                                <?php elseif($notification->type == 'subscription'): ?>
                                    <a href="<?php echo e(route('subscription.details')); ?>">See Details</a>
                                <?php elseif($notification->type == 'admin-subscription'): ?>
                                    <a href="<?php echo e(route('showPendingPayments')); ?>">See Details</a>
                                <?php elseif($notification->type == 'teacher-message'): ?>
                                    <a href="<?php echo e(route('contracts.myContract')); ?>#chat">See Details</a>
                                <?php elseif($notification->type == 'admin-message'): ?>
                                    <a href="<?php echo e(route('contracts.edit', $notification->model_id)); ?>#chat">See Details</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center">
                        <p>No notifications to display.</p>
                    </div>
                <?php endif; ?>
                <?php if($notifications->isNotEmpty()): ?>
                    <dev class="mt-3 pagination">
                        <?php echo e($notifications->links()); ?>

                    </dev>
                <?php endif; ?>
            </div>

            <?php if($notifications->isNotEmpty()): ?>
                <div class="text-center">
                    <a href="#!" id="mark-as-seen" class="dark-link">Mark all as seen</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
                    url: "<?php echo e(route('notifications.markAsSeen')); ?>",
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/notification.blade.php ENDPATH**/ ?>