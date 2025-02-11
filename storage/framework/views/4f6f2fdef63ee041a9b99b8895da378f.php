<?php $__env->startSection('styles'); ?>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="<?php echo e(asset('assets/plugins/select2/css/select2.min.css')); ?>" rel="stylesheet" />
    <link href="<?php echo e(asset('assets/plugins/select2/css/select2-bootstrap4.css')); ?>" rel="stylesheet" />
    <style>
        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <h5>Units List</h5>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create courses')): ?>
                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add New
                        Unit</button>
                </div>
            <?php endif; ?>
            <div class="table-responsive">
                <table id="courses-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Level</th>
                            <th>Type</th>
                            <th>Teacher</th>
                            <th>Block</th> 
                            <th>Completed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create courses')): ?>
    <!-- Add Course Modal -->
            <div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content">
                        <form id="addCourseForm" enctype="multipart/form-data">
                            <?php echo csrf_field(); ?>
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCourseModalLabel">Add New Unit</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <div class="mb-3">
                                    <label for="title" class="form-label">Unit Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($category->id); ?>"><?php echo e($category->age_group); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="level" class="form-label">Level</label>
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="" disabled selected>Select Level</option>
                                        <?php for($i = 1; $i <= 6; $i++): ?>
                                            <option value="<?php echo e($i); ?>">Level <?php echo e($i); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="" disabled selected>Select Type</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="intensive">Intensive</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                <label for="block_name" class="form-label">Block</label>
                                <label for="block_id" class="form-label">Block</label>
                                <select class="form-select" id="block_id" name="block_id" required>
                                    <option value="" disabled selected>Select Block</option>
                                    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($block->id); ?>"><?php echo e($block->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>




                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Upload Image</label>
                                    <input type="file" class="filepond" name="image" data-allow-reorder="true"
                                        data-max-file-size="5MB" data-max-files="1">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Unit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div> 
 <?php endif; ?>


    <?php if (\Illuminate\Support\Facades\Blade::check('hasanyrole', 'Super Admin|Admin')): ?>
        <!-- Assign Teacher Modal -->
        <div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <form id="assignTeacherForm">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="assignTeacherModalLabel">Assign Teacher to Unit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="assignCourseId" name="course_id">
                            <div class="mb-3">
                                <label for="teacher_id" class="form-label">Select Teacher</label>
                                <select class="form-select" id="teacher_id" name="teacher_id" required style="width: 100%;">
                                    <option value="" disabled selected>Select Teacher</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Assign Teacher</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <input type="hidden" id="delete_source" value="<?php echo e(route('admin.courses.delete')); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="<?php echo e(asset('assets/plugins/select2/js/select2.min.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        var table = $('#courses-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '<?php echo e(route('admin.getCourses')); ?>',
            columns: [
                {
                    data: 'id',
                    name: 'id',
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'description',
                    name: 'description',
                    render: function(data) {
                        return data.length > 100 ? data.substring(0, 50) + '...' : data;
                    }
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'level',
                    name: 'level'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'teacher',
                    name: 'teacher',
                    defaultContent: 'N/A'
                },
                {
                    data: 'block_name', 
                    name: 'block_name',
                    defaultContent: 'No Block'
                },
                {
                    data: 'completed',
                    name: 'completed',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `<div class="form-check form-switch">
                                <input class="form-check-input toggle-complete" type="checkbox" data-id="${row.id}" ${data ? 'checked' : ''}>
                            </div>`;
                    }
                },
                {
                    data: 'actions',
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }
            ],

            columnDefs: [{
                visible: false,
                targets: 0
            }],
            dom: 'Bfrtip',
            buttons: [
                {
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

        // Handle toggle complete switch
        $('#courses-table').on('change', '.toggle-complete', function() {
            var courseId = $(this).data('id');
            var completed = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: `/courses/${courseId}/toggle-complete`,
                method: 'POST',
                data: {
                    _token: '<?php echo e(csrf_token()); ?>',
                    completed: completed
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload(null, false);
                        showAlert('success', 'Unit completion status updated successfully!', 'bxs-check-circle');
                    } else {
                        showAlert('danger', 'Error updating Unit completion status', 'bxs-message-square-x');
                    }
                },
                error: function() {
                    showAlert('danger', 'Error updating Unit completion status', 'bxs-message-square-x');
                }
            });
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // FilePond initialization
        FilePond.registerPlugin(FilePondPluginFileValidateSize, FilePondPluginImagePreview, FilePondPluginFileValidateType);

        const pond = FilePond.create(document.querySelector('input[name="image"]'), {
            allowFileTypeValidation: true,
            acceptedFileTypes: ['image/*'],
            fileValidateTypeLabelExpectedTypes: 'Expected file type: Image'
        });

        // Submit form for adding course/unit
        $('#addCourseForm').on('submit', function(e) {
            e.preventDefault();
            var blockValue = $('#block_id').val(); 
            console.log('Selected Block:', blockValue); 

            var originalFormData = new FormData(this);
            var newFormData = new FormData();

            // Iterate over each entry in the original form data
            for (var pair of originalFormData.entries()) {
                if (pair[0] !== 'image') {
                    newFormData.append(pair[0], pair[1]);
                }
            }

            // Add the image file separately
            var file = pond.getFile();
            if (file) {
                newFormData.append('image', file.file);
            }

            $.ajax({
                url: '<?php echo e(route('admin.storeCourse')); ?>',
                method: 'POST',
                data: newFormData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#addCourseModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', 'Unit added successfully!', 'bxs-check-circle');

                    // Clear input fields
                    $('#title').val('');
                    $('#description').val('');
                    $('#category_id').val('');
                    $('#level').val('');
                    $('#type').val('');

                    // Clear FilePond
                    pond.removeFiles();
                },
                error: function(response) {
                    showAlert('danger', 'Error adding Unit', 'bxs-message-square-x');
                }
            });
        });

        // Assign Teacher
        $(document).on('click', '.assign-teacher-btn', function() {
            var courseId = $(this).data('course-id');
            $('#assignCourseId').val(courseId);

            $.ajax({
                url: '<?php echo e(route('admin.getTeachersForAssignment')); ?>',
                method: 'GET',
                success: function(response) {
                    var teacherSelect = $('#teacher_id');
                    teacherSelect.empty();
                    teacherSelect.append('<option value="" disabled selected>Select Teacher</option>');
                    $.each(response, function(index, teacher) {
                        teacherSelect.append('<option value="' + teacher.id + '">' +
                            teacher.user.first_name + ' ' + teacher.user.last_name + ' - ' + teacher.user.email +
                            '</option>');
                    });
                    $('#teacher_id').select2({
                        theme: 'bootstrap4',
                        dropdownParent: $('#assignTeacherModal'),
                        placeholder: 'Search for a teacher',
                        allowClear: true
                    });

                    // Pre-select the assigned teacher if available
                    var selectedTeacherId = $('#assignCourseId').data('teacher-id');
                    if (selectedTeacherId) {
                        $('#teacher_id').val(selectedTeacherId).trigger('change');
                    }

                    $('#assignTeacherModal').modal('show');
                },
                error: function(response) {
                    showAlert('danger', 'Error fetching teachers', 'bxs-message-square-x');
                }
            });
        });

        $('#assignTeacherForm').on('submit', function(e) {
            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: '<?php echo e(route('admin.assignTeacherToCourse')); ?>',
                method: 'POST',
                data: formData,
                success: function(response) {
                    $('#assignTeacherModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', 'Teacher assigned successfully!', 'bxs-check-circle');
                },
                error: function(response) {
                    showAlert('danger', 'Error assigning teacher', 'bxs-message-square-x');
                }
            });
        });

        // Clear modal on close
        $('#addCourseModal').on('hidden.bs.modal', function() {
            // Clear input fields
            $('#title').val('');
            $('#description').val('');
            $('#category_id').val('');
            $('#level').val('');
            $('#type').val('');

            // Clear FilePond
            pond.removeFiles();
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

        // Delete course/unit
        $(document).on('click', '.delete-btn', function() {
            let id = $(this).data('id');
            let url = $('#delete_source').val() + '/' + id;

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: url,
                        method: 'DELETE',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    title: "Deleted!",
                                    text: response.message,
                                    icon: "success"
                                }).then(() => {
                                    $('#courses-table').DataTable().ajax.reload();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: "Error",
                                text: xhr.responseJSON?.message || "An unknown error occurred",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });

    });
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/admin/courses.blade.php ENDPATH**/ ?>