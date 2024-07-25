@extends('layouts_dashboard.main')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/line-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fancybox.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tooltipster.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        .card.card-item {
            height: 450px;
        }

        .card-text.description {
            height: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bg-primary {
            background-color: #be09cd !important;
        }

        .skillbar-box {
            width: 100%;
        }

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

        .card.card-item.youtube {
            height: 315px;
        }

        .card-body.d-flex.flex-column.youtube {
            padding: 10px 20px 0;
        }
    </style>
@endsection

@section('content')
    @php
        $isSUB = true;
    @endphp
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
                        window.location.href = '{{ route('student.dashboard') }}';
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
                            <div class="card card-item">
                                <div class="card-image">
                                    <a href="javascript:void(0);" class="d-block course-link"
                                        data-course-id="{{ $course->id }}"
                                        data-enrolled="{{ in_array($course->id, $enrolledCourseIds) }}">
                                        <img class="card-img-top lazy" src="{{ $course->image }}" alt="Card image cap">
                                        <div class="play-button">
                                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                                viewBox="-307.4 338.8 91.8 91.8" xml:space="preserve">
                                                <style type="text/css">
                                                    .st0 {
                                                        opacity: 0.6;
                                                        fill: #000000;
                                                        border-radius: 100px;
                                                    }

                                                    .st1 {
                                                        fill: #ffffff;
                                                    }
                                                </style>
                                                <g>
                                                    <circle class="st0" cx="-261.5" cy="384.7" r="45.9"></circle>
                                                    <path class="st1"
                                                        d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364 C-274.4,363.3-273.5,362.8-272.9,363.2z">
                                                    </path>
                                                </g>
                                            </svg>
                                        </div>
                                    </a>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $course->title }}</h5>
                                    <p class="card-text">
                                        <i class="bi bi-person"></i> {{ $course->number_of_students }} Students
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
                                    <div class="rating-wrap d-flex align-items-center justify-content-between  mb-3">
                                        <div class="review-stars">
                                            @php
                                                $rating = $course->RateAvg();
                                                $fullStars = floor($rating);
                                                $halfStar = $rating - $fullStars >= 0.5 ? true : false;
                                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                            @endphp

                                            @for ($i = 0; $i < $fullStars; $i++)
                                                <span class="la la-star"></span>
                                            @endfor

                                            @if ($halfStar)
                                                <span class="la la-star-half-o"></span>
                                            @endif

                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                <span class="la la-star-o"></span>
                                            @endfor
                                        </div>
                                    </div>
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
        @php
            $isSUB = false;
        @endphp
        <div class="container my-5">
            <div class="row" id="video-list">
                @foreach ($videos as $video)
                    <div class="col-md-4 mb-4">
                        <div class="card card-item youtube h-100">
                            <div class="card-image">
                                <a href="javascript:void(0);" class="d-block video-link"
                                    data-video-id="{{ $video->id }}">
                                    <img class="card-img-top lazy" src="{{ $video->thumbnail_url }}" alt="Video Thumbnail">
                                    <div class="play-button">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                            viewBox="-307.4 338.8 91.8 91.8" xml:space="preserve">
                                            <style type="text/css">
                                                .st0 {
                                                    opacity: 0.6;
                                                    fill: #000000;
                                                    border-radius: 100px;
                                                }

                                                .st1 {
                                                    fill: #ffffff;
                                                }
                                            </style>
                                            <g>
                                                <circle class="st0" cx="-261.5" cy="384.7" r="45.9"></circle>
                                                <path class="st1"
                                                    d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364 C-274.4,363.3-273.5,362.8-272.9,363.2z">
                                                </path>
                                            </g>
                                        </svg>
                                    </div>
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column youtube">
                                <h5 class="card-title mb-1">{{ $video->title }}</h5>

                                <div class="d-flex justify-content-between mt-auto">
                                    <p class="card-text">
                                        <i class="bi bi-eye"></i> {{ $video->view_count }} Views
                                    </p>
                                    <p class="card-text">
                                        <i class="bi bi-hand-thumbs-up"></i> {{ $video->like_count }} Likes
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center">
                {!! $videos->links() !!}
            </div>
        </div>
        @if (auth()->user()->getAgeGroup() !== '1-5')
            <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <h5 class="modal-title">Unlock Premium Features</h5>
                            <p>Get exclusive access to advanced courses and resources. Enhance your learning with our
                                Premium Plan!</p>
                            <button class="btn btn-detail btn-primary"
                                onclick="location.href='{{ route('subscription.details') }}'">See Details</button>
                            <p class="mt-4"><strong>Note:</strong> if you have subscribed and still see this message,
                                please wait for up to one minute. The page will reload, and you will gain access to the
                                courses. The delay is due to payment processing by PayPal. Thank you for your patience!</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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

        <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="videoTitle" class="course-title"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="video-details">
                            <div class="ratio ratio-16x9">
                                <iframe id="videoFrame" src="" frameborder="0" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            @if (Auth::user()->student->subscription_status !== 'subscribed' || $subscription->status !== 'active')
                @if (auth()->user()->getAgeGroup() !== '0-5')
                    $('#subscriptionModal').modal('show');
                @endif
            @endif

            $('.course-link').click(function() {
                var courseId = $(this).data('course-id');
                var isEnrolled = $(this).data('enrolled');

                if (isEnrolled) {
                    window.location.href = '/courses/' + courseId + '/show';
                } else {
                    $.ajax({
                        url: '{{ route('student.getCourseDetails') }}',
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
                }
            });

            $('.enroll-btn').click(function() {
                var courseId = $(this).data('course-id');
                $.ajax({
                    url: '{{ route('student.getCourseDetails') }}',
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

            $('#enrollButton').click(function() {
                var courseId = $(this).data('course-id');
                $.ajax({
                    url: '{{ route('student.enroll') }}',
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
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            });

            @if (!$isSUB)
                $(document).on('click', '.video-link', function() {
                    var videoId = $(this).data('video-id');
                    $.ajax({
                        url: '{{ route('student.dashboard') }}',
                        type: 'GET',
                        data: {
                            video_id: videoId
                        },
                        success: function(response) {
                            $('#videoTitle').text(response.title);
                            $('#videoFrame').attr('src', 'https://www.youtube.com/embed/' +
                                response.video_id);
                            $('#videoModal').modal('show');
                        },
                        error: function(xhr) {
                            console.log(xhr.responseText);
                        }
                    });
                });

                $('#videoModal').on('hidden.bs.modal', function() {
                    $('#videoFrame').attr('src', '');
                });

                function fetchVideos(page) {
                    $.ajax({
                        url: '{{ route('student.dashboard') }}',
                        type: 'GET',
                        data: {
                            page: page
                        },
                        success: function(data) {
                            $('#video-list').html(data);
                        }
                    });
                }

                function adjustCardHeights() {
                    var maxHeight = 0;
                    $('.card.card-item.youtube').each(function() {
                        var thisHeight = $(this).height();
                        if (thisHeight > maxHeight) {
                            maxHeight = thisHeight;
                        }
                    });
                    $('.card.card-item.youtube').height(maxHeight);
                }

                $(window).on('load', function() {
                    adjustCardHeights();
                });

                $('.card.card-item.youtube img').on('load', function() {
                    adjustCardHeights();
                });
            @endif
        });
    </script>
@endsection
