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
            height: 500px;
        }

        .card-text.description {
            height: 80px;
            /* Adjust based on your design */
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bg-primary {
            background-color: #be09cd !important;
        }

        .skillbar-box {
            width: 100%;
        }
    </style>
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
                    <div class="col-lg-4 responsive-column-half mb-4">
                        <div class="card card-item">
                            <div class="card-image">
                                <a href="{{ route('admin.showcourse', $course->id) }}" class="d-block">
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
                                                    d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7c-0.7,0.4-1.5-0.1-1.5-0.9V364
                                                                                                                                                            C-274.4,363.3-273.5,362.8-272.9,363.2z">
                                                </path>
                                            </g>
                                        </svg>
                                    </div>

                                </a>
                            </div>
                            <!-- end card-image -->
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('admin.showcourse', $course->id) }}">{{ $course->title }}</a>
                                </h5>
                                <p class="card-text description lh-22 pt-2">
                                    @if (strlen($course->description) > 100)
                                        {{ substr($course->description, 0, 100) }}...
                                    @else
                                        {{ $course->description }}
                                    @endif
                                </p>
                                <p class="card-text lh-22 pt-2">
                                    {{ $course->teacher ? $course->teacher->user->full_name : 'No teacher assigned yet' }}
                                </p>
                                <div
                                    class="my-course-progress-bar-wrap justify-content-between d-flex flex-nowrap align-items-center mt-3 position-relative w-100">

                                    <div class="skillbar-box">
                                        <div class="skillbar skillbar-skillbar-2" data-percent="{{ $course->progress }}%">
                                            <div class="skillbar-bar skillbar--bar-2 bg-primary "
                                                style="width: {{ $course->progress }}%;"></div>
                                        </div>
                                        <!-- End Skill Bar -->
                                    </div>
                                    <div class="skill-bar-percent text-nowrap">{{ $course->progress }}%</div>


                                </div>
                                <dev class="rating-wrap d-flex align-items-center justify-content-between pt-3">
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
                                </dev>

                                <a href="{{ route('admin.showcourse', $course->id) }}"
                                    class="btn btn-primary mt-3 d-block">View Course</a>
                            </div>
                            <!-- end card-body -->
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/isotope.js') }}"></script>
    <script src="{{ asset('js/waypoint.min.js') }}"></script>
    <script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('js/fancybox.js') }}"></script>
    <script src="{{ asset('js/datedropper.min.js') }}"></script>
    <script src="{{ asset('js/emojionearea.min.js') }}"></script>
    <script src="{{ asset('js/tooltipster.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
@endsection
