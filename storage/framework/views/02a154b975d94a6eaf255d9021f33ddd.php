<header>
    <div class="topbar d-flex align-items-center">
        <nav class="navbar navbar-expand gap-3">
            <div class="mobile-toggle-menu"><i class='bx bx-menu'></i>
            </div>
            
            <div class="position-relative search-bar d-lg-block d-none" data-bs-toggle="modal"
                data-bs-target="#SearchModal">
                <input class="form-control px-5" disabled type="search" placeholder="Search">
                <span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-5"><i
                        class='bx bx-search'></i></span>
            </div>
            <div class="top-menu ms-auto">
                <ul class="navbar-nav align-items-center gap-1">
                    <li class="nav-item mobile-search-icon d-flex d-lg-none" data-bs-toggle="modal"
                        data-bs-target="#SearchModal">
                        <a class="nav-link" href="avascript:;"><i class='bx bx-search'></i>
                        </a>
                    </li>

                    

                    <li class="nav-item dark-mode d-none d-sm-flex">
                        <a class="nav-link dark-mode-icon" href="javascript:;"><i class='bx bx-moon'></i>
                        </a>
                    </li>

                    


                    <li class="nav-item dropdown dropdown-large">
                        <a class="nav-link dropdown-toggle dropdown-toggle-nocaret position-relative" href="#"
                            data-bs-toggle="dropdown">
                            <span class="alert-count">0</span>
                            <i class='bx bx-bell'></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a href="javascript:;">
                                <div class="msg-header">
                                    <p class="msg-header-title">Notifications</p>
                                    <p class="msg-header-badge"><span id="unread-count">0</span> New</p>
                                </div>
                            </a>
                            <div class="header-notifications-list app-container" id="notification-list">
                                <!-- Notifications will be appended here -->
                            </div>
                            <a href="<?php echo e(route('notifications.index')); ?>">
                                <div class="text-center msg-footer">
                                    <button class="btn btn-primary w-100">View All Notifications</button>
                                </div>
                            </a>
                        </div>
                    </li>


                    


                </ul>
            </div>
            <div class="user-box dropdown px-3">
                <a class="d-flex align-items-center nav-link dropdown-toggle gap-3 dropdown-toggle-nocaret"
                    href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="<?php echo e(Auth::user()->profile_image ? asset(Auth::user()->profile_image) : asset('assets/images/avatars/profile-Img.png')); ?>"
                        class="user-img" alt="user avatar">
                    <div class="user-info">
                        <p class="user-name bold mb-0">
                            <?php if(Auth::check()): ?>
                                <?php echo e(Auth::user()->getFullNameAttribute()); ?>

                            <?php endif; ?>
                        </p>
                        <p class="designattion mb-0">
                            <?php if(Auth::check()): ?>
                                <?php echo e(Auth::user()->role->role_name); ?>

                            <?php endif; ?>
                        </p>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item d-flex align-items-center" href="<?php echo e(route('profile.edit')); ?>"><i
                                class="bx bx-user fs-5"></i><span>Profile</span></a>
                    </li>
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-cog fs-5"></i><span>Settings</span></a> -->
                    <!--                     </li> -->
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-home-circle fs-5"></i><span>Dashboard</span></a> -->
                    <!--                     </li> -->
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-dollar-circle fs-5"></i><span>Earnings</span></a> -->
                    <!--                     </li> -->
                    <!--                     <li><a class="dropdown-item d-flex align-items-center" href="javascript:;"><i -->
                    <!--                                 class="bx bx-download fs-5"></i><span>Downloads</span></a> -->
                    <!--                     </li> -->
                    <li>
                        <div class="dropdown-divider mb-0">
                        </div>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bx bx-log-out-circle"></i><span>Logout</span>
                        </a>
                    </li>
                    <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                        <?php echo csrf_field(); ?>
                    </form>
                </ul>
            </div>
        </nav>
    </div>
</header>

