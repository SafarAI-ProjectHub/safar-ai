<?php $__env->startSection('styles'); ?>
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .widget-card {
            display: flex;
            align-items: center;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
        }

        .widget-icon {
            font-size: 30px;
            margin-left: auto;
        }

        .current-month-widget {
            background-color: #4CAF50;
            color: #fff;
        }

        .previous-month-widget {
            background-color: #2196F3;
            color: #fff;
        }

        .difference-widget {
            background-color: #f44336;
            color: #fff;
        }

        .positive-difference-widget {
            background-color: #4CAF50;
            color: #fff;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    

    <a href="<?php echo e(route('teachers.logs.index')); ?>" class="btn btn-primary mb-3">
        <i class='bx bx-arrow-back'></i> Back
    </a>
    <div class="card">
        <div class="card-body">
            <h5>Activity Logs for :<?php echo e($user->full_name); ?></h5>
            <div class="table-responsive">
                <table id="logs-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Login Time</th>
                            <th>End Time</th>
                            <th>Total Active Time (hours)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 d-flex">
        <div class="card radius-10 w-100">
            <div class="card-body">
                <p class="font-weight-bold mb-1 text-secondary">
                    Monthly Activity
                </p>
                <div class="mt-4 d-flex justify-content-between col-12">
                    <div class="widget-card current-month-widget col-3">
                        <div>
                            <p class="mb-0">Total Active Time This Month</p>
                            <h5 id="currentMonthTotal" class="mb-0"></h5>
                        </div>
                        <i class="bx bx-time-five widget-icon"></i>
                    </div>
                    <div class="widget-card previous-month-widget col-3">
                        <div>
                            <p class="mb-0">Total Active Time Last Month</p>
                            <h5 id="previousMonthTotal" class="mb-0"></h5>
                        </div>
                        <i class="bx bx-time widget-icon"></i>
                    </div>
                    <div class="widget-card difference-widget col-3" id="differenceWidget">
                        <div>
                            <p class="mb-0">Difference(from last Month)</p>
                            <h5 id="monthDifference" class="mb-0"></h5>
                        </div>
                        <i class="bx bx-trending-up widget-icon"></i>
                    </div>
                </div>
                <div class="chart-container-0 mt-5">
                    <canvas id="activityChart"></canvas>
                </div>

            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            var ctx = document.getElementById('activityChart').getContext('2d');
            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, '#00b09b');
            gradientStroke1.addColorStop(1, '#96c93d');
            var myChart;

            function convertToHoursMinutes(decimalHours) {
                const totalMinutes = Math.round(decimalHours * 60);
                const hours = Math.floor(totalMinutes / 60);
                const minutes = totalMinutes % 60;
                if (minutes === 0) {
                    return hours + ' hour' + (hours !== 1 ? 's' : '');
                } else {
                    return hours + ' hour' + (hours !== 1 ? 's' : '') + ' and ' + minutes + ' minute' + (minutes !==
                        1 ? 's' : '');
                }
            }

            function createChart(data) {
                if (myChart) {
                    myChart.destroy();
                }

                var dailyActivity = data.currentMonthData.map(seconds => (seconds / 3600).toFixed(2));
                var previousMonthTotal = data.previousMonthTotal / 3600;
                var currentMonthTotal = data.currentMonthTotal / 3600;

                if (isNaN(currentMonthTotal)) currentMonthTotal = 0;
                if (isNaN(previousMonthTotal)) previousMonthTotal = 0;
                if (currentMonthTotal === 0 && previousMonthTotal === 0) {
                    $('#currentMonthTotal').text('0 hours');
                    $('#previousMonthTotal').text('0 hours');
                    $('#monthDifference').text('0%');

                } else {
                    var difference = previousMonthTotal === 0 ? 100 : ((currentMonthTotal - previousMonthTotal) /
                        previousMonthTotal * 100).toFixed(2);

                    $('#currentMonthTotal').text(convertToHoursMinutes(currentMonthTotal));
                    $('#previousMonthTotal').text(convertToHoursMinutes(previousMonthTotal));

                    var differenceText = difference + '%';
                    $('#monthDifference').text(differenceText);

                    var differenceWidget = $('#differenceWidget');
                    if (difference >= 0) {
                        differenceWidget.removeClass('difference-widget').addClass('positive-difference-widget');
                    } else {
                        differenceWidget.removeClass('positive-difference-widget').addClass('difference-widget');
                    }
                }

                var daysInMonth = Array.from({
                    length: dailyActivity.length
                }, (_, i) => i + 1);

                myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: daysInMonth,
                        datasets: [{
                            label: 'Activity Time (hours)',
                            data: dailyActivity,
                            backgroundColor: gradientStroke1,
                            fill: {
                                target: 'origin',
                                above: 'rgb(21 202 32 / 15%)',
                            },
                            tension: 0.4,
                            borderColor: gradientStroke1,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false,
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function getTeacherIdFromUrl() {
                var pathArray = window.location.pathname.split('/');
                var teacherId = pathArray[pathArray.length - 1];
                return teacherId;
            }


            var teacherId = getTeacherIdFromUrl();


            $.ajax({
                url: '/admin/teacher/' + teacherId + '/monthly-activity',
                method: 'GET',
                success: function(response) {
                    createChart(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching monthly activity:', error);
                }
            });

            $('#logs-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '<?php echo e(route('teacher.logs.show', $id)); ?>',
                    dataSrc: function(json) {
                        json.data.forEach(log => {
                            var hours = log.total_active_time / 3600;
                            log.total_active_time = convertToHoursMinutes(hours);
                        });

                        return json.data;
                    }
                },
                columns: [{
                        data: 'login_time',
                        name: 'login_time'
                    },
                    {
                        data: 'end_time',
                        name: 'end_time'
                    },
                    {
                        data: 'total_active_time',
                        name: 'total_active_time'
                    },
                    {
                        data: 'session_status',
                        name: 'session_status'
                    },
                ],
                columnDefs: [{
                    targets: 3,
                    width: '10%'
                }],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-outline-secondary buttons-copy buttons-html5'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary buttons-excel buttons-html5'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-secondary buttons-pdf buttons-html5'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary buttons-print'
                    }
                ],
                lengthChange: false,
                order: [
                    [0, 'desc']
                ]
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/admin/teacher/logs.blade.php ENDPATH**/ ?>