@extends('layouts_dashboard.main')

@section('styles')
    <style>
        .question-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 15px;
        }

        .question-header {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .question-body {
            padding: 15px;
        }

        .question-text {
            font-weight: bold;
        }

        .sub-text {
            font-style: italic;
            margin-bottom: 10px;
        }

        .correct-answer {
            font-weight: bold;
        }

        .incorrect-answer {
            font-weight: bold;
        }

        .note-text {
            font-style: italic;
        }

        .assessment-info {
            margin-bottom: 20px;
        }

        .assessment-info table {
            width: 100%;
            border-collapse: collapse;
        }

        .assessment-info th,
        .assessment-info td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .assessment-info th {
            background-color: #f8f9fa;
        }

        .animated-arrow {
            display: inline-flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .animated-arrow:hover {
            transform: translateX(-5px);
        }

        /* Light Theme */
        .light-theme .question-card {
            background-color: #f8f9fa;
        }

        .light-theme .question-header {
            background-color: #e9ecef;
        }

        .light-theme .sub-text {
            color: #6c757d;
        }

        .light-theme .correct-answer {
            color: green;
        }

        .light-theme .incorrect-answer {
            color: red;
        }

        .light-theme .note-text {
            color: blue;
        }

        /* Dark Theme */
        .dark-theme .question-card {
            background-color: #2c2c2c;
            border-color: #444;
        }

        .dark-theme .question-header {
            background-color: #3c3c3c;
            border-bottom-color: #444;
        }

        .dark-theme .question-body {
            color: #ddd;
        }

        .dark-theme .question-text {
            color: #fff;
        }

        .dark-theme .sub-text {
            color: #999;
        }

        .dark-theme .correct-answer {
            color: #28a745;
        }

        .dark-theme .incorrect-answer {
            color: #dc3545;
        }

        .dark-theme .note-text {
            color: #17a2b8;
        }

        .dark-theme .assessment-info th {
            background-color: #444;
        }

        .dark-theme .assessment-info td,
        .dark-theme .assessment-info th {
            border-color: #555;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="mb-4">
            <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm animated fadeInLeft">
                <i class='bx bx-arrow-back'></i> Back
            </a>
        </div>
        <div class="card mb-4 table-responsive">
            <div class="card-header bg-primary text-white">
                <h2>Quiz Result: {{ $assessment->quiz->title }}</h2>
                <p>{{ $assessment->quiz->unit->title }} - {{ $assessment->quiz->unit->course->title }}</p>
                <p><strong>Student Name:</strong> {{ $assessment->user->full_name }}</p>
            </div>
        </div>

        <form id="review-form">
            @csrf

            <div class="assessment-info">
                <table>
                    <thead>
                        <tr>
                            <th>Assessment Type</th>
                            <th>Mark</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($assessment->ai_assessment)
                            <tr>
                                <td>AI Assessment</td>
                                <td>{{ $assessment->ai_mark }} / 100</td>
                                <td>{{ $assessment->ai_notes }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td>Teacher Review</td>
                            <td><input type="number" name="teacher_mark" value="{{ $assessment->teacher_mark ?? '' }}"
                                    style="    width: 60px !important;" placeholder="Enter mark" class="form-control"
                                    min="0" max="100" /> / 100</td>
                            <td>
                                <textarea name="teacher_notes" class="form-control" placeholder="Enter Overall Notes" rows="3">{{ $assessment->teacher_notes ?? '' }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @foreach ($assessment->quiz->questions as $question)
                <div class="question-card mb-4">
                    <div class="question-header">
                        <h5 class="card-title">Question {{ $loop->iteration }}: {{ $question->question_text }}</h5>
                        <p><strong>Type:</strong>
                            @if ($question->question_type === 'text')
                                Writing Question
                            @elseif($question->question_type === 'voice')
                                Speaking Question
                            @elseif($question->question_type === 'choice')
                                Multiple Choice Question
                            @endif
                        </p>
                    </div>
                    <div class="question-body">
                        @if ($question->sub_text)
                            <p class="sub-text"><strong>Note:</strong> {{ $question->sub_text }}</p>
                        @endif

                        @php
                            $response = $assessment->userResponses->where('question_id', $question->id)->first();
                        @endphp

                        @if ($response)
                            @if ($question->question_type === 'voice')
                                <p class="instruction-text">Your Answer:</p>
                                <audio controls>
                                    <source src="{{ asset($response->response) }}" type="audio/wav">
                                    Your browser does not support the audio element.
                                </audio>
                            @else
                                <p class="instruction-text">Your Answer: {{ $response->response }}</p>
                            @endif

                            <div class="form-group">
                                <label for="correct_{{ $response->id }}">Correct:</label>
                                <select name="responses[{{ $response->id }}][correct]" id="correct_{{ $response->id }}"
                                    class="form-control">
                                    <option value="1" @if ($response->correct) selected @endif>Yes</option>
                                    <option value="0" @if (!$response->correct) selected @endif>No</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="teacher_review_{{ $response->id }}">Teacher Review:</label>
                                <textarea name="responses[{{ $response->id }}][teacher_review]" id="teacher_review_{{ $response->id }}"
                                    class="form-control">{{ $response->teacher_review ?? '' }}</textarea>
                            </div>

                            @if ($question->question_type === 'choice')
                                <p class="instruction-text"><strong>Choices:</strong></p>
                                @php
                                    $choices = ['a', 'b', 'c', 'd'];
                                @endphp
                                @foreach ($question->choices as $index => $choice)
                                    <p
                                        class="@if ($choice->choice_text === $response->response) @if ($response->correct) correct-answer @else incorrect-answer @endif
@elseif ($choice->is_correct)
correct-answer @endif">
                                        <strong>{{ $choices[$index] }}.</strong> {{ $choice->choice_text }}
                                        @if ($choice->choice_text === $response->response)
                                            (Your answer)
                                        @elseif ($choice->is_correct)
                                            (Correct answer)
                                        @endif
                                    </p>
                                @endforeach
                            @endif

                            <p class="note-text">AI Notes: {{ $response->ai_review ?? 'Pending Review' }}</p>
                        @else
                            <p class="incorrect-answer">No response</p>
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="text-center">
                <button type="button" id="submit-button" class="btn btn-success">Save Review</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#submit-button').on('click', function(event) {
                event.preventDefault();

                // get the CSRF token from the meta tag
                const token = $('meta[name="csrf-token"]').attr('content');

                // get the form data
                const formData = $('#review-form').serialize();
                $.ajax({
                    url: '{{ route('quizResults.update', $assessment->id) }}',
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function(response) {
                        if (response.success) {
                            swal("Success", "Review saved successfully", "success");
                        } else {
                            // Extract and display validation error messages
                            let errorMessage = '';
                            $.each(response.message, function(key, messages) {
                                $.each(messages, function(index, message) {
                                    errorMessage += message + '\n';
                                });
                            });
                            swal("Error", errorMessage, "error");
                        }
                    },
                    error: function(response) {
                        // show error message from the response
                        if (response.responseJSON && response.responseJSON.message) {
                            let errorMessage = '';
                            $.each(response.responseJSON.message, function(key, messages) {
                                $.each(messages, function(index, message) {
                                    errorMessage += message + '\n';
                                });
                            });
                            swal("Error", errorMessage, "error");
                        } else {
                            swal("Error", "An error occurred. Please try again.", "error");
                        }
                    }
                });
            });

            const audioPlayers = document.querySelectorAll('audio');
            audioPlayers.forEach(player => {
                player.addEventListener('play', () => {
                    audioPlayers.forEach(p => {
                        if (p !== player) {
                            p.pause();
                        }
                    });
                });
            });
        });
    </script>
@endsection
