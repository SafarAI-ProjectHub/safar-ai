@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header  text-white">
                <h2>Activity Results</h2>
            </div>
            <div class="card-body table-responsive">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="course-filter" class="form-control">
                            <option value="">Select Unit</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="unit-filter" class="form-control">
                            <option value="">Select Lesson</option>
                        </select>
                    </div>
                </div>
                <table id="quiz-results-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Unit Title</th>
                            <th>Lesson Title</th>
                            <th>AI Mark</th>
                            <th>Score</th>
                            <th>Action</th>
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
            // Initialize Select2
            $('#course-filter').select2({
                placeholder: 'Select Course',
                allowClear: true
            });
            $('#unit-filter').select2({
                placeholder: 'Select Unit',
                allowClear: true
            });

            // Initialize DataTable
            var table = $('#quiz-results-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('quizResults.index') }}',
                    data: function(d) {
                        d.course_id = $('#course-filter').val();
                        d.unit_id = $('#unit-filter').val();
                    }
                },
                columns: [{
                        data: 'username',
                        name: 'username'
                    },
                    {
                        data: 'course_title',
                        name: 'course_title'
                    },
                    {
                        data: 'unit_title',
                        name: 'unit_title'
                    },
                    {
                        data: 'ai_mark',
                        name: 'ai_mark'
                    },
                    {
                        data: 'score',
                        name: 'score'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
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

                lengthChange: false
            });

            // Handle course filter change
            $('#course-filter').on('change', function() {
                var courseId = $(this).val();
                $('#unit-filter').val('').trigger('change');
                $.ajax({
                    url: '{{ route('units.byCourse') }}',
                    method: 'GET',
                    data: {
                        course_id: courseId
                    },
                    success: function(response) {
                        var unitFilter = $('#unit-filter');
                        unitFilter.empty();
                        unitFilter.append('<option value="">Select Unit</option>');
                        $.each(response.units, function(index, unit) {
                            unitFilter.append('<option value="' + unit.id + '">' + unit
                                .title + '</option>');
                        });
                        unitFilter.trigger('change');
                    }
                });
                table.ajax.reload();
            });

            // Handle unit filter change
            $('#unit-filter').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
