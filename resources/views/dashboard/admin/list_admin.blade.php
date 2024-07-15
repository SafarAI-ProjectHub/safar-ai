@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/sweetalert/dist/sweetalert.css" />
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Admin List</h5>
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('admin.create') }}" class="btn btn-primary">Create Admin</a>
            </div>
            <table id="admin-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Date of Birth</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>




    <script>
        $(document).ready(function() {
            $('#admin-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('admin.list') }}',
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'first_name',
                        name: 'first_name'
                    },
                    {
                        data: 'last_name',
                        name: 'last_name'
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
                        data: 'date_of_birth',
                        name: 'date_of_birth'
                    },
                    {
                        data: 'country_location',
                        name: 'country_location'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'action',
                        name: 'action',
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
        });

        function deleteAdmin(id) {
            if (confirm("Are you sure you want to delete this admin?")) {
                $.ajax({
                    url: '/admin/delete/' + id,
                    type: 'DELETE',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(result) {
                        swal("Success", "Admin deleted successfully", "success");
                        $('#admin-table').DataTable().ajax.reload();
                    }
                });
            }
        }
    </script>
@endsection
