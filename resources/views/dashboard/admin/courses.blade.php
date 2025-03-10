@extends('layouts_dashboard.main')

@section('styles')
    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />

    <style>
        .modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <h5>Courses List</h5>
        @can('create courses')
            <div class="d-flex justify-content-end mb-3 gap-2">
                <!-- زر الإضافة -->
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    Add New Course
                </button>
                <!-- زر الـ Fetch from Moodle -->
                <button class="btn btn-sm btn-info" id="fetchFromMoodleBtn">
                    Fetch from Moodle
                </button>
            </div>
        @endcan

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
                        <th>Completed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
@can('create courses')
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="addCourseForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Title -->
                    <div class="mb-3">
                        <label class="form-label">Course Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" required></textarea>
                    </div>

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id" required>
                            <option value="" disabled selected>Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Level -->
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select class="form-select" name="level" required>
                            <option value="" disabled selected>Select Level</option>
                            @for ($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">Level {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Type -->
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="weekly">Weekly</option>
                            <option value="intensive">Intensive</option>
                        </select>
                    </div>

                    <!-- Image -->
                    <div class="mb-3">
                        <label class="form-label">Upload Image</label>
                        <input type="file" class="filepond" name="image"
                               data-allow-reorder="true"
                               data-max-file-size="5MB"
                               data-max-files="1">
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
@endcan

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="editCourseForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editCourseId" name="course_id">

                    <div class="mb-3">
                        <label class="form-label">Course Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="editCategoryId" name="category_id" required>
                            <option value="" disabled selected>Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select class="form-select" id="editLevel" name="level" required>
                            <option value="" disabled selected>Select Level</option>
                            @for ($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}">Level {{ $i }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <select class="form-select" id="editType" name="type" required>
                            <option value="" disabled selected>Select Type</option>
                            <option value="weekly">Weekly</option>
                            <option value="intensive">Intensive</option>
                        </select>
                    </div>

                    <!-- تغيير الصورة اختياري -->
                    <div class="mb-3">
                        <label class="form-label">Change Image</label>
                        <input type="file" class="filepond" id="editImage" name="image"
                               data-allow-reorder="true"
                               data-max-file-size="5MB"
                               data-max-files="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Assign Teacher Modal -->
@hasanyrole('Super Admin|Admin')
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <form id="assignTeacherForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Assign Teacher to Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="assignCourseId" name="course_id">
                    <div class="mb-3">
                        <label class="form-label">Select Teacher</label>
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
@endhasanyrole

<!-- حقل مخفي لحذف الكورسات -->
<input type="hidden" id="delete_source" value="{{ route('admin.courses.delete', ['id' => 0]) }}">
@endsection

@section('scripts')
<!-- FilePond JS -->
<script src="https://unpkg.com/filepond/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

<!-- Select2 JS -->
<script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(function() {
    // سنقرأ category_id من بارامترات الرابط إن وُجد
    // مثال: /admin/courses?category_id=3
    let categoryId = "{{ request('category_id') }}";

    // إعداد DataTable
    let table = $('#courses-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("admin.courses.getCourses") }}',
            // نرسل category_id في البيانات
            data: {
                category_id: categoryId
            }
        },
        columns: [
            { data: 'id',          name: 'id' },
            { data: 'title',       name: 'title' },
            {
                data: 'description',
                name: 'description',
                render: function(data) {
                    if (!data) return '';
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
            },
            { data: 'category',    name: 'category' },
            { data: 'level',       name: 'level' },
            { data: 'type',        name: 'type' },
            {
                data: 'teacher',
                name: 'teacher',
                defaultContent: 'N/A'
            },
            {
                // لاحظ أننا نستقبل قيمة completed_switch من الكونترولر
                // لكن هنا في كودك الأصلي: استقبلته باسم 'completed'
                // لذلك سنستخدمه كما هو
                data: 'completed',
                name: 'completed',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    let checked = (parseInt(data) === 1) ? 'checked' : '';
                    return `
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-complete" type="checkbox"
                                   data-id="${row.id}" ${checked}>
                        </div>
                    `;
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false
            }
        ],
        columnDefs: [
            { visible: false, targets: 0 }
        ],
        dom: 'Bfrtip',
        buttons: [
            { extend: 'copy',  className: 'btn btn-outline-secondary' },
            { extend: 'excel', className: 'btn btn-outline-secondary' },
            { extend: 'pdf',   className: 'btn btn-outline-secondary' },
            { extend: 'print', className: 'btn btn-outline-secondary' }
        ],
        lengthChange: false
    });

    // زر Fetch from Moodle
    $('#fetchFromMoodleBtn').on('click', function() {
        $.ajax({
            url: '{{ route("admin.courses.syncFromMoodle") }}',
            method: 'GET',
            success: function(resp) {
                table.ajax.reload();
                showAlert('success', resp.message, 'bxs-check-circle');
            },
            error: function() {
                showAlert('danger', 'Error fetching data from Moodle', 'bxs-message-square-x');
            }
        });
    });

    // Toggle Complete
    $('#courses-table').on('change', '.toggle-complete', function() {
        let courseId  = $(this).data('id');
        let completed = $(this).is(':checked') ? 1 : 0;

        $.ajax({
            url: '/admin/courses/toggle-complete/' + courseId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                completed: completed
            },
            success: function(resp) {
                if (resp.success) {
                    table.ajax.reload(null, false);
                    showAlert('success', 'Course completion status updated!', 'bxs-check-circle');
                } else {
                    showAlert('danger', 'Error updating completion status', 'bxs-message-square-x');
                }
            },
            error: function() {
                showAlert('danger', 'Error updating completion status', 'bxs-message-square-x');
            }
        });
    });

    // FilePond
    FilePond.registerPlugin(FilePondPluginFileValidateSize, FilePondPluginImagePreview, FilePondPluginFileValidateType);

    let pondCreate = FilePond.create(document.querySelector('#addCourseForm input[name="image"]'), {
        allowFileTypeValidation: true,
        acceptedFileTypes: ['image/*']
    });

    let pondEdit  = FilePond.create(document.querySelector('#editCourseForm input[name="image"]'), {
        allowFileTypeValidation: true,
        acceptedFileTypes: ['image/*']
    });

    // إضافة كورس جديد
    $('#addCourseForm').on('submit', function(e) {
        e.preventDefault();
        let originalForm = new FormData(this);
        let formData     = new FormData();

        // لا نضيف الصورة مباشرة من الـ Form بل من FilePond
        for (let pair of originalForm.entries()) {
            if (pair[0] !== 'image') {
                formData.append(pair[0], pair[1]);
            }
        }

        // إضافة الصورة من FilePond
        let file = pondCreate.getFile();
        if (file) {
            formData.append('image', file.file);
        }

        $.ajax({
            url: '{{ route("admin.courses.storeCourse") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                $('#addCourseModal').modal('hide');
                table.ajax.reload();
                showAlert('success', resp.success || 'Course added!', 'bxs-check-circle');

                $('#addCourseForm')[0].reset();
                pondCreate.removeFiles();
            },
            error: function() {
                showAlert('danger', 'Error adding course', 'bxs-message-square-x');
            }
        });
    });

    // عند إغلاق مودال الإضافة
    $('#addCourseModal').on('hidden.bs.modal', function() {
        $('#addCourseForm')[0].reset();
        pondCreate.removeFiles();
    });

    // فتح مودال التعديل
    $(document).on('click', '.edit-btn', function() {
        let courseId = $(this).data('id');
        $.ajax({
            url: '/admin/courses/edit/' + courseId,
            method: 'GET',
            success: function(course) {
                $('#editCourseId').val(course.id);
                $('#editTitle').val(course.title);
                $('#editDescription').val(course.description);
                $('#editCategoryId').val(course.category_id);
                $('#editLevel').val(course.level);
                $('#editType').val(course.type);

                // إزالة أي صورة سابقة من FilePond
                pondEdit.removeFiles();

                // عرض المودال
                $('#editCourseModal').modal('show');
            },
            error: function() {
                showAlert('danger', 'Error fetching course info', 'bxs-message-square-x');
            }
        });
    });

    // حفظ تعديل الكورس
    $('#editCourseForm').on('submit', function(e) {
        e.preventDefault();

        let courseId    = $('#editCourseId').val();
        let originalForm= new FormData(this);
        let formData    = new FormData();

        for (let pair of originalForm.entries()) {
            if (pair[0] !== 'image' && pair[0] !== 'course_id') {
                formData.append(pair[0], pair[1]);
            }
        }
        formData.append('_token', '{{ csrf_token() }}');

        let file = pondEdit.getFile();
        if (file) {
            formData.append('image', file.file);
        }

        $.ajax({
            url: '/admin/courses/update/' + courseId,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp) {
                if (resp.success) {
                    $('#editCourseModal').modal('hide');
                    table.ajax.reload();
                    showAlert('success', resp.message || 'Course updated!', 'bxs-check-circle');
                } else {
                    showAlert('danger', 'Error updating course', 'bxs-message-square-x');
                }
            },
            error: function() {
                showAlert('danger', 'Error updating course', 'bxs-message-square-x');
            }
        });
    });

    $('#editCourseModal').on('hidden.bs.modal', function() {
        $('#editCourseForm')[0].reset();
        pondEdit.removeFiles();
    });

    // إسناد معلّم
    $(document).on('click', '.assign-teacher-btn', function() {
        let courseId = $(this).data('course-id');
        $('#assignCourseId').val(courseId);

        $.ajax({
            url: '{{ route("admin.courses.getTeachersForAssignment") }}',
            method: 'GET',
            success: function(resp) {
                let select = $('#teacher_id');
                select.empty();
                select.append('<option value="" disabled selected>Select Teacher</option>');
                $.each(resp, function(i, teacher) {
                    select.append(`
                        <option value="${teacher.id}">
                            ${teacher.user.first_name} ${teacher.user.last_name} - ${teacher.user.email}
                        </option>
                    `);
                });
                select.select2({
                    theme: 'bootstrap4',
                    dropdownParent: $('#assignTeacherModal')
                });
                $('#assignTeacherModal').modal('show');
            },
            error: function() {
                showAlert('danger', 'Error fetching teachers', 'bxs-message-square-x');
            }
        });
    });

    $('#assignTeacherForm').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.ajax({
            url: '{{ route("admin.courses.assignTeacherToCourse") }}',
            method: 'POST',
            data: formData,
            success: function(resp) {
                $('#assignTeacherModal').modal('hide');
                table.ajax.reload();
                showAlert('success', resp.success || 'Teacher assigned!', 'bxs-check-circle');
            },
            error: function() {
                showAlert('danger', 'Error assigning teacher', 'bxs-message-square-x');
            }
        });
    });

    // حذف الكورس
    $(document).on('click', '.delete-btn', function() {
        let id       = $(this).data('id');
        let baseUrl  = $('#delete_source').val(); // .../delete/0
        let deleteUrl= baseUrl.replace('/0', '/' + id);

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
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                $.ajax({
                    url: deleteUrl,
                    method: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: "Deleted!",
                                text: response.message,
                                icon: "success"
                            }).then(() => {
                                table.ajax.reload();
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
                            text: xhr.responseJSON?.message || "Unknown error",
                            icon: "error"
                        });
                    }
                });
            }
        });
    });

    // دالة تنبيه
    function showAlert(type, message, icon) {
        let alertHtml = `
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
        setTimeout(() => {
            $('.alert').alert('close');
        }, 4000);
    }
});
</script>
@endsection
