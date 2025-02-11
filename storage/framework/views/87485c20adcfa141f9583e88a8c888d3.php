<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="applications-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Position</th>
                            <th>CV Link</th>
                            <th>Years of Experience</th>
                            <th>Exam result</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="statusForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusModalLabel">Update Status</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="teacher_id" id="teacher_id">
                        <div class="mb-3">
                            <label for="approval_status" class="form-label">Approval Status</label>
                            <select class="form-select" id="approval_status" name="approval_status">
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View CV Modal -->
    <div class="modal fade" id="viewCvModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">View CV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="cvFrame" src="" width="100%" height="500px"></iframe>
                </div>
            </div>
        </div>
    </div>
    <!-- Assessment Modal -->
    <div class="modal fade" id="assessmentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Teacher Assessment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <dev class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h2>Instructions</h2>
                        </div>
                        <dev class="card-body mb-3">

                            <p>1. Mark the correct answers and provide admin review for each question.</p>
                            <p>2. Make sure Voice questions are reviewed by listening to the audio.</p>
                            <p>3. Click on the "Save Changes" button to save the updates.</p>
                        </dev>
                    </dev>
                    <div id="assessmentDetails">
                        <!-- Assessment details will be dynamically loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('scripts'); ?>
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            var table = $('#applications-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '<?php echo e(route('admin.getApplicationsIndex')); ?>',
                columns: [{
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'position',
                        name: 'position'
                    },
                    {
                        data: 'cv_link',
                        render: function(data, type, row, meta) {
                            return `<a href="<?php echo e(asset('${data}')); ?>" class="view-cv" target="_blank">View CV</a>`;
                        }
                    },
                    {
                        data: 'years_of_experience',
                        name: 'years_of_experience'
                    },
                    {
                        data: 'exam_result',
                        name: 'exam_result',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status'
                    },
                    {
                        data: null,
                        name: 'actions',
                        render: function(data, type, row) {
                            return '<button class="btn btn-primary btn-sm update-status" data-id="' +
                                row.id + '" data-status="' + row.approval_status +
                                '">Update Status</button>';
                        },
                        orderable: false,
                        searchable: false
                    }
                ],
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
                lengthChange: false
            });


            table.buttons().container().appendTo('#applications-table_wrapper .col-md-6:eq(0)');

            $('#applications-table').on('click', '.update-status', function() {
                var teacherId = $(this).data('id');
                var currentStatus = $(this).data('status');
                $('#teacher_id').val(teacherId);
                $('#approval_status').val(currentStatus);
                $('#statusModal').modal('show');
            });

            $('#statusForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: '<?php echo e(route('admin.updateTeacherStatus')); ?>',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#statusModal').modal('hide');
                        table.ajax.reload();
                        showAlert('success', 'Status updated successfully!',
                            'bxs-check-circle');
                    },
                    error: function(response) {
                        showAlert('danger', 'Error updating status', 'bxs-message-square-x');
                    }
                });
            });

            $('#applications-table').on('click', '.view-cv', function(event) {
                event.preventDefault();
                var cvLink = $(this).attr('href');
                var extension = cvLink.split('.').pop().toLowerCase();

                if (extension === 'pdf') {
                    $('#cvFrame').attr('src', cvLink);
                } else if (extension === 'docx') {
                    $('#cvFrame').attr('src', 'https://docs.google.com/gview?url=' + encodeURIComponent(
                        cvLink) + '&embedded=true');
                } else {
                    $('#cvFrame').attr('src', '');
                    alert('Unsupported file type.');
                }

                $('#viewCvModal').modal('show');
            });

            $('#applications-table').on('click', '.view-assessment', function() {
                var teacherId = $(this).data('id');
                $.get('/admin/teachers/' + teacherId + '/assessments', function(data) {
                    var assessmentsHtml = '';
                    data.assessments.forEach(function(assessment) {
                        var responseContent = '';
                        if (assessment?.question?.question_type === 'voice') {
                            responseContent =
                                `<audio controls><source src="/storage/${assessment.response}" type="audio/wav">Your browser does not support the audio element.</audio>`;
                        } else {
                            responseContent = assessment?.response || '';
                        }


                        assessmentsHtml += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Question: ${assessment?.question?.question_text || 'No question text available'}</h5>

                    </div>
                    <div class="card-body">
                        <p><strong>Answer:</strong> ${responseContent}</p>
                        <p><strong>Correct:</strong> ${assessment.correct ? 'Yes' : 'No'}</p>
                        <p><strong>AI Review:</strong> ${assessment.ai_review}</p>
                        <p><strong>Admin Review:</strong> ${assessment.admin_review || ''}</p>
                        <div class="mb-3">
                            <label for="correct_${assessment.id}" class="form-label">Mark as Correct</label>
                            <input type="checkbox" class="form-check-input" id="correct_${assessment.id}" ${assessment.correct ? 'checked' : ''} data-id="${assessment.id}" data-teacher="${teacherId}">
                        </div>
                        <div class="mb-3">
                            <label for="admin_review_${assessment.id}" class="form-label">Admin Review</label>
                            <textarea class="form-control" id="admin_review_${assessment.id}" rows="3">${assessment.admin_review ? assessment.admin_review : ''}</textarea>
                        </div>
                    </div>
                </div>
            `;
                    });
                    $('#assessmentDetails').html(assessmentsHtml);
                    $('#assessmentModal').modal('show');
                });
            });

            $('#saveChanges').on('click', function() {
                $('#assessmentModal .card').each(function() {
                    var assessmentId = $(this).find('input[type="checkbox"]').data('id');
                    var teacherId = $(this).find('input[type="checkbox"]').data('teacher');
                    var isCorrect = $(this).find('input[type="checkbox"]').is(':checked');
                    var adminReview = $(this).find('textarea').val();

                    $.ajax({
                        url: '/admin/teachers/' + teacherId + '/assessments/' +
                            assessmentId,
                        method: 'PUT',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            correct: isCorrect,
                            admin_review: adminReview
                        },
                        success: function(response) {
                            $('#assessmentModal').modal('hide');
                            table.ajax.reload();
                            showAlert('success', 'Assessment updated successfully!',
                                'bxs-check-circle');
                        },
                        error: function(response) {
                            showAlert('danger', 'Error updating assessment',
                                'bxs-message-square-x');
                        }
                    });
                });
            });


            function showAlert(type, message, icon) {
                var alertHtml = `
                    <div class="alert alert-${type} border-0 bg-${type} alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="font-35 text-white">
                                <i class="bx ${icon}"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="mb-0 text-white">${type.charAt(0).toUpperCase() + type.slice(1)}</h6>
                                <div class="text-white">${message}</div>
                            </div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                $('body').append(alertHtml);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/admin/applications.blade.php ENDPATH**/ ?>