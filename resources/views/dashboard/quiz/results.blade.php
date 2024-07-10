@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
@endsection

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>Results for Quiz: {{ $quiz->title }}</h2>
                <p>Course: {{ $quiz->unit->course->title }}</p>
                <p>Unit: {{ $quiz->unit->title }}</p>
            </div>
            <div class="card-body">
                <table id="results-table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>AI Mark</th>
                            <th>Teacher Mark</th>
                            <th>Score</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#results-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('quiz.resultsDataTable', $quiz->id) }}'
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'ai_mark',
                        name: 'ai_mark'
                    },
                    {
                        data: 'teacher_mark',
                        name: 'teacher_mark'
                    },
                    {
                        data: 'score',
                        name: 'score'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endsection
