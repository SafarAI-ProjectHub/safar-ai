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

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Emilys+Candy&family=Send+Flowers&display=swap" rel="stylesheet">

    <style>
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #f8f9fa !important;
            min-height: 100vh !important;
        }
        .lesson-wrapper {
            width: 100%;
            max-width: 100%;
            height: auto;
            min-height: calc(100vh + 155px);    
            margin: 2rem auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: none;
            position: relative;
            padding: 2rem;
            overflow: hidden;
            padding-bottom: 155px;
        }

        @if($stage === 'lesson_details')
            .lesson-wrapper {
                max-width: 800px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                overflow: hidden; 
            }
            .svg-corner {
                position: absolute;
                width: 400px;
                height: 200px;
                pointer-events: none;
                opacity: 0.7;
                z-index: 1;  
            }
            .svg-top-left {
                top: 0px;
                left: 1px;
            }
            .svg-top-right {
                top: 0px;
                right: 1px;
                transform: scaleX(-1); 
            }
            .svg-bottom-left {
                bottom: 0px;
                left: 1px;
                transform: rotate(180deg) scaleX(-1);
            }
            .svg-bottom-right {
                bottom: 0px;
                right: 1px;
                transform: rotate(180deg);
            }
            .lesson-logo {
                text-align: center;
                margin: 1.5rem 0 0.5rem; 
                position: relative;
                z-index: 3; 
            }
            .lesson-logo img {
                width: 120px; 
            }
            .fancy-title {
                font-family: "Playfair Display", serif;
                font-optical-sizing: auto;
                font-size: 3rem;
                font-weight: 800;  
                color: #333;
                text-align: center;
                margin-bottom: 2rem; 
            }
        @endif

        .block-circle {
            position: relative;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: linear-gradient(135deg, #a0007d, #ed68f8b8, #844dcd14);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 10px;
            transition: transform 0.3s ease-in-out;
            overflow: hidden;
            margin: 0 auto;
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
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        .card.card-item {
            height: 100%;
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .card-text.description {
            height: 80px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .lesson-card {
            position: relative;
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            min-height: 280px; 
            display: flex;
            flex-direction: column;
            border: none;
        }
        .lesson-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 15px;
            padding: 2px; 
            background: linear-gradient(135deg, #ff0080, #be09cd, #6200ea);
            -webkit-mask: 
                linear-gradient(#fff 0 0) content-box, 
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            pointer-events: none;
            z-index: 0;
        }
        .lesson-card .card-body {
            position: relative;
            z-index: 1;
            color: #444;
        }
        .lesson-card h5.card-title {
            color: #333;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .lesson-card p {
            color: #555;
        }
        .lesson-card:hover {
            transform: scale(1.02);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .lesson-card .btn-primary {
            background-color: #be09cd;
            border: none;
            margin-top: auto; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }
        .lesson-card .btn-primary:hover {
            background-color: #a0007d;
        }
        .lesson-body-text {
            text-align: left;
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin: 1rem auto;
            max-width: 700px;
        }
    </style>
@endsection


@section('content')
    @php
        /*
            تحديد عنوان الصفحة بحسب المرحلة (stage) الحالية:
            1- blocks   -> صفحة الكتل
            2- units    -> صفحة الوحدات
            3- lessons  -> صفحة الدروس
            4- lesson_details -> تفاصيل الدرس
        */
        $pageTitle = '';
        if ($stage === 'blocks') {
            $pageTitle = 'My Blocks';
        } elseif ($stage === 'units') {
            $pageTitle = 'Units in This Block';
        } elseif ($stage === 'lessons') {
            $pageTitle = 'Lessons in This Unit';
        } elseif ($stage === 'lesson_details') {
            $pageTitle = $lesson->title ?? 'Lesson Title';
        }
    @endphp

    <div class="lesson-wrapper">
        {{-- 
            إذا كنا في مرحلة التفاصيل، نظهر الزخارف (SVG) في الأركان + اللوغو في المنتصف بالأعلى + العنوان بخط مزخرف
        --}}
        @if($stage === 'lesson_details')
            <div class="svg-corner svg-top-left">
                <img src="{{ asset('images/file.svg') }}" alt="svg-ornament">
            </div>
            <div class="svg-corner svg-top-right">
                <img src="{{ asset('images/file.svg') }}" alt="svg-ornament">
            </div>
            <div class="svg-corner svg-bottom-left">
                <img src="{{ asset('images/file.svg') }}" alt="svg-ornament">
            </div>
            <div class="svg-corner svg-bottom-right">
                <img src="{{ asset('images/file.svg') }}" alt="svg-ornament">
            </div>

            <div class="lesson-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logo">
            </div>

            <h1 class="fancy-title">{{ $pageTitle }}</h1>
        @else
            {{-- غير ذلك، نعرض العنوان بشكل عادي في منتصف الصفحة --}}
            <h1 class="text-center mb-4">{{ $pageTitle }}</h1>
        @endif

        {{-- ############################################ BLOCKS ############################################ --}}
        @if($stage === 'blocks')
            <div class="row text-center mb-4">
                <h2>Choose a Block</h2>
            </div>
            <div class="row d-flex justify-content-center">
                @forelse($blocks as $block)
                    <div class="col-md-3 mb-4">
                        <div class="block-circle">
                            {{-- الانتقال لصفحة الكورسات (الوحدات) الخاصة بهذا الـ Block --}}
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

        {{-- ############################################ UNITS ############################################ --}}
        @if($stage === 'units')
            <div class="row">
                @if ($courses->isEmpty())
                    <div class="col-md-12 text-center">
                        <h3>You have not enrolled in any units yet.</h3>
                    </div>
                @else
                    @foreach ($courses as $course)
                        @php
                            // جلب الدروس التابعة لهذا الكورس
                            // إن كانت العلاقة اسمها ->units
                            $lessons = $course->units ?? collect();
                            $totalLessons = $lessons->count();

                            $studentId = Auth::check() && Auth::user()->student
                                         ? Auth::user()->student->id
                                         : null;

                            $completedCount = 0;
                            if ($studentId && $totalLessons > 0) {
                                $lessonIds = $lessons->pluck('id')->toArray();
                                // نبحث في student_units عن الدروس المكتملة
                                $completedCount = DB::table('student_units')
                                    ->where('student_id', $studentId)
                                    ->whereIn('unit_id', $lessonIds)
                                    ->where('completed', 1)
                                    ->count();
                            }

                            // نسبة الإنجاز
                            $progress = $totalLessons > 0
                                ? round(($completedCount / $totalLessons) * 100)
                                : 0;
                        @endphp

                        <div class="col-lg-4 responsive-column-half mb-4">
                            <div class="card card-item">
                                <div class="card-image">
                                    <a href="{{ route('student.myCourses', ['unit_id' => $course->id]) }}" class="d-block">
                                        <img class="card-img-top lazy"
                                             src="{{ $course->image }}"
                                             alt="Course Image"
                                             style="width:100%; min-height:212px; object-fit:cover;">
                                    </a>
                                </div>
                                <div class="card-body d-flex flex-column">
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

                                    {{-- شريط التقدم --}}
                                    <div class="mb-3">
                                        <label>Progress:</label>
                                        <div class="progress" style="height: 20px; background-color: #e0e0e0;">
                                            <div class="progress-bar"
                                                 role="progressbar"
                                                 style="width: {{ $progress }}%; background-color: #d455df;"
                                                 aria-valuenow="{{ $progress }}"
                                                 aria-valuemin="0"
                                                 aria-valuemax="100">
                                                 {{ $progress }}%
                                            </div>
                                        </div>
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

        {{-- ############################################ LESSONS ############################################ --}}
        @if($stage === 'lessons')
            @php
                $completedLessons = $lessons->filter(function($l){ 
                    return isset($l->is_completed) && $l->is_completed; 
                })->count();
                $totalLessons = $lessons->count();
                $progress = $totalLessons > 0 
                    ? round(($completedLessons / $totalLessons) * 100) 
                    : 0;
            @endphp

            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="d-flex align-items-center">
                        <span class="me-2">Progress:</span>
                        <div class="progress flex-grow-1" style="height: 20px;">
                            <div class="progress-bar" 
                                 role="progressbar" 
                                 style="width: {{ $progress }}%;" 
                                 aria-valuenow="{{ $progress }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $progress }}%
                            </div>
                        </div>
                    </div>
                </div>

                @if ($lessons->isEmpty())
                    <div class="col-md-12 text-center">
                        <h3>No Lessons found.</h3>
                    </div>
                @else
                    @foreach ($lessons as $lesson)
                        <div class="col-md-4 mb-4">
                            <div class="card lesson-card">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title d-flex align-items-center">
                                        {{ $lesson->title ?? 'Lesson Title' }}
                                        @if(isset($lesson->is_completed) && $lesson->is_completed)
                                            <i class="bi bi-check-circle-fill text-success ms-2"></i>
                                        @endif
                                    </h5>
                                    <p>{!! $lesson->subtitle !!}</p>
                                    <a href="{{ route('student.myCourses', ['lesson_id' => $lesson->id]) }}"
                                       class="btn btn-primary d-block">
                                        View Lesson
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        @endif

        {{-- ###################################### LESSON DETAILS ###################################### --}}
        @if($stage === 'lesson_details')
            <div class="lesson-body-text">
                @if($lesson->content_type === 'text')
                    {!! $lesson->content !!}
                @elseif($lesson->content_type === 'video')
                    <video controls style="max-width: 100%; border: 2px solid #ddd; border-radius: 5px;">
                        <source src="{{ asset($lesson->content) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                @elseif($lesson->content_type === 'youtube')
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                        <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                src="https://www.youtube.com/embed/{{ $lesson->content }}"
                                frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                @else
                    {!! nl2br(e($lesson->content ?? 'No content')) !!}
                @endif
            </div>

            <div class="mt-4 text-center">
                @if(!isset($lesson->is_completed) || !$lesson->is_completed)
                    <form action="{{ route('student.markLessonCompleted', ['lesson_id' => $lesson->id]) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            Mark Lesson as Completed
                        </button>
                    </form>
                @else
                    <p class="text-success">You have already completed this lesson!</p>
                @endif
            </div>
        @endif
    </div><!-- /lesson-wrapper -->
@endsection


@section('scripts')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/owl.theme.default.min.css') }}"></script>
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
    </script>
@endsection
