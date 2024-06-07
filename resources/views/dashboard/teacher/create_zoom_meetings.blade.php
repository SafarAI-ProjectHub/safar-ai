@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Add New Zoom Meeting</h5>
            <form id="zoom-meeting-form" action="{{ route('zoom-meetings.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="topic" class="form-label">Topic</label>
                    <input type="text" class="form-control" id="topic" name="topic" required>
                </div>
                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (in minutes)</label>
                    <input type="number" class="form-control" id="duration" name="duration" required>
                </div>
                <div class="mb-3">
                    <label for="agenda" class="form-label">Agenda</label>
                    <textarea class="form-control" id="agenda" name="agenda"></textarea>
                </div>
                <div class="mb-3">
                    <label for="invite_option" class="form-label">Invite Option</label>
                    <select class="form-select" id="invite_option" name="invite_option" required>
                        <option value="all">All Students</option>
                        <option value="teachers">All Teachers</option>
                        <option value="course_specific">Specific Course</option>
                    </select>
                </div>
                <div class="mb-3" id="course_select_div" style="display: none;">
                    <label for="course_id" class="form-label">Course</label>
                    <select class="form-select" id="course_id" name="course_id">
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Create Meeting</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#invite_option').on('change', function() {
                if ($(this).val() === 'course_specific') {
                    $('#course_select_div').show();
                } else {
                    $('#course_select_div').hide();
                }
            });

            $('#zoom-meeting-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('zoom-meetings.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        alert('Zoom meeting created successfully.');
                        // window.location.href = "{{ route('zoom-meetings.index') }}";
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let error in errors) {
                            errorMessages += errors[error] + '\n';
                        }
                        alert('Failed to create Zoom meeting: \n' + errorMessages);

                        // Check if user needs to authorize Zoom
                        if (xhr.responseJSON.url) {
                            window.location.href = xhr.responseJSON.url;
                            // console.log(xhr);
                        }
                    }
                });
            });
        });
    </script>
@endsection
