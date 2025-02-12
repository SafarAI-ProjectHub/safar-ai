@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Teachers' Activity for today</h5>
            <div class="table-responsive">
                <table id="teacher-activity-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Teacher</th>
                            <th>Email</th>
                            <th>Active Time (Today)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#teacher-activity-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('teachers.logs.index') }}',
                columns: [{
                        data: 'teacher_id',
                        name: 'teacher_id'
                    },
                    {
                        data: 'teacher',
                        name: 'teacher'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'active_time',
                        name: 'active_time'
                    },
                    {
                        data: 'id',
                        name: 'actions',
                        render: function(data) {
                            return `<a href="/admin/teacher/logs/${data}" class="btn btn-primary btn-sm">View Logs</a>`;
                        }
                    }
                ],
                columnDefs: [{
                    targets: 2,
                    width: '10%'
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
                lengthChange: false,
                order: [
                    [0, 'desc']
                ]
            });
        });
    </script>
@endsection
