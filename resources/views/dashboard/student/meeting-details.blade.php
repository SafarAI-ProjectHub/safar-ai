@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Zoom Meeting Details</h5>

            <p>
                <strong>Teacher Name:</strong>
                {{ $userMeeting->meeting->user->full_name }}
            </p>

            <p>
                <strong>Topic:</strong>
                {{ $userMeeting->meeting->topic }}
            </p>

            <p>
                <strong>Agenda:</strong>
                {{ $userMeeting->meeting->agenda }}
            </p>

            <p>
                <strong>Start Time:</strong>
                {{ $userMeeting->meeting->start_time->format('d-m-Y / h:i A') }}
            </p>

            <p>
                <strong>Duration:</strong>
                @php
                    $hours = intdiv($userMeeting->meeting->duration, 60);
                    $minutes = $userMeeting->meeting->duration % 60;
                @endphp
                @if ($hours > 0)
                    {{ $hours }} hour{{ $hours > 1 ? 's' : '' }}
                    @if ($minutes > 0)
                        {{ $minutes }} minute{{ $minutes > 1 ? 's' : '' }}
                    @endif
                @else
                    {{ $minutes }} minute{{ $minutes > 1 ? 's' : '' }}
                @endif
            </p>

            <div class="mt-3">
                <a href="{{ $userMeeting->meeting->join_url }}" class="btn btn-primary" target="_blank">
                    Join Meeting
                </a>
                <a href="{{ route('student.meetings.index') }}" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>
@endsection
