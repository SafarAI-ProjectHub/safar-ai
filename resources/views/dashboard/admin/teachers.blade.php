@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="teachers-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>CV Link</th>
                            <th>Years of Experience</th>
                            <th>Exam Score</th>
                            <th>Country Location</th>
                            <th>Approval Status</th>

                            {{-- <th>Actions</th> --}}
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
    <script>
        $(document).ready(function() {
            var table = $('#teachers-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.getteachers') }}',
                columns: [{
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
                        data: 'exam_score',
                        name: 'exam_score'
                    },
                    {
                        data: 'country_location',
                        name: 'country_location'
                    },
                    {
                        data: 'approval_status',
                        name: 'approval_status'
                    }
                    // {
                    //     data: null,
                    //     name: 'actions',
                    //     render: function(data, type, row) {
                    //         return '<button class="btn btn-primary btn-sm update-status" data-id="' +
                    //             row.id + '" data-status="' + row.approval_status +
                    //             '">Update Status</button>';
                    //     },
                    //     orderable: false,
                    //     searchable: false
                    // }
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
                .appendTo('#teachers-table_wrapper .col-md-6:eq(0)');

            $('#teachers-table').on('click', '.update-status', function() {
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
