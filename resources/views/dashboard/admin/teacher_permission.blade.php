@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <h5>Manage Create Course Permissions</h5>
            <div id="status-message" class="alert d-none"></div>
            <div class="col-md-4 mb-3">
                <select id="user-filter" class="form-control">
                    <option value="">Select User</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }} - {{ $user->email }} </option>
                    @endforeach
                </select>
            </div>
            <table id="permissions-table" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Permissions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#user-filter').select2({
                placeholder: 'Select User',
                allowClear: true
            });

            var table = $('#permissions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('manage.permissions') }}',
                    data: function(d) {
                        d.user_id = $('#user-filter').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'permissions',
                        name: 'permissions',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $('#user-filter').on('change', function() {
                table.draw();
            });
        });

        function updatePermission(userId, permissionId, action) {
            $.ajax({
                url: '{{ route('manage.permissions.update') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    user_id: userId,
                    permission_id: permissionId,
                    action: action
                },
                success: function(response) {
                    showAlert('success', response.status, 'bxs-check-circle');
                    $('#permissions-table').DataTable().ajax.reload();
                },
                error: function(response) {
                    showAlert('danger', 'Error updating permission!', 'bxs-message-square-x');
                }
            });
        }

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
    </script>
@endsection
