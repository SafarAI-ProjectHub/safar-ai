@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">

    <style>
        .modal-dialog {
            max-width: 600px;
            margin: 30px auto;
        }

        .modal-content {
            padding: 20px;
        }

        .modal-header {
            border-bottom: none;
        }

        .btn-close {
            float: right;
        }

        .btn-detail {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            text-decoration: none;
            margin-top: 15px;
        }

        .btn-detail:hover {
            background-color: #0056b3;
        }

        .course-details {
            max-height: 400px;
            overflow-y: auto;
        }

        .course-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .course-description {
            white-space: pre-wrap;
            margin-bottom: 1rem;
        }

        .modal-body {
            padding: 2rem;
        }
    </style>
@endsection

@section('content')
    @if (Auth::user()->status == 'pending')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Your account is pending',
                    text: 'You need to take the level test to start your learning journey.',
                    icon: 'warning',
                    confirmButtonText: 'Take the level test'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the level test page
                        window.location.href = '{{ route('level.test') }}';
                    }
                });
            });
        </script>
    @elseif (Auth::user()->student->subscription_status == 'subscribed' &&
            isset($subscription->status) &&
            $subscription->status == 'active')
        <div class="container my-5">
            <div class="row">
                <div class="col-md-12 text-center mb-4">
                    <h1>All Courses</h1>
                </div>
                @if ($courses->isEmpty())
                    <div class="col-md-12 text-center">
                        <h3>No courses available</h3>
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
                                    {{-- the number of student in htis coures  --}}
                                    <p class="card-text">
                                        <i class="bi bi-person"></i> {{ $course->students->count() }} Students
                                    </p>
                                    <p class="card-text">
                                        @if (in_array($course->id, $enrolledCourseIds))
                                            <span class="badge bg-success">Enrolled</span>
                                        @endif
                                    </p>
                                    <p class="card-text">
                                        @if ($course->type == 'weekly')
                                            <i class="bi bi-calendar-week"></i> Weekly Course
                                        @else
                                            <i class="bi bi-lightning"></i> Intensive Course
                                        @endif
                                    </p>
                                    <div class="mt-auto">
                                        @if (in_array($course->id, $enrolledCourseIds))
                                            <a href="{{ route('admin.showcourse', $course->id) }}"
                                                class="btn btn-secondary w-100">View Course</a>
                                        @else
                                            <button class="btn btn-primary w-100 enroll-btn"
                                                data-course-id="{{ $course->id }}">Enroll in Course</button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-md-12">
                <h1>Watch Our YouTube Playlist</h1>
            </div>
            <div class="col-md-12">
                <div class="card" style="width: 100%;">
                    <div class="text-center">
                        <iframe width="100%" height="315"
                            src="https://www.youtube.com/embed/videoseries?list=PLWz5rJ2EKKc8j2fah8n19gP9IpRhol_Ip"
                            frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>

        <!-- New Modal for suggesting subscription -->
        <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="modal-title">Unlock Premium Features</h5>
                        <p>Get exclusive access to advanced courses and resources. Enhance your learning with our Premium
                            Plan!</p>
                        <button class="btn btn-detail btn-primary"
                            onclick="location.href='{{ route('subscription.details') }}'">See Details</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal for Course Details -->
    <div class="modal fade" id="courseModal" tabindex="-1" aria-labelledby="courseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="courseModalLabel">Course Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="course-details">
                        <h5 id="courseTitle" class="course-title"></h5>
                        <p id="courseDescription" class="course-description"></p>
                        <p><strong>Teacher Name:</strong> <span id="teacherName"></span></p>
                        <p><strong>Years of Experience:</strong> <span id="teacherExperience"></span></p>
                    </div>
                    <button id="enrollButton" class="btn btn-primary w-100 mt-3">Enroll in Course</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Trigger the modal for users without a subscription
            @if (Auth::user()->student->subscription_status !== 'subscribed' || $subscription->status !== 'active')
                $('#subscriptionModal').modal('show');
            @endif

            // Enroll button click event
            $('.enroll-btn').click(function() {
                var courseId = $(this).data('course-id');
                $.ajax({
                    url: '{{ route('student.getCourseDetails') }}', // Adjust this route
                    type: 'GET',
                    data: {
                        course_id: courseId
                    },
                    success: function(response) {
                        $('#courseTitle').text(response.course.title);
                        $('#courseDescription').text(response.course.description);
                        $('#teacherName').text(response.course.teacher_name || 'N/A');
                        $('#teacherExperience').text(response.course.years_of_experience ||
                            'N/A');
                        $('#enrollButton').data('course-id', courseId);
                        $('#courseModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            // Enroll in course
            $('#enrollButton').click(function() {
                var courseId = $(this).data('course-id');
                $.ajax({
                    url: '{{ route('student.enroll') }}', // Adjust this route
                    type: 'POST',
                    data: {
                        course_id: courseId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#courseModal').modal('hide');
                        Swal.fire({
                            title: 'Enrolled Successfully!',
                            text: 'You have been enrolled in the course.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location
                                .reload(); // Refresh the page to update the enrolled courses
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });
        });
    </script>
@endsection
