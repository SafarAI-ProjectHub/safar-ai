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

        .instruction-text {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .form-check-input[type=checkbox] {
            width: 40px;
            height: 20px;
            margin-left: 10px;
        }

        .form-check-primary .form-check-input {
            width: 20px !important;
            height: 20px;
        }

        .form-check-label {
            margin-left: 5px;
        }

        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loader .spinner-border {
            width: 3rem;
            height: 3rem;
        }

        .recording {
            background-color: red !important;
        }
    </style>
@endsection

@section('content')
    <div id="loader" style="display: none;">
        <div class="spinner-border text-primary" role="status">

        </div>
    </div>

    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                @if ($levelTestQuestions->isNotEmpty())
                    <h2>{{ $levelTestQuestions->first()->levelTest->title }}</h2>
                    <p>{{ $levelTestQuestions->first()->levelTest->description }}</p>
                @else
                    <h2>No Level Test Available</h2>
                @endif
            </div>
        </div>
        <form id="level-test-form" method="POST" action="{{ route('level-test.submit') }}" enctype="multipart/form-data">
            @csrf

            @foreach ($levelTestQuestions as $question)
                <div class="question-card mb-4">
                    <div class="question-header">
                        <h5 class="card-title">Question {{ $loop->iteration }} : {{ $question->question_text }}</h5>
                    </div>
                    <div class="question-body">
                        @if ($question->sub_text)
                            <p class="sub-text"><strong>Note:</strong>{{ $question->sub_text }}</p>
                        @endif

                        @if ($question->question_type === 'text')
                            <div class="form-group">
                                <label class="instruction-text">Please write your answer below:</label>
                                <textarea class="form-control" name="question_{{ $question->id }}" required></textarea>
                            </div>
                        @elseif($question->question_type === 'voice')
                            <div class="form-group">
                                <label class="instruction-text">Please record your answer:</label>
                                <button type="button" class="btn btn-primary record-btn mb-3"
                                    data-target="audio-playback_{{ $loop->iteration }}">
                                    Record <i class="fas fa-microphone"></i>
                                </button>
                            </div>
                            <input type="file" id="audio-upload_{{ $loop->iteration }}"
                                name="question_{{ $question->id }}_audio" style="display:none;" accept="audio/*" required>
                            <audio id="audio-playback_{{ $loop->iteration }}" controls
                                style="display:none; width: 100%;"></audio>
                        @elseif($question->question_type === 'choice')
                            <label class="instruction-text">Please select one of the following options:</label>
                            @foreach ($question->choices as $choice)
                                <div class="form-check form-check-primary">
                                    <input class="form-check-input" type="radio" name="question_{{ $question->id }}"
                                        id="option_{{ $loop->index }}" value="{{ $choice->id }}" required>
                                    <label class="form-check-label"
                                        for="option_{{ $loop->index }}">{{ $choice->choice_text }}</label>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endforeach

            <div class="text-center">
                <button type="submit" class="btn btn-success">Submit Quiz</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recordBtns = document.querySelectorAll('.record-btn');
            const audioUploads = document.querySelectorAll('input[type="file"]');
            let mediaRecorders = [];
            let audioChunks = [];
            let isRecordingCompleted = Array(recordBtns.length).fill(false);
            let activeRecorderIndex = null;

            recordBtns.forEach((recordBtn, index) => {
                recordBtn.addEventListener('click', () => {
                    const audioPlayback = document.getElementById(recordBtn.dataset.target);
                    if (activeRecorderIndex !== null && activeRecorderIndex !== index) {
                        mediaRecorders[activeRecorderIndex].stop();
                        recordBtns[activeRecorderIndex].textContent = 'Record';
                        recordBtns[activeRecorderIndex].classList.remove('recording');
                    }

                    if (!mediaRecorders[index] || mediaRecorders[index].state === 'inactive') {
                        navigator.mediaDevices.getUserMedia({
                                audio: true
                            })
                            .then(stream => {
                                mediaRecorders[index] = new MediaRecorder(stream);
                                mediaRecorders[index].start();
                                activeRecorderIndex = index;
                                isRecordingCompleted[index] = false;
                                recordBtn.classList.add('recording');
                                mediaRecorders[index].addEventListener('dataavailable',
                                    event => {
                                        audioChunks[index] = event.data;
                                    });
                                mediaRecorders[index].addEventListener('stop', () => {
                                    const audioBlob = new Blob([audioChunks[index]], {
                                        type: 'audio/wav'
                                    });
                                    audioChunks[index] = [];
                                    const audioUrl = URL.createObjectURL(audioBlob);
                                    audioPlayback.src = audioUrl;
                                    audioPlayback.style.display = 'block';
                                    const file = new File([audioBlob],
                                        `recording_${index + 1}.wav`, {
                                            type: 'audio/wav'
                                        });
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    audioUploads[index].files = dataTransfer.files;
                                    isRecordingCompleted[index] = true;
                                    recordBtn.classList.remove('recording');
                                    activeRecorderIndex = null;
                                });
                                recordBtn.textContent = 'Stop Recording';
                            });
                    } else if (mediaRecorders[index].state === 'recording') {
                        mediaRecorders[index].stop();
                        recordBtn.textContent = 'Record';
                    }
                });
            });

            // Handle form submission
            const form = document.getElementById('level-test-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                // Check if all required audio recordings are completed
                let allAudioRecorded = true;
                audioUploads.forEach((input, index) => {
                    if (input.hasAttribute('required') && !isRecordingCompleted[index]) {
                        allAudioRecorded = false;
                    }
                });

                if (!allAudioRecorded) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Recordings',
                        text: 'Please complete all required audio recordings before submitting the quiz.',
                    });
                    return;
                }

                // Temporarily remove 'required' attribute from hidden inputs
                audioUploads.forEach(input => input.removeAttribute('required'));

                document.getElementById('loader').style.display = 'flex'; // Show loader
                const formData = new FormData(form);
                fetch('{{ route('level-test.submit') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loader').style.display = 'none'; // Hide loader
                        if (data.success) {
                            window.location.href = '{{ route('student.dashboard') }}';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Error',
                                text: 'An error occurred while submitting your quiz. Please try again.',
                            });
                        }
                    })
                    .catch(error => {
                        document.getElementById('loader').style.display = 'none'; // Hide loader
                        Swal.fire({
                            icon: 'error',
                            title: 'Submission Error',
                            text: 'An error occurred while submitting your quiz. Please try again.',
                        });
                        console.error('Error:', error);
                    });
            });
        });
    </script>
@endsection
