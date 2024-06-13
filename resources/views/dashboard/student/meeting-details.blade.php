@extends('layouts_dashboard.main')

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Zoom Meeting Details</h5>
            <p><strong>Teacher Name:</strong> {{ $userMeeting->meeting->user->full_name }}</p>
            <p><strong>Topic:</strong> {{ $userMeeting->meeting->topic }}</p>
            <p><strong>Agenda:</strong> {{ $userMeeting->meeting->agenda }}</p>
            <p><strong>Start Time:</strong> {{ \Carbon\Carbon::parse($userMeeting->meeting->start_time)->format('d-m-Y / h:i A') }}</p>
            <p><strong>Duration:</strong> 
                @php
                    $hours = intdiv($userMeeting->meeting->duration, 60);
                    $minutes = $userMeeting->meeting->duration % 60;
                    echo $hours > 0 ? ($hours . ' hour' . ($hours > 1 ? 's' : '') . ($minutes > 0 ? ' ' . $minutes . ' minute' . ($minutes > 1 ? 's' : '') : '')) : $userMeeting->meeting->duration . ' minute' . ($userMeeting->meeting->duration > 1 ? 's' : '');
                @endphp
            </p>
            <p> <a href="{{ $userMeeting->meeting->join_url }}" class="btn btn-primary" target="_blank">Join Meeting</a> <strong></strong> <a href="{{ route('student.meetings.index') }}" class="btn btn-secondary">Back</a></p>
            
        </div>
    </div> 
@endsection
