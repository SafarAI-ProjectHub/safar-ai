@extends('layouts_dashboard.main')

@section('styles')
    <style>
        .question-card {
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 15px;
        }

        .question-header {
            background-color: #e9ecef;
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
            color: #6c757d;
            margin-bottom: 10px;
        }

        .correct-answer {
            color: green;
            font-weight: bold;
        }

        .incorrect-answer {
            color: red;
            font-weight: bold;
        }

        .note-text {
            color: blue;
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
    </style>
@endsection

@section('content')
    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h2>Activity Result: {{ $assessment->quiz->title }}</h2>
                <p>{{ $assessment->quiz->unit->title }} - {{ $assessment->quiz->unit->course->title }}</p>
            </div>
        </div>

        @if ($assessment->ai_assessment || $assessment->teacher_review)
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
                        @if ($assessment->teacher_review)
                            <tr>
                                <td>Teacher Review</td>
                                <td>{{ $assessment->teacher_mark }} / 100</td>
                                <td>{{ $assessment->teacher_notes }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif

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

                        @if ($assessment->ai_assessment || $assessment->teacher_review)
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

                            <p class="note-text">Notes: {{ $response->ai_review ?? 'Pending Review' }}</p>
                            @if ($response->teacher_review)
                                <p class="note-text">Teacher Review: {{ $response->teacher_review }}</p>
                            @endif
                        @else
                            <p class="note-text">Awaiting review.</p>
                        @endif
                    @else
                        <p class="incorrect-answer">No response</p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
