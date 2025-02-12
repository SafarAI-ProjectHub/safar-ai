@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h4>My Certificates</h4>
        </div>
        <div class="card-body">
            <table id="certificates-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Completed At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#certificates-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('student.myCertificates') }}',
                columns: [{
                        data: 'course',
                        name: 'course'
                    },
                    {
                        data: 'completed_at',
                        name: 'completed_at',
                        render: function(data) {
                            return moment(data).format('MMMM D, YYYY');
                        }
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
    </script>
@endsection
