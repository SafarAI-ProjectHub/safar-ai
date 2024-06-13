@extends('layouts_dashboard.main')

@section('content')
<div class="container">
    <div class="card mb-3">
        <div class="card-header">
            <h2>{{ $levelTest->title }}</h2>
            <p>{{ $levelTest->description }}</p>
        </div>
    </div>
    <form method="POST" action="{{ route('level-test.submit') }}" enctype="multipart/form-data">
        @csrf

        @foreach($levelTestQuestions as $question)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Question {{ $loop->iteration }}: {{ $question->question_type }}</h5>
                </div>
                <div class="card-body">
                    <p>{{ $question->question_text }}</p>

                    @if($question->question_type === 'text')
                        <input type="text" class="form-control" name="question_{{ $loop->iteration }}" required>
                    @elseif($question->question_type === 'voice')
                        <div class="form-group">
                            <button type="button" class="btn btn-primary record-btn" data-target="audio-playback_{{ $loop->iteration }}">Record <i class="fas fa-microphone"></i></button>
                        </div>
                        <input type="file" id="audio-upload_{{ $loop->iteration }}" name="question_{{ $loop->iteration }}_audio" style="display:none;" accept="audio/*">
                        <audio id="audio-playback_{{ $loop->iteration }}" controls style="display:none; width: 100%;"></audio>
                    @elseif($question->question_type === 'choice')
                        @foreach($question->choices as $choice)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="question_{{ $loop->parent->iteration }}" id="option_{{ $loop->index }}" value="{{ $choice->id }}" required>
                                <label class="form-check-label" for="option_{{ $loop->index }}">{{ $choice->choice_text }}</label>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-success">Submit Quiz</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recordBtns = document.querySelectorAll('.record-btn');
        const audioUploads = document.querySelectorAll('input[type="file"]');

        let mediaRecorders = [];
        let audioChunks = [];

        recordBtns.forEach((recordBtn, index) => {
            recordBtn.addEventListener('click', () => {
                const audioPlayback = document.getElementById(recordBtn.dataset.target);

                if (!mediaRecorders[index] || mediaRecorders[index].state === 'inactive') {
                    navigator.mediaDevices.getUserMedia({ audio: true })
                        .then(stream => {
                            mediaRecorders[index] = new MediaRecorder(stream);
                            mediaRecorders[index].start();

                            mediaRecorders[index].addEventListener('dataavailable', event => {
                                audioChunks[index] = event.data;
                            });

                            mediaRecorders[index].addEventListener('stop', () => {
                                const audioBlob = new Blob([audioChunks[index]], { type: 'audio/wav' });
                                audioChunks[index] = [];
                                const audioUrl = URL.createObjectURL(audioBlob);
                                audioPlayback.src = audioUrl;
                                audioPlayback.style.display = 'block';

                                // Create a File object for the audio recording
                                const file = new File([audioBlob], `recording_${index + 1}.wav`, { type: 'audio/wav' });
                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                audioUploads[index].files = dataTransfer.files;
                            });

                            recordBtn.textContent = 'Stop Recording';
                        });
                } else if (mediaRecorders[index].state === 'recording') {
                    mediaRecorders[index].stop();
                    recordBtn.textContent = 'Record';
                }
            });
        });
    });
</script>
@endsection
