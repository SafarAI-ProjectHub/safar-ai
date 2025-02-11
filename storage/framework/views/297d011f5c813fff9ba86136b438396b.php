<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <h5>Manage Activities and Questions</h5>
            <div class="d-flex justify-content-end mb-3">
                <a href="<?php echo e(route('quiz.addPage')); ?>" class="btn btn-sm btn-primary">Add New Activity</a>
            </div>
            <div class="table-responsive">
                <table id="quizzes-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Activity Title</th>
                            <th>Lesson</th>
                            <th>Unit</th>
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
            var table = $('#quizzes-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('quizzes.datatable')); ?>',
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'unit.title',
                        name: 'unit.title'
                    },
                    {
                        data: 'unit.course.title',
                        name: 'unit.course.title'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-outline-secondary'
                    },
                    {
                        extend: 'print',
                        className: 'btn btn-outline-secondary'
                    }
                ],
                columnDefs: [{
                    targets: 3,
                    width: '30%'
                }],
                lengthChange: false
            });

            // Handle Edit button click
            $(document).on('click', '.edit-quiz', function() {
                var quizId = $(this).data('id');
                window.location.href = '/quizzes/' + quizId + '/edit';
            });

            // Handle Delete button click
            $(document).on('click', '.delete-quiz', function() {
                var quizId = $(this).data('id');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '/quizzes/' + quizId + '/delete',
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your activity has been deleted.',
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(response) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the activity.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/quiz/quizzes.blade.php ENDPATH**/ ?>