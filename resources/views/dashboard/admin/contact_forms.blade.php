@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Manage Contact Forms</h5>
            <div class="form-group mb-3">
                <dev class="d-flex justify-content-between align-items-center">
                    <label for="filter-resolved">Filter by status:</label>
                    <select id="filter-resolved" class="form-control">
                        <option value="all">All</option>
                        <option value="0">Not Resolved</option>
                        <option value="1">Resolved</option>
                    </select>
                </dev>
            </div>
            <div class="table-responsive">
                <table id="contact-forms-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be populated via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            var table = $('#contact-forms-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('admin.contact_forms.data') }}',
                    type: 'GET',
                    data: function(d) {
                        d.resolved = $('#filter-resolved').val();
                    }
                },
                columns: [{
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'subject'
                    },
                    {
                        data: 'message'
                    },
                    {
                        data: 'resolved',
                        render: function(data) {
                            return data ?
                                '<dev class="badge bg-success text-center p-2">Resolved</dev>' :
                                '<dev class="badge bg-danger text-center p-2">Not Resolved</dev>';
                        }
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#filter-resolved').change(function() {
                table.ajax.reload();
            });

            // Handle contact form mark as resolved
            $(document).on('click', '.handle-contact', function() {
                var contactId = $(this).data('id');
                $.ajax({
                    url: '/admin/contact-forms/' + contactId + '/resolved',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.fire('Success!', response.message, 'success');
                        table.ajax.reload();
                    },
                    error: function(response) {
                        Swal.fire('Error!',
                            'There was an error marking the contact form as resolved.',
                            'error');
                    }
                });
            });

            // Handle contact form deletion
            $(document).on('click', '.delete-contact', function() {
                var contactId = $(this).data('id');
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
                            url: '/admin/contact-forms/' + contactId,
                            type: 'DELETE',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Swal.fire('Deleted!', response.message, 'success');
                                table.ajax.reload();
                            },
                            error: function(response) {
                                Swal.fire('Error!',
                                    'There was an error deleting the contact form.',
                                    'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
