<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?php echo e(asset('assets/images/logo-icon.png')); ?>" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text"><span style="color: #844DCD"><span style="color:#C45ACD">Safar</span> AI</span></h4>
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i></div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <?php
            $currentPath = parse_url(url()->current(), PHP_URL_PATH);
            $teacherPath = parse_url(asset('/teacher'), PHP_URL_PATH);
            $href = $currentPath != $teacherPath ? $teacherPath : '#';
        ?>
        <?php if(auth()->user()->hasRole('Teacher') && auth()->user()->teacher->approval_status == 'pending'): ?>
            <li>
                <a href="<?php echo e($href); ?>" onclick="showPendingApprovalAlert()">
                    <div class="parent-icon"><i class='bx bx-time-five'></i></div>
                    <div class="menu-title">Pending Approval</div>
                </a>
            </li>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
            <li>
                <a href="<?php echo e(route('dashboard')); ?>">
                    <div class="parent-icon"><i class='bx bx-tachometer'></i></div>
                    <div class="menu-title">Dashboard</div>
                </a>
            </li>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Student')): ?>

            <li>
                <a href="<?php echo e(route('student.dashboard')); ?>">
                    <div class="parent-icon"><i class='bx bx-home'></i></div>
                    <div class="menu-title">Home</div>
                </a>
            </li>
            <?php if(auth()->user()->student->subscription_status === 'subscribed'): ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-book-open'></i></div>
                        <div class="menu-title">Units</div>
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo e(route('student.myCourses')); ?>">
                                <div class="parent-icon"><i class='bx bx-book'></i></div>
                                <div class="menu-title">My Units</div>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('student.myCertificates')); ?>">
                                <div class="parent-icon"><i class='bx bx-award'></i></div>
                                <div class="menu-title">My Certificates</div>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-task'></i></div>
                        <div class="menu-title">Activities</div>
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo e(route('student.quizzes.list')); ?>">
                                <div class="parent-icon"><i class="bx bx-list-ul"></i></div>
                                <div class="menu-title">Available Activities</div>
                            </a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin')): ?>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx  bxs-user-detail'></i></div>
                    <div class="menu-title">Admins</div>
                </a>
                <ul>
                    <li>
                        <a href="<?php echo e(route('admin.list')); ?>">
                            <div class="parent-icon"><i class="bx bx-list-ul"></i></div>
                            <div class="menu-title">Admins List</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.create')); ?>">
                            <div class="parent-icon"><i class="bx bx-plus-circle"></i></div>
                            <div class="menu-title">Create Admin</div>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-user-circle'></i></div>
                    <div class="menu-title">Teachers</div>
                </a>
                <ul>
                    <li>
                        <a href="<?php echo e(route('admin.applications')); ?>">
                            <div class="parent-icon"><i class="bx bx-file"></i></div>
                            <div class="menu-title">Applications Review</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.teachers')); ?>">
                            <div class="parent-icon"><i class="bx bx-check-circle"></i></div>
                            <div class="menu-title">Approved Teachers</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('contracts.index')); ?>">
                            <div class="parent-icon"><i class="bx bx-file-find"></i></div>
                            <div class="menu-title">Contracts</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.getTeachersAssessments')); ?>">
                            <div class="parent-icon"><i class="bx bxs-graduation"></i></div>
                            <div class="menu-title">Teachers Assessments</div>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('teacherTests.index')); ?>">
                            <div class="parent-icon"><i class="bx bx-bar-chart-alt"></i></div>
                            <div class="menu-title">Manage Level Tests</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('teacherTest.addPage')); ?>">
                            <div class="parent-icon"><i class="bx bx-plus-circle"></i></div>
                            <div class="menu-title">Add Level Test</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('teachers.logs.index')); ?>">
                            <div class="parent-icon"><i class="bx bx-time"></i></div>
                            <div class="menu-title">Active Time</div>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin|Teacher')): ?>
            <?php if(auth()->user()->hasRole('Teacher') && auth()->user()->teacher->approval_status == 'pending'): ?>
            <?php else: ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-book-open'></i></div>
                        <div class="menu-title">Units</div>
                    </a>
                    <ul>
                        <li>
                            <a href="<?php echo e(route('admin.courses')); ?>">
                                <div class="parent-icon"><i class="bx bx-book"></i></div>
                                <div class="menu-title">
                                    <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
                                        Manage Units
                                    <?php else: ?>
                                        My Units
                                    <?php endif; ?>
                                </div>
                            </a>
                        </li>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
                            <li>
                                <a href="<?php echo e(route('manage.permissions')); ?>">
                                    <div class="parent-icon"><i class="bx bx-lock"></i></div>
                                    <div class="menu-title">Create Unit Permissions</div>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li>
                            <a href="<?php echo e(route('quizzes.index')); ?>">
                                <div class="parent-icon"><i class="bx bx-brain"></i></div>
                                <div class="menu-title">Activities</div>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('quiz.addPage')); ?>">
                                <div class="parent-icon"><i class="bx bx-plus-circle"></i></div>
                                <div class="menu-title">Add Activity</div>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('quizResults.index')); ?>">
                                <div class="parent-icon"><i class="bx bx-clipboard"></i></div>
                                <div class="menu-title">Activity Results</div>
                            </a>
                        </li>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
                            <li>
                                <a href="<?php echo e(route('admin.reviews.index')); ?>">
                                    <div class="parent-icon"><i class="bx bx-star"></i></div>
                                    <div class="menu-title">Reviews</div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
            <li>
                <a href="<?php echo e(route('youtube_videos.index')); ?>">
                    <div class="parent-icon"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em"
                            viewBox="0 0 24 24">
                            <path fill="currentColor"
                                d="M21.593 7.203a2.506 2.506 0 0 0-1.762-1.766C18.265 5.007 12 5 12 5s-6.264-.007-7.831.404a2.56 2.56 0 0 0-1.766 1.778c-.413 1.566-.417 4.814-.417 4.814s-.004 3.264.406 4.814c.23.857.905 1.534 1.763 1.765c1.582.43 7.83.437 7.83.437s6.265.007 7.831-.403a2.515 2.515 0 0 0 1.767-1.763c.414-1.565.417-4.812.417-4.812s.02-3.265-.407-4.831M9.996 15.005l.005-6l5.207 3.005z" />
                        </svg></div>
                    <div class="menu-title">Youtube Videos</div>
                </a>
            </li>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin|Teacher')): ?>
            <?php if(auth()->user()->hasRole('Teacher') && auth()->user()->teacher->approval_status == 'pending'): ?>
            <?php else: ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-user'></i></div>
                        <div class="menu-title">Students</div>
                    </a>
                    <ul>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
                            <li>
                                <a href="<?php echo e(route('admin.students')); ?>">
                                    <div class="parent-icon"><i class="bx bx-group"></i></div>
                                    <div class="menu-title">Students List</div>
                                </a>
                            </li>

                            <li>
                                <a href="<?php echo e(route('admin.getStudentsAssessments')); ?>">
                                    <div class="parent-icon"><i class="bx bxs-graduation"></i></div>
                                    <div class="menu-title">Students Assessments</div>
                                </a>
                            </li>

                            <li>
                                <a href="<?php echo e(route('studentTests.index')); ?>">
                                    <div class="parent-icon"><i class="bx bx-bar-chart-alt"></i></div>
                                    <div class="menu-title">Manage Level Tests</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('studentTest.addPage')); ?>">
                                    <div class="parent-icon"><i class="bx bx-plus-circle"></i></div>
                                    <div class="menu-title">Add Level Test</div>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Teacher')): ?>
                            <li>
                                <a href="<?php echo e(route('teacher.getStudentProfiles')); ?>">
                                    <div class="parent-icon"><i class="bx bx-group"></i></div>
                                    <div class="menu-title">Student Profiles</div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin|Teacher|Student')): ?>
            <?php if(auth()->user()->hasRole('Teacher') && auth()->user()->teacher->approval_status == 'pending'): ?>
            <?php elseif(auth()->user()->getAgeGroup() == '1-5' && auth()->user()->hasRole('Student')): ?>
            <?php else: ?>
                <li>
                    <a href="javascript:;" class="has-arrow">
                        <div class="parent-icon"><i class='bx bx-video'></i></div>
                        <div class="menu-title">Zoom</div>
                    </a>
                    <ul>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin|Teacher')): ?>
                            <li>
                                <a href="<?php echo e(route('zoom-meetings.index')); ?>">
                                    <div class="parent-icon"><i class="bx bx-calendar"></i></div>
                                    <div class="menu-title">Zoom Meetings</div>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo e(route('zoom-meetings.create')); ?>">
                                    <div class="parent-icon"><i class="bx bx-plus-circle"></i></div>
                                    <div class="menu-title">Schedule Meeting</div>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Student')): ?>
                            <li>
                                <a href="<?php echo e(route('student.meetings.index')); ?>">
                                    <div class="parent-icon"><i class="bx bx-meeting"></i></div>
                                    <div class="menu-title">My Zoom Meetings</div>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </li>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Student')): ?>

            <?php if(auth()->user()->getAgeGroup() !== '1-5'): ?>
                <li>
                    <a href="<?php echo e(route('subscription.details')); ?>">
                        <div class="parent-icon"><i class='bx bx-credit-card'></i></div>
                        <div class="menu-title">My Subscription</div>
                    </a>
                </li>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
            <li>
                <a href="javascript:;" class="has-arrow">
                    <div class="parent-icon"><i class='bx bx-wallet'></i></div>
                    <div class="menu-title">Subscription</div>
                </a>
                <ul>
                    <li>
                        <a href="<?php echo e(route('showPendingPayments')); ?>">
                            <div class="parent-icon"><i class="bx bx-time"></i></div>
                            <div class="menu-title">Pending Cliq Payments</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.subscriptions')); ?>">
                            <div class="parent-icon"><i class="bx bx-check-circle"></i></div>
                            <div class="menu-title">Active Subscriptions</div>
                        </a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('admin.inactive_subscriptions')); ?>">
                            <div class="parent-icon"><i class="bx bx-x-circle"></i></div>
                            <div class="menu-title">Inactive Subscriptions</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.payments')); ?>">
                            <div class="parent-icon"><i class="bx bx-receipt"></i></div>
                            <div class="menu-title">Payments</div>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo e(route('admin.subscriptions.index')); ?>">
                            <div class="parent-icon"><i class="bx bx-layer-plus"></i></div>
                            <div class="menu-title">Manage Subscriptions</div>
                        </a>
                    </li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
            <li>
                <a href="<?php echo e(route('offers.index')); ?>">
                    <div class="parent-icon"><i class='bx bx-gift'></i></div>
                    <div class="menu-title">Offers</div>
                </a>
            </li>
            <li>
                <a href="<?php echo e(route('admin.contact_forms.index')); ?>">
                    <div class="parent-icon"><i class='bx bx-envelope'></i></div>
                    <div class="menu-title">Contact Form</div>
                </a>
            </li>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Teacher')): ?>
            <li>
                <a href="<?php echo e(route('contracts.myContract')); ?>">
                    <div class="parent-icon"><i class='bx bx-file'></i></div>
                    <div class="menu-title">My Contract</div>
                </a>
            </li>
        <?php endif; ?>

    </ul>
    <!--end navigation-->
</div>
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/layouts_dashboard/sidebar.blade.php ENDPATH**/ ?>