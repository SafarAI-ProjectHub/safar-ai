@extends('layouts_dashboard.main')

@section('styles')
    <!-- استدعاء ملفات CSS وباقي الإعدادات -->
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
            height: 100%;
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

        /* قمنا بتعطيل زر الإغلاق (X) في الهيدر، وأيضاً سنمنع الإغلاق بالنقر خارج الـ Modal */
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
@elseif (Auth::user()->student->subscription_status == 'subscribed' && isset($subscription->status) && $subscription->status == 'active')
    @if($courses->isEmpty())
        <div class="container my-5">
            <div class="row text-center mb-4">
                <h1>Free Videos</h1>
            </div>
            @if($videos->isEmpty())
                <div class="row">
                    <div class="col-12 text-center">
                        <p>No videos available</p>
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($videos as $video)
                    <div class="col-md-4 mb-4">
                        <div class="card card-item youtube h-100">
                            <div class="card-image">
                                <a href="javascript:void(0);"
                                   class="d-block video-link"
                                   data-video-id="{{ $video->video_id }}"
                                   data-video-title="{{ $video->title }}">
                                    <img class="card-img-top lazy" src="{{ $video->thumbnail_url }}" alt="Video Thumbnail">
                                    <div class="play-button"></div>
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column youtube">
                                <h5 class="card-title mb-1">{{ $video->title }}</h5>
                                <div class="d-flex justify-content-between mt-auto">
                                    <p class="card-text"><i class="bi bi-eye"></i> {{ $video->view_count }} Views</p>
                                    <p class="card-text"><i class="bi bi-hand-thumbs-up"></i> {{ $video->like_count }} Likes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center">
                    {!! $videos->links() !!}
                </div>
            @endif
        </div>
    @else
        <div class="container my-5">
            <div class="row text-center mb-4">
                <h1>All units</h1>
                <p>Found {{ $courses->count() }} units</p>
            </div>
            <div class="row">
                @foreach ($courses as $course)
                <div class="col-md-4 mb-4">
                    <div class="card card-item">
                        <div class="card-image">
                            <a href="javascript:void(0);" class="d-block course-link"
                               data-course-id="{{ $course->id }}"
                               data-enrolled="{{ in_array($course->id, $enrolledCourseIds) }}">
                                <img class="card-img-top lazy" src="{{ $course->image }}" alt="Card image cap">
                                <div class="play-button"></div>
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
                                    <i class="bi bi-calendar-week"></i> Weekly Unit
                                @else
                                    <i class="bi bi-lightning"></i> Intensive Unit
                                @endif
                            </p>
                            @php
                                $rating = $course->RateAvg();
                                $fullStars = floor($rating);
                                $halfStar = $rating - $fullStars >= 0.5 ? true : false;
                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            @endphp
                            <div class="d-flex align-items-center justify-content-between pt-2">
                                <div class="review-stars">
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
                                       class="btn btn-secondary w-100">View Unit</a>
                                @else
                                    <button class="btn btn-primary w-100 enroll-btn"
                                            data-course-id="{{ $course->id }}">
                                        Enroll in Unit
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    @endif
@else
    <div class="container my-5">
        <div class="row text-center mb-4">
            <h1>Free Videos</h1>
        </div>
        @if($videos->isEmpty())
            <div class="row">
                <div class="col-12 text-center">
                    <p>No videos available</p>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($videos as $video)
                <div class="col-md-4 mb-4">
                    <div class="card card-item youtube h-100">
                        <div class="card-image">
                            <a href="javascript:void(0);"
                               class="d-block video-link"
                               data-video-id="{{ $video->video_id }}"
                               data-video-title="{{ $video->title }}">
                                <img class="card-img-top lazy" src="{{ $video->thumbnail_url }}" alt="Video Thumbnail">
                                <div class="play-button"></div>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column youtube">
                            <h5 class="card-title mb-1">{{ $video->title }}</h5>
                            <div class="d-flex justify-content-between mt-auto">
                                <p class="card-text"><i class="bi bi-eye"></i> {{ $video->view_count }} Views</p>
                                <p class="card-text"><i class="bi bi-hand-thumbs-up"></i> {{ $video->like_count }} Likes</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center">
                {!! $videos->links() !!}
            </div>
        @endif
    </div>

    @if (auth()->user()->getAgeGroup() !== '1-5')
        <!-- 
            هنا نجعل الـ Modal لا يُغلق إلا عند الضغط على زر "See Details"
            وذلك بإضافة data-bs-backdrop="static" و data-bs-keyboard="false" 
            وإزالة زر الإغلاق (X) في الهيدر
         -->
        <div class="modal fade"
             id="subscriptionModal"
             tabindex="-1"
             aria-labelledby="subscriptionModalLabel"
             aria-hidden="true"
             data-bs-backdrop="static"  <!-- منع إغلاق الـ Modal بالنقر خارجها -->
             data-bs-keyboard="false">  <!-- منع إغلاق الـ Modal بزر Escape من الكيبورد -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- تمت إزالة زر الإغلاق (X) -->
                        <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
                    </div>
                    <div class="modal-body">
                        <h5 class="modal-title">Unlock Premium Features</h5>
                        <p>Get exclusive access to advanced units and resources. Enhance your learning with our
                            Premium Plan!</p>
                        <!-- زر الإجراء الوحيد الذي يسمح للمستخدم بإغلاق الـ Modal عبر التحويل لصفحة الاشتراك -->
                        <button class="btn btn-detail btn-primary"
                                onclick="location.href='{{ route('subscription.details') }}'">
                            See Details
                        </button>
                        <p class="mt-4">
                            <strong>Note:</strong> if you have subscribed and still see this message,
                            please wait for up to one minute. The page will reload, and you will gain access to the
                            units. The delay is due to payment processing by PayPal. Thank you for your patience!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

{{-- Modals --}}
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
                <button id="enrollButton" class="btn btn-primary w-100 mt-3">Enroll in Unit</button>
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
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // إذا لم يكن المستخدم مشتركاً، سنظهر المودال الإجباري "Subscription Modal"
        @if (Auth::user()->student->subscription_status !== 'subscribed' || $subscription->status !== 'active')
            @if (auth()->user()->getAgeGroup() !== '1-5')
                $('#subscriptionModal').modal('show');
            @endif
        @endif

        // عند الضغط على اسم الدورة (كورس) إذا كان المستخدم "ملتحق" يتم نقله مباشرة لصفحة الكورس
        // وإلا سيظهر له المودال بعرض تفاصيل الكورس مع زر التحاق
        $('.course-link').click(function() {
            var courseId = $(this).data('course-id');
            var isEnrolled = $(this).data('enrolled');

            if (isEnrolled) {
                window.location.href = '/courses/' + courseId + '/show';
            } else {
                $.ajax({
                    url: '{{ route('student.getCourseDetails') }}',
                    type: 'GET',
                    data: { course_id: courseId },
                    success: function(response) {
                        $('#courseTitle').text(response.course.title);
                        $('#courseDescription').text(response.course.description);
                        $('#teacherName').text(response.course.teacher_name || 'N/A');
                        $('#teacherExperience').text(response.course.years_of_experience || 'N/A');
                        $('#enrollButton').data('course-id', courseId);
                        $('#courseModal').modal('show');
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                    }
                });
            }
        });

        // عند الضغط على زر "Enroll in Unit" ضمن أي كورس
        $('.enroll-btn').click(function() {
            var courseId = $(this).data('course-id');
            $.ajax({
                url: '{{ route('student.getCourseDetails') }}',
                type: 'GET',
                data: { course_id: courseId },
                success: function(response) {
                    $('#courseTitle').text(response.course.title);
                    $('#courseDescription').text(response.course.description);
                    $('#teacherName').text(response.course.teacher_name || 'N/A');
                    $('#teacherExperience').text(response.course.years_of_experience || 'N/A');
                    $('#enrollButton').data('course-id', courseId);
                    $('#courseModal').modal('show');
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        });

        // زر الالتحاق في المودال
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
                        text: 'You have been enrolled in the Unit.',
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

        // عند الضغط على الفيديو المجاني لعرضه في مودال اليوتيوب
        $(document).on('click', '.video-link', function() {
            var videoId = $(this).data('video-id');
            var videoTitle = $(this).data('video-title');

            $('#videoTitle').text(videoTitle);
            $('#videoFrame').attr('src', 'https://www.youtube.com/embed/' + videoId);
            $('#videoModal').modal('show');
        });

        // عند إغلاق المودال الخاص بالفيديو، يجب إيقاف تشغيله
        $('#videoModal').on('hide.bs.modal', function() {
            $('#videoFrame').attr('src', '');
        });

        // تابع لاستدعاء الفيديوهات بالصفحات (إذا أردت إضافة Pagination AJAX)
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

        // ضبط ارتفاع الكروت (في حال احتجت ذلك)
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
    });
</script>
@endsection
