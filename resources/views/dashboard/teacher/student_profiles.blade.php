@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <h5>Manage Students</h5>
            <div class="row mb-3">
                <div class="col-md-4">
                    <select id="course-filter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table id="students-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Full Name</th>
                            <th>Country</th>
                            <th>Age</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#students-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('teacher.getStudentProfiles') }}',
                    data: function(d) {
                        d.course_id = $('#course-filter').val();
                    }
                },
                columns: [{
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'country_location',
                        name: 'country_location'
                    },
                    {
                        data: 'age',
                        name: 'age'
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
                    width: '10%'
                }],
                lengthChange: false
            });
            $('#course-filter').select2({
                placeholder: 'Select Course',
                allowClear: true
            });
            $('#course-filter').change(function() {
                table.draw();
            });
        });
    </script>
@endsection
