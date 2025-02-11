<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="user-meetings-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Teacher Name</th>
                            <th>Topic</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Join URL</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#user-meetings-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "<?php echo e(route('student.meetings.datatable')); ?>",
                columns: [{
                        data: 'teacher_name',
                        name: 'teacher_name'
                    },
                    {
                        data: 'meeting.topic',
                        name: 'meeting.topic'
                    },
                    {
                        data: 'meeting.start_time',
                        name: 'meeting.start_time'
                    },
                    {
                        data: 'meeting.duration',
                        name: 'meeting.duration'
                    },
                    {
                        data: 'join_url',
                        name: 'join_url',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    },
                ],
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/student/meetings.blade.php ENDPATH**/ ?>