@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Manage Student Level Tests</h5>
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('studentTest.addPage') }}" class="btn btn-sm btn-primary">Add New Test</a>
            </div>
            <div class="table-responsive">
                <table id="student-tests-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Age Group</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#student-tests-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('studentTests.datatable') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'age_group',
                        name: 'age_group'
                    },
                    {
                        data: 'active',
                        name: 'active',
                        render: function(data, type, row) {
                            return `<div class="form-check form-switch">
                                    <input class="form-check-input activate-test" type="checkbox" data-id="${row.id}" ${data ? 'checked' : ''}>
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
                    width: '15%'
                }],
                lengthChange: false
            });

            // Handle Edit button click
            $(document).on('click', '.edit-test', function() {
                var testId = $(this).data('id');
                window.location.href = '/admin/student/test/' + testId + '/edit';
            });

            // Handle Delete button click
            $(document).on('click', '.delete-test', function() {
                var testId = $(this).data('id');
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
                            url: '/admin/student/test/' + testId + '/delete',
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your test has been deleted.',
                                    'success'
                                );
                                table.ajax.reload();
                            },
                            error: function(response) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the test.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            // Handle Activate/Deactivate switch
            $(document).on('change', '.activate-test', function() {
                var testId = $(this).data('id');
                var isActive = $(this).is(':checked');
                $.ajax({
                    url: '/admin/student/test/' + testId + '/activate',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        active: isActive
                    },
                    success: function(response) {
                        table.ajax.reload(null,
                            false);
                        if (response.status) {
                            Swal.fire(
                                'Success!',
                                'The test has been ' + (isActive ? 'activated' :
                                    'deactivated') + '.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Error!',
                                response.message,
                                'error'
                            );
                        }

                    },
                    error: function(response) {
                        table.ajax.reload(null,
                            false);
                        console.log(response.responseJSON);
                        Swal.fire(
                            'Error!',
                            response.responseJSON.message,
                            'error'
                        );
                    }
                });
            });
        });
    </script>
@endsection
