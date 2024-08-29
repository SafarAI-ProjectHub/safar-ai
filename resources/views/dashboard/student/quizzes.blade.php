@extends('layouts_dashboard.main')

@section('styles')
    <link href="{{ asset('assets/plugins/select2/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/dateRange.css') }}" rel="stylesheet" />
    <link href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Available Quizzes</h5>

            <div class="table-responsive">
                <table id="quizzes-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Quiz Title</th>
                            <th>Lesson</th>
                            <th>Unit</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('assets/plugins/select2/js/select2.min.js') }}"></script>
    <script src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#quizzes-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('student.quizzes.list') }}',
                },
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'course',
                        name: 'course'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                lengthChange: false,
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
            });
        });
    </script>
@endsection