<script>
    // Initialize Pusher
    Pusher.logToConsole = false;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '<?php echo e(env('PUSHER_APP_KEY')); ?>',
        cluster: '<?php echo e(env('PUSHER_APP_CLUSTER')); ?>',
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                Authorization: 'Bearer ' + '<?php echo e(csrf_token()); ?>',
            },
        },
    });

    function truncateMessage(message, maxLength) {
        if (message.length > maxLength) {
            return message.substring(0, maxLength) + '...';
        }
        return message;
    }

    function playNotificationSoundheader() {
        const sound = new Audio(
            `/sounds/chatify/mixkit-correct-answer-tone-2870.wav`
        );
        console.log('playNotificationSoundheader::::::::::::::::::');
        sound.play();
    }

    function timeAgo(date) {
        const now = new Date();
        const seconds = Math.floor((now - new Date(date)) / 1000);
        let interval = Math.floor(seconds / 31536000);

        if (interval > 1) {
            return interval + ' years ago';
        }
        interval = Math.floor(seconds / 2592000);
        if (interval > 1) {
            return interval + ' months ago';
        }
        interval = Math.floor(seconds / 604800);
        if (interval > 1) {
            return interval + ' weeks ago';
        }
        interval = Math.floor(seconds / 86400);
        if (interval > 1) {
            return interval + ' days ago';
        }
        interval = Math.floor(seconds / 3600);
        if (interval > 1) {
            return interval + ' hours ago';
        }
        interval = Math.floor(seconds / 60);
        if (interval > 1) {
            return interval + ' minutes ago';
        }
        return Math.floor(seconds) + ' seconds ago';
    }

    $(document).ready(function() {
        function fetchNotifications() {

            $.ajax({
                url: "<?php echo e(route('notifications.get')); ?>",
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
                            notificationUrl = `<?php echo e(route('subscription.details')); ?>`;
                        } else if (notification.type === 'admin-subscription') {
                            notificationUrl = `<?php echo e(route('showPendingPayments')); ?>`;
                        } else if (notification.type === 'teacher-message') {
                            notificationUrl = `<?php echo e(route('contracts.myContract')); ?>`;
                            notificationUrl = notificationUrl + '#chat';
                        } else if (notification.type === 'admin-message') {
                            notificationUrl =
                                `<?php echo e(route('contracts.edit', ':contractId')); ?>`.replace(
                                    ':contractId', notification.model_id);
                            notificationUrl = notificationUrl + '#chat';
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

        fetchNotifications();

        Echo.private('notifications.' + '<?php echo e(Auth::id()); ?>')
            .listen('NotificationEvent', (e) => {
                console.log('Notification:', e.notification.title);
                if (e.notification.type == 'subscription' || e.notification.title ==
                    'Payment Completed' || e.notification.title ===
                    'Subscription Activated' || e.notification.title == 'Subscription Approved' || e
                    .notification.type == 'Subscription Cancelled') {


                    window.location.reload();
                }

                fetchNotifications();
                playNotificationSoundheader();
            });
    });
</script>


<script>
    document.addEventListener("visibilitychange", function() {
        if (document.hidden) {
            console.log("Browser tab is hidden")
        } else {
            console.log("Browser tab is visible")
        }
    });
    let currentActivityStart = new Date();

    function encryptData(data) {
        return btoa(JSON.stringify(data));
    }

    function decryptData(data) {
        return JSON.parse(atob(data));
    }

    function sendActivityStatus(status, additionalData = {}) {
        const data = encryptData({
            status: status,
            additionalData: additionalData
        });
        $.ajax({
            type: 'POST',
            url: '/update-activity-status',
            data: JSON.stringify({
                data: data
            }),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                return;
            },
            error: function(error) {
                console.error('Error updating activity status:', error);
            }
        });
    }

    function handleActivityStatusChange(status) {
        const now = new Date();
        const activeTime = Math.floor((now - currentActivityStart) / 1000);
        sendActivityStatus(status, {
            activeTime: activeTime
        });

        sessionStorage.setItem('activityData', encryptData({
            currentActivityStart: now
        }));
        currentActivityStart = now;
    }

    // Handle focus event
    $(window).on('focus', function() {

        currentActivityStart = new Date();
        sendActivityStatus('active');

    });

    // Handle blur event
    $(window).on('blur', function() {

        handleActivityStatusChange('inactive');
    });

    // Handle beforeunload event
    $(window).on('beforeunload', function() {

        handleActivityStatusChange('inactive');
    });

    // Handle visibilitychange event
    $(document).on('visibilitychange', function() {

        if (document.hidden) {
            handleActivityStatusChange('inactive');
        } else {
            currentActivityStart = new Date();
            sendActivityStatus('active');

        }
    });

    // Handle page reload
    $(document).ready(function() {

        const activityData = sessionStorage.getItem('activityData');
        if (activityData) {
            const decryptedData = decryptData(activityData);
            currentActivityStart = new Date(decryptedData.currentActivityStart);
        } else {
            currentActivityStart = new Date();
        }
        sendActivityStatus(document.hidden ? 'inactive' : 'active');

    });

    // Handle page unload
    $(window).on('unload', function() {

        handleActivityStatusChange('inactive');
    });
</script>
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/layouts_dashboard/header.blade.php ENDPATH**/ ?>