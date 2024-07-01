@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
@endsection


@section('content')
    <div class="container my-5">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1>My Enrolled Courses</h1>
            </div>
            @if ($courses->isEmpty())
                <div class="col-md-12 text-center">
                    <h3>You have not enrolled in any courses yet.</h3>
                </div>
            @else
                @foreach ($courses as $course)
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-img-top text-center" style="background-color: #f8f9fa;">
                                <img src="{{ $course->image }}" class="img-fluid" alt="Course Image"
                                    style="width: 80%; padding: 10px;">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $course->title }}</h5>
                                <p class="card-text">
                                    <i class="bi bi-person"></i> {{ $course->students->count() }} Students
                                </p>
                                <p class="card-text">
                                    <strong><i class="bi bi-calendar-event"></i> Enrollment Date:</strong>
                                    {{ $course->pivot->enrollment_date }}<br>
                                    <strong><i class="bi bi-graph-up-arrow"></i> Progress:</strong>
                                    {{ $course->pivot->progress }}%
                                </p>
                                <p class="card-text">
                                    @if ($course->type == 'weekly')
                                        <i class="bi bi-calendar-week"></i> Weekly Course
                                    @else
                                        <i class="bi bi-lightning"></i> Intensive Course
                                    @endif
                                </p>
                                <div class="mt-auto">
                                    <a href="{{ route('admin.showcourse', $course->id) }}"
                                        class="btn btn-primary w-100">View Course</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
