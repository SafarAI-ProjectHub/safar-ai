@extends('layouts_dashboard.main')

@section('content')
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h2>Quiz Results for: {{ $quiz->title }}</h2>
                <p>Course: {{ $quiz->unit->course->title }}</p>
            </div>
            <div class="card-body">
                <table class="table table-striped table-bordered" id="results-table">
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

    <!-- Modal for Viewing Responses -->
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">Student Quiz Response</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Response content will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary save-review">Save Review</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var table = $('#results-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('quiz.resultsDataTable', $quiz->id) }}',
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

            // View Response button click handler
            $(document).on('click', '.view-response', function() {
                var assessmentId = $(this).data('id');
                $.get('/assessments/' + assessmentId + '/response', function(data) {
                    var modalBody = $('#responseModal .modal-body');
                    modalBody.empty();

                    data.quiz.questions.forEach(function(question, index) {
                        var questionHtml = '<div class="question-card mb-4">';
                        questionHtml +=
                            '<div class="question-header"><h5 class="card-title">Question ' +
                            (index + 1) + ': ' + question.question_text + '</h5></div>';
                        questionHtml += '<div class="question-body">';

                        if (question.sub_text) {
                            questionHtml += '<p class="sub-text"><strong>Note:</strong> ' +
                                question.sub_text + '</p>';
                        }

                        if (question.question_type === 'text') {
                            questionHtml +=
                                '<div class="form-group"><label class="instruction-text">Answer:</label>';
                            questionHtml +=
                                '<textarea class="form-control" name="question_' + question
                                .id + '" disabled>' + (data.response[question.id] ??
                                    'No Answer') + '</textarea></div>';
                        } else if (question.question_type === 'voice') {
                            questionHtml +=
                                '<div class="form-group"><label class="instruction-text">Answer:</label>';
                            questionHtml +=
                                '<audio controls style="width: 100%;"><source src="/storage/' +
                                data.response[question.id] +
                                '" type="audio/wav">Your browser does not support the audio element.</audio></div>';
                        } else if (question.question_type === 'choice') {
                            questionHtml +=
                                '<label class="instruction-text">Selected Option:</label>';
                            question.choices.forEach(function(choice) {
                                var isChecked = (data.response[question.id] ==
                                    choice.id) ? 'checked' : '';
                                questionHtml +=
                                    '<div class="form-check form-check-primary">';
                                questionHtml +=
                                    '<input class="form-check-input" type="radio" name="question_' +
                                    question.id + '" value="' + choice.id + '" ' +
                                    isChecked + ' disabled>';
                                questionHtml += '<label class="form-check-label">' +
                                    choice.choice_text + '</label></div>';
                            });
                        }

                        questionHtml += '</div></div>';
                        modalBody.append(questionHtml);
                    });

                    modalBody.append(
                        '<div class="form-group"><label for="teacher_notes">Teacher\'s Notes</label><textarea class="form-control" id="teacher_notes" name="teacher_notes">' +
                        data.teacher_notes + '</textarea></div>');
                    $('#responseModal').modal('show');
                });
            });

            // Save Review button click handler
            $('.save-review').on('click', function() {
                var assessmentId = $('#responseForm').data('id');
                var formData = $('#responseForm').serialize();

                $.ajax({
                    url: '/assessments/' + assessmentId + '/review',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#responseModal').modal('hide');
                        alert('Review saved successfully');
                    },
                    error: function(response) {
                        alert('Error saving review');
                    }
                });
            });
        });
    </script>
@endsection
