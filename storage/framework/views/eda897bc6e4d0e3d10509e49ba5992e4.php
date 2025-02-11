<?php $__env->startSection('styles'); ?>
    <link href="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css')); ?>" rel="stylesheet">

    <style>
        .bg-paypal {
            background-color: #009cde;
        }

        .bg-cliq {
            background-color: #f5a623;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <!--start page wrapper -->

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Active Subscriptions
                            </p>
                            <h4 class="my-1 text-info">
                                <?php echo e($data['total_Active_subscription']); ?>

                            </h4>
                            <p class="mb-0 font-13"
                                style="color: <?php echo e($data['mom_active_subscription'] < 0 ? 'red' : 'green'); ?>">
                                <?php echo e($data['mom_active_subscription'] < 0 ? '' : '+'); ?>

                                <?php echo e(number_format($data['mom_active_subscription'], 2)); ?>% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto">
                            <i class='bx bxs-cart'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Total Revenue
                            </p>
                            <h4 class="my-1 text-danger">
                                $<?php echo e(number_format($data['total_payment'], 2)); ?>

                            </h4>
                            <p class="mb-0 font-13" style="color: <?php echo e($data['mom_payment'] < 0 ? 'red' : 'green'); ?>">
                                <?php echo e($data['mom_payment'] < 0 ? '' : '+'); ?>

                                <?php echo e(number_format($data['mom_payment'], 2)); ?>% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto">
                            <i class='bx bxs-wallet'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Revenue Growth
                            </p>
                            <h4 class="my-1 text-success">
                                <?php echo e(number_format($data['mom_growth'], 2)); ?>%
                            </h4>
                            <p class="mb-0 font-13" style="color: <?php echo e($data['mom_growth'] < 0 ? 'red' : 'green'); ?>">
                                <?php echo e($data['mom_growth'] < 0 ? '' : '+'); ?>

                                <?php echo e(number_format($data['mom_growth'], 2)); ?>% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto">
                            <i class='bx bxs-bar-chart-alt-2'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-start border-0 border-4 border-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0 text-secondary">
                                Total Students
                            </p>
                            <h4 class="my-1 text-warning">
                                <?php echo e($data['total_student']); ?>

                            </h4>
                            <p class="mb-0 font-13" style="color: <?php echo e($data['mom_student'] < 0 ? 'red' : 'green'); ?>">
                                <?php echo e($data['mom_student'] < 0 ? '-' : '+'); ?>

                                <?php echo e(number_format($data['mom_student'], 2)); ?>% from last month
                            </p>
                        </div>
                        <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto">
                            <i class='bx bxs-group'></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->

    <div class="row">
        <div class="col-12 col-lg-8 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">
                                Sales Overview
                            </h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                                <i class='bx bx-dots-horizontal-rounded font-22 text-option'></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center ms-auto font-13 gap-2 mb-3">
                        <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                style="color: #14abef"></i>Students</span>
                        <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                style="color: #ffc107"></i>Subscriptions</span>
                        <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1"
                                style="color: #ff8359"></i>Payments</span>
                    </div>
                    <div class="chart-container-1">
                        <canvas id="chart1"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">
                                Payment Methods
                            </h6>
                        </div>
                        
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container-2">
                        <canvas id="chart2"></canvas>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li
                            class="list-group-item d-flex bg-transparent justify-content-between align-items-center border-top">
                            Cliq
                            <span class="badge bg-cliq rounded-pill"><?php echo e($data['yearly_payments_cliq']); ?></span>
                        </li>
                        <li class="list-group-item d-flex bg-transparent justify-content-between align-items-center">
                            Paypal
                            <span class="badge bg-paypal rounded-pill"><?php echo e($data['yearly_payments_paypal']); ?></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!--end row-->


    <div class="card radius-10">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <div>
                    <h6 class="mb-0">Recent Subscriptions</h6>
                </div>

            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Subscription ID</th>
                            <th>Status</th>
                            <th>Total Paid</th>
                            <th>Subscription Type</th>
                            <th>Start Date</th>
                            <th>Next Billing Date / End Date </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $data['recent_subscriptions']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($subscription->user->full_name); ?></td>
                                <td><?php echo e($subscription->id); ?></td>
                                <td><span
                                        class="badge <?php echo e($subscription->status == 'active' ? 'bg-success' : 'bg-danger'); ?>"><?php echo e($subscription->status); ?></span>
                                </td>
                                <td>$<?php echo e(number_format($subscription->payments->sum('amount'), 2)); ?></td>
                                <td>
                                    <?php if($subscription->latestPayment): ?>
                                        <span
                                            class="badge <?php echo e($subscription->latestPayment->payment_type == 'paypal' ? 'bg-success' : 'bg-warning'); ?>">
                                            <?php echo e($subscription->latestPayment->payment_type == 'paypal' ? 'Monthly' : 'One Time'); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No Payment</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($subscription->created_at->format('d M Y')); ?></td>
                                <td><?php echo e($subscription->next_billing_time ? $subscription->next_billing_time->format('d M Y') : 'N/A'); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-12 col-lg-7 col-xl-8 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div>
                            <h6 class="mb-0">
                                Geographic Distribution
                            </h6>
                        </div>

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-7 col-xl-8 border-end">
                            <div id="geographic-map-2" style="height: 400px;"></div>
                        </div>
                        <div class="col-lg-5 col-xl-4">
                            <?php $__currentLoopData = $data['country_data']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country => $counts): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="mb-4">
                                    <p class="mb-2">
                                        <i class="flag-icon flag-icon-<?php echo e(strtolower($country)); ?> me-1"></i>
                                        <?php echo e($country); ?>

                                        <span
                                            class="float-end"><?php echo e(round((($counts['students'] + $counts['teachers']) /array_sum(array_map(function ($c) {return $c['students'] + $c['teachers'];}, $data['country_data']))) *100,2)); ?>%</span>
                                    </p>
                                    <div class="progress" style="height: 7px;">
                                        <div class="progress-bar bg-primary progress-bar-striped" role="progressbar"
                                            style="width: <?php echo e(round((($counts['students'] + $counts['teachers']) /array_sum(array_map(function ($c) {return $c['students'] + $c['teachers'];}, $data['country_data']))) *100,2)); ?>%">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="col-12 col-lg-5 col-xl-4 d-flex">
            <div class="card w-100 radius-10">
                <div class="card-body">
                    <div class="card radius-10 border shadow-none mt-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">
                                        Total Units
                                    </p>
                                    <h4 class="my-1">
                                        <?php echo e($data['total_courses']); ?>

                                    </h4>
                                    <p class="mb-0 font-13">
                                        +<?php echo e(number_format($data['wow_enrolled_users'], 2)); ?>%
                                        from
                                        last week
                                    </p>
                                </div>
                                <div class="widgets-icons-2 bg-gradient-cosmic text-white ms-auto">
                                    <i class='bx bxs-book'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card radius-10 border shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">
                                        Total Lessons
                                    </p>
                                    <h4 class="my-1">
                                        <?php echo e($data['total_units']); ?>

                                    </h4>
                                    <p class="mb-0 font-13">
                                        +<?php echo e(number_format($data['wow_enrolled_users'], 2)); ?>%
                                        from
                                        last week
                                    </p>
                                </div>
                                <div class="widgets-icons-2 bg-gradient-ibiza text-white ms-auto">
                                    <i class='bx bxs-bookmark-alt'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card radius-10 mb-0 border shadow-none">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <p class="mb-0 text-secondary">
                                        Total Users Enrolled
                                    </p>
                                    <h4 class="my-1">
                                        <?php echo e($data['enrolled_users_by_week']); ?>

                                    </h4>
                                    <p class="mb-0 font-13">
                                        +<?php echo e(number_format($data['wow_enrolled_users'], 2)); ?>%
                                        from
                                        last week
                                    </p>
                                </div>
                                <div class="widgets-icons-2 bg-gradient-kyoto text-dark ms-auto">
                                    <i class='bx bxs-user-check'></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->

    <div class="row row-cols-1 row-cols-lg-12">

        <div class="col-12 d-flex">
            <div class="card radius-10 w-100">
                <div class="card-body">
                    <p class="font-weight-bold mb-1 text-secondary">
                        Monthly Revenue
                    </p>
                    <div class="d-flex align-items-center mb-4">
                        <div>
                            <h4 class="mb-0">
                                $<?php echo e(number_format($data['current_month_revenue2'], 2)); ?>

                            </h4>
                        </div>
                        <div class="">
                            <p
                                class="mb-0 align-self-center font-weight-bold <?php echo e($data['revenue_change'] >= 0 ? 'text-success' : 'text-danger'); ?> ms-2">
                                <?php echo e($data['revenue_change'] >= 0 ? '+' : ''); ?><?php echo e(number_format($data['revenue_change'], 2)); ?>%
                                <i
                                    class="bx <?php echo e($data['revenue_change'] >= 0 ? 'bxs-up-arrow-alt' : 'bxs-down-arrow-alt'); ?> mr-2"></i>
                            </p>
                        </div>
                    </div>
                    <div class="chart-container-0 mt-5">
                        <canvas id="chart3"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div><!--end row-->


    <!--end page wrapper -->
    <!--start overlay-->
    <div class="overlay toggle-icon"></div>
    <!--end overlay-->
    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->
    

    <!--end wrapper-->
    <script>
        var yearlyStudents = <?php echo json_encode($data['yearly_students'], 15, 512) ?>;
        var yearlySubscriptions = <?php echo json_encode($data['yearly_subscriptions'], 15, 512) ?>;
        var yearlyPayments = <?php echo json_encode($data['yearly_payments'], 15, 512) ?>;
        var yearlyPaymentsPaypal = <?php echo json_encode($data['yearly_payments_paypal'], 15, 512) ?>;
        var yearlyPaymentsCliq = <?php echo json_encode($data['yearly_payments_cliq'], 15, 512) ?>;
        var countryData = <?php echo json_encode($data['country_data'], 15, 512) ?>;
        var dailyRevenue = <?php echo json_encode(array_values($data['daily_revenue']), 15, 512) ?>;
    </script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <!-- jQuery -->
    <script src="<?php echo e(asset('assets/plugins/jquery/jquery-3.6.0.min.js')); ?>"></script>

    <!-- jVectorMap CSS -->

    <!-- jVectorMap JS -->
    <script src="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/index.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/index.blade.php ENDPATH**/ ?>