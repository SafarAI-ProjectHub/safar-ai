@extends('layouts_dashboard.main')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/select2/css/select2-bootstrap4.css') }}" rel="stylesheet" />
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">Add New Course</button>
            </div>
            <div class="table-responsive">
                <table id="courses-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Category</th>
                            <th>Level</th>
                            <th>Type</th>
                            <th>Teacher</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Course Modal -->
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <form id="addCourseForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title</label>
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
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->age_group }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="level" class="form-label">Level</label>
                            <select class="form-select" id="level" name="level" required>
                                <option value="" disabled selected>Select Level</option>
                                @for ($i = 1; $i <= 6; $i++)
                                    <option value="{{ $i }}">Level {{ $i }}</option>
                                @endfor
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Teacher Modal -->
    <div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <form id="assignTeacherForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignTeacherModalLabel">Assign Teacher to Course</h5>
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
@endsection

@section('scripts')
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#courses-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.getCourses') }}',
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
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
                        data: 'actions',
                        name: 'actions',
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

            table.buttons().container()
                .appendTo('#courses-table_wrapper .col-md-6:eq(0)');

            $('#addCourseForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: '{{ route('admin.storeCourse') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#addCourseModal').modal('hide');
                        table.ajax.reload();
                        showAlert('success', 'Course added successfully!', 'bxs-check-circle');

                        // Clear input fields
                        $('#title').val('');
                        $('#description').val('');
                        $('#category_id').val('');
                        $('#level').val('');
                        $('#type').val('');
                    },
                    error: function(response) {
                        showAlert('danger', 'Error adding course', 'bxs-message-square-x');
                    }
                });
            });

            $(document).on('click', '.assign-teacher-btn', function() {
                var courseId = $(this).data('course-id');
                $('#assignCourseId').val(courseId);

                $.ajax({
                    url: '{{ route('admin.getTeachersForAssignment') }}',
                    method: 'GET',
                    success: function(response) {
                        var teacherSelect = $('#teacher_id');
                        teacherSelect.empty();
                        teacherSelect.append(
                            '<option value="" disabled selected>Select Teacher</option>');
                        $.each(response, function(index, teacher) {
                            teacherSelect.append('<option value="' + teacher.id + '">' +
                                teacher.user.first_name + ' ' + teacher.user
                                .last_name + ' - ' + teacher.user.email +
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
                    url: '{{ route('admin.assignTeacherToCourse') }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#assignTeacherModal').modal('hide');
                        table.ajax.reload();
                        showAlert('success', 'Teacher assigned successfully!',
                            'bxs-check-circle');
                    },
                    error: function(response) {
                        showAlert('danger', 'Error assigning teacher', 'bxs-message-square-x');
                    }
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
@endsection
