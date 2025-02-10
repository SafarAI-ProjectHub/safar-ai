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
        .block-circle {
    position: relative;
    width: 180px;
    height: 180px;
    border-radius: 50%;
    background: linear-gradient(135deg, #a0007d,#ed68f8b8, #844dcd14);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    /* box-shadow: 0 0 20px rgba(0, 0, 0, 0.3),
                0 0 30px rgba(190, 9, 205, 0.5); */
    padding: 10px;
    transition: transform 0.3s ease-in-out;
    overflow: hidden; 
}

.block-circle::before {
    content: "";
    position: absolute;
    inset: 0; 
    border-radius: 50%;
    background: inherit; 
    filter: blur(20px);
    opacity: 0.8;
    z-index: 1;
    transition: transform 0.3s ease-in-out;
}

.block-circle:hover {
    transform: scale(1.05) rotate(2deg);
}
.block-circle:hover::before {
    transform: scale(1.2);
}

.block-circle a {
    position: relative;
    z-index: 2; 
    text-decoration: none;
    color: #fff;
    font-weight: bold;
    font-size: 1.1rem;
    text-shadow: 0 0 10px rgba(0,0,0,0.5);
}

    </style>
@endsection

@section('content')
<div class="container my-5">

   
@if($stage === 'blocks')
    <div class="row text-center mb-4">
        <h1>My Blocks</h1>
    </div>
    <div class="row d-flex justify-content-center">
        @forelse($blocks as $block)
            <div class="col-md-3 mb-4 d-flex justify-content-center">
                <div class="block-circle">
                    <a href="{{ route('student.myCourses', ['block_id' => $block->id]) }}">
                        {{ $block->name }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <h3>You have no blocks.</h3>
            </div>
        @endforelse
    </div>
@endif


    @if($stage === 'units')
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1>Units in This Block</h1>
            </div>
            @if ($courses->isEmpty())
                <div class="col-md-12 text-center">
                    <h3>You have not enrolled in any units yet.</h3>
                </div>
            @else
                @foreach ($courses as $course)
                    <div class="col-lg-4 responsive-column-half mb-4">
                        <div class="card card-item">
                            <div class="card-image">
                                <a href="{{ route('student.myCourses', ['unit_id' => $course->id]) }}" class="d-block">
                                    <img class="card-img-top lazy" src="{{ $course->image }}" alt="Card image cap" style="width:100%; min-height:212px; object-fit:cover;">
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
                                                    d="M-272.9,363.2l35.8,20.7c0.7,0.4,0.7,1.3,0,1.7l-35.8,20.7
                                                       c-0.7,0.4-1.5-0.1-1.5-0.9V364
                                                       C-274.4,363.3-273.5,362.8-272.9,363.2z">
                                                </path>
                                            </g>
                                        </svg>
                                    </div>
                                </a>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="{{ route('student.myCourses', ['unit_id' => $course->id]) }}">
                                        {{ $course->title }}
                                    </a>
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

                                {{-- Progress Bar --}}
                                <div class="my-course-progress-bar-wrap justify-content-between d-flex flex-nowrap align-items-center mt-3 position-relative w-100">
                                    <div class="skillbar-box">
                                        <div class="skillbar skillbar-skillbar-2" data-percent="{{ $course->progress }}%">
                                            <div class="skillbar-bar skillbar--bar-2 bg-primary" style="width: {{ $course->progress }}%;"></div>
                                        </div>
                                    </div>
                                    <div class="skill-bar-percent text-nowrap">
                                        {{ $course->progress }}%
                                    </div>
                                </div>

                                @php
                                    $rating = $course->RateAvg();
                                    $fullStars = floor($rating);
                                    $halfStar = $rating - $fullStars >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                @endphp
                                <div class="review-stars pt-3">
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

                                <a href="{{ route('student.myCourses', ['unit_id' => $course->id]) }}"
                                   class="btn btn-primary mt-auto d-block">
                                   View Lessons
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    @if($stage === 'lessons')
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1>Lessons in This Unit</h1>
            </div>
            @if ($lessons->isEmpty())
                <div class="col-md-12 text-center">
                    <h3>No Lessons found.</h3>
                </div>
            @else
                @foreach ($lessons as $lesson)
                    <div class="col-md-4 mb-4">
                        <div class="card card-item">
                            <div class="card-body">
                                <h5 class="card-title">
                                    {{ $lesson->title ?? 'Lesson Title' }}
                                </h5>
                                <p>
                                    {{ \Illuminate\Support\Str::limit($lesson->content ?? '', 100) }}
                                </p>
                                <a href="{{ route('student.myCourses', ['lesson_id' => $lesson->id]) }}"
                                   class="btn btn-primary mt-auto d-block">
                                   View Lesson
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    @if($stage === 'lesson_details')
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1>{{ $lesson->title ?? 'Lesson Title' }}</h1>
            </div>
            <div class="col-12">
                @if($lesson->content_type === 'text')
                    <p>{!! nl2br(e($lesson->content)) !!}</p>
                @elseif($lesson->content_type === 'video')
                    <video width="100%" controls>
                        <source src="{{ asset($lesson->content) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @elseif($lesson->content_type === 'youtube')
                    <iframe width="560" height="315" 
                            src="https://www.youtube.com/embed/{{ $lesson->content }}"
                            frameborder="0" allowfullscreen>
                    </iframe>
                @else
                    <p>{!! nl2br(e($lesson->content ?? 'No content')) !!}</p>
                @endif
            </div>
        </div>
    @endif

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
    
    <script>
        function adjustCardHeights() {
            var maxHeight = 0;
            var maximgHeight = 0;
            $('.card.card-item.youtube').each(function() {
                var thisHeight = $(this).height();
                if (thisHeight > maxHeight) {
                    maxHeight = thisHeight;
                }
            });
            $('.card-img-top img').each(function() {
                var imgHeight = $(this).height();
                if (imgHeight > maximgHeight) {
                    maximgHeight = imgHeight;
                }
            });
            $('.card.card-item.youtube').height(maxHeight);
        }

        $(window).on('load', function() {
            adjustCardHeights();
        });
    </script>
@endsection
