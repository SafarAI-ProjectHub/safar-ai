@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="teachers-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>CV Link</th>
                            <th>Years of Experience</th>
                            <th>Country Location</th>
                            <th>Approval Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Teacher Modal -->
    <div class="modal fade" id="editTeacherModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTeacherForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="teacher_id" name="teacher_id">
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone_number" name="phone_number" required>
                        </div>
                        <div class="mb-3">
                            <label for="country_location" class="form-label">Country Location</label>
                            <input type="text" class="form-control" id="country_location" name="country_location"
                                required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
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

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="statusForm">
                    @csrf
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
@endsection

@section('scripts')
    <!-- Include SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">

    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        $(document).ready(function() {
            var table = $('#teachers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.getteachers') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'cv_link',
                        name: 'cv_link'
                    },
                    {
                        data: 'years_of_experience',
                        name: 'years_of_experience'
                    },
                    {
                        data: 'country_location',
                        name: 'country_location'
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: 7,
                    width: '25%'
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
                lengthChange: false
            });

            table.buttons().container().appendTo('#teachers-table_wrapper .col-md-6:eq(0)');

            $('#teachers-table').on('click', '.update-status', function() {
                var teacherId = $(this).data('id');
                var currentStatus = $(this).data('status');
                $('#teacher_id').val(teacherId);
                $('#approval_status').val(currentStatus);
                $('#statusModal').modal('show');
            });

            $('#teachers-table').on('click', '.edit-teacher', function() {
                var teacherId = $(this).data('id');
                $.get('/admin/teachers/' + teacherId + '/edit', function(data) {
                    $('#editTeacherModal #teacher_id').val(data.id);
                    $('#editTeacherModal #first_name').val(data.user.first_name);
                    $('#editTeacherModal #last_name').val(data.user.last_name);
                    $('#editTeacherModal #email').val(data.user.email);
                    $('#editTeacherModal #phone_number').val(data.user.phone_number);
                    $('#editTeacherModal #country_location').val(data.user.country_location);
                    $('#editTeacherModal').modal('show');
                });
            });

            $('#editTeacherForm').on('submit', function(e) {
                e.preventDefault();
                var teacherId = $('#editTeacherModal #teacher_id').val();
                var formData = $(this).serialize();
                $.ajax({
                    url: '/admin/teachers/' + teacherId,
                    method: 'PUT',
                    data: formData,
                    success: function(response) {
                        $('#editTeacherModal').modal('hide');
                        table.ajax.reload();
                        showAlert('success', 'Teacher updated successfully!',
                            'bxs-check-circle');
                    },
                    error: function(response) {
                        showAlert('danger', 'Error updating teacher', 'bxs-message-square-x');
                    }
                });
            });

            $('#teachers-table').on('click', '.delete-teacher', function() {
                var teacherId = $(this).data('id');
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
                            url: '/admin/teachers/' + teacherId,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                table.ajax.reload();
                                Swal.fire('Deleted!', 'Teacher has been deleted.',
                                    'success');
                            },
                            error: function(response) {
                                Swal.fire('Error!', 'Error deleting teacher', 'error');
                            }
                        });
                    }
                })
            });

            $('#teachers-table').on('click', '.view-cv', function(event) {
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

            $('#statusForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                $.ajax({
                    url: '{{ route('admin.updateTeacherStatus') }}',
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

            $('#teachers-table').on('click', '.create-contract', function() {
                var teacherId = $(this).data('id');
                window.location.href = '/admin/contracts/create/' + teacherId;
            });

            $('#teachers-table').on('click', '.edit-contract', function() {
                var contractId = $(this).data('id');
                window.location.href = '/admin/contracts/' + contractId + '/edit';
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
                </div>`;
                $('body').append(alertHtml);
                setTimeout(function() {
                    $('.alert').alert('close');
                }, 5000);
            }
        });
    </script>
@endsection
