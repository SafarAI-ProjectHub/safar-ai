@extends('layouts_dashboard.main')

@section('styles')
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="author" content="TechyDevs">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- <title>Safar AI</title> --}}

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10/dist/sweetalert2.min.css">
    {{-- <!-- Favicon -->
    <link rel="icon" sizes="16x16" href="{{ asset('assets/images/logo-icon.png') }}"> --}}

    <!-- inject:css -->
    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/line-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fancybox.css') }}">
    <link rel="stylesheet" href="{{ asset('css/animated-headline.css') }}">
    <link rel="stylesheet" href="{{ asset('css/plyr.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/jquery-te-1.4.0.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <style>
        .lecture-viewer-text-wrap .active {
            display: block;
        }
    </style>
@endsection

@section('content')


    <div class="card g-3 mt-5">
        <div class="card-body row g-3">
            <h4 class="pt-3 mb-0">
                <span class="text-muted fw-light">Courses /</span> {{ $course->title }} <span
                    class="{{ $course->completed ? 'badge bg-success' : 'badge bg-warning text-dark' }} rounded-pill">{{ $course->completed ? 'Completed' : 'In Progress' }}</span>

            </h4>
            <section class="course-dashboard">

                <div class="course-dashboard-wrap">
                    <div class="course-dashboard-container d-flex">
                        <div class="course-dashboard-column">

                            <div class="lecture-viewer-container" style="min-height:50vh;">
                                @if ($course->units->count() > 0)
                                    @foreach ($course->units as $unit)
                                        @php
                                            $contentDivId = 'unitContent' . $loop->iteration; // Unique ID for content div
                                        @endphp

                                        @if ($unit->content_type === 'video')
                                            <div id="{{ $contentDivId }}" class="collapse lecture-video-item"
                                                aria-labelledby="heading{{ $loop->iteration }}"
                                                data-parent="#accordionCourseExample">
                                                <video controls crossorigin playsinline id="player{{ $loop->iteration }}">
                                                    <!-- Video files -->
                                                    <source src="{{ asset($unit->content) }}" type="video/mp4" />
                                                    <!-- Additional sources can be specified here -->

                                                    <!-- Caption files -->
                                                    <track kind="captions" label="English" srclang="en" src="#"
                                                        default />
                                                    <track kind="captions" label="Français" srclang="fr" src="#" />

                                                    <!-- Fallback for browsers that don't support the <video> element -->
                                                    <a href="{{ asset($unit->content) }}" download>Download</a>
                                                </video>
                                            </div>
                                        @else
                                            <div id="{{ $contentDivId }}"
                                                class="lecture-viewer-text-wrap {{ $loop->first ? 'active' : '' }} "
                                                aria-labelledby="heading{{ $loop->iteration }}"
                                                data-parent="#accordionCourseExample">
                                                <div class="lecture-viewer-text-content custom-scrollbar-styled">
                                                    <div class="lecture-viewer-text-body">
                                                        {!! $unit->content !!} <!-- Dynamically loading text content -->
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    <div class="lecture-viewer-container">
                                        <div class="lecture-video-item">
                                            <h3 class="fs-24 font-weight-semi-bold">No
                                                content available Yet</h3>
                                        </div>
                                    </div>
                                @endif
                            </div><!-- end lecture-viewer-container -->

                            <div class="lecture-video-detail">
                                <div class="lecture-tab-body bg-gray p-4">
                                    <ul class="nav nav-tabs generic-tab" id="myTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link" id="search-tab" data-toggle="tab" href="#search"
                                                role="tab" aria-controls="search" aria-selected="false">
                                                <i class="la la-search"></i>
                                            </a>
                                        </li>
                                        <li class="nav-item mobile-menu-nav-item">
                                            <a class="nav-link" id="course-content-tab" data-toggle="tab"
                                                href="#course-content" role="tab" aria-controls="course-content"
                                                aria-selected="false">
                                                Course Content
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview"
                                                role="tab" aria-controls="overview" aria-selected="true">
                                                Overview
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="rating-tab" data-toggle="tab" href="#rating"
                                                role="tab" aria-controls="rating" aria-selected="false">
                                                <i class="la la-star me-1"></i> Leave a Rating
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="reviews-tab" data-toggle="tab" href="#reviews"
                                                role="tab" aria-controls="reviews" aria-selected="false">
                                                Reviews
                                            </a>
                                        </li>

                                        {{-- <li class="nav-item">
                                                            <a class="nav-link" id="question-and-ans-tab" data-toggle="tab"
                                                                href="#question-and-ans" role="tab" aria-controls="question-and-ans"
                                                                aria-selected="false">
                                                                Question & Ans
                                                            </a>
                                                        </li> --}}
                                        {{-- <li class="nav-item">
                                                            <a class="nav-link" id="announcements-tab" data-toggle="tab"
                                                                href="#announcements" role="tab" aria-controls="announcements"
                                                                aria-selected="false">
                                                                Announcements
                                                            </a>
                                                        </li> --}}
                                    </ul>
                                </div>
                                <div class="lecture-video-detail-body">
                                    <div class="tab-content" id="myTabContent">
                                        <div class="tab-pane fade" id="search" role="tabpanel"
                                            aria-labelledby="search-tab">
                                            <div class="search-course-wrap pt-40px">
                                                <form action="#" class="pb-5">
                                                    <div class="input-group">
                                                        <input class="form-control form--control form--control-gray pl-3"
                                                            type="text" name="search"
                                                            placeholder="Search course content">
                                                        <div class="input-group-append">
                                                            <button class="btn theme-btn"><span
                                                                    class="la la-search"></span></button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <div class="search-results-message text-center">
                                                    <h3 class="fs-24 font-weight-semi-bold pb-1">Start a new search</h3>
                                                    <p>To find captions, lectures or resources</p>
                                                </div>
                                            </div><!-- end search-course-wrap -->
                                        </div><!-- end tab-pane -->
                                        <!-- Mobile Course Content -->
                                        <div class="tab-pane fade" id="course-content" role="tabpanel"
                                            aria-labelledby="course-content-tab">
                                            <div class="mobile-course-menu pt-4">
                                                <div class="accordion generic-accordion generic--accordion"
                                                    id="mobileCourseAccordionCourseExample">
                                                    <div class="card">
                                                        <div class="card-header" id="mobileCourseHeadingOne">
                                                            <button class="btn btn-link" type="button"
                                                                data-toggle="collapse"
                                                                data-target="#mobileCourseCollapseOne"
                                                                aria-expanded="true"
                                                                aria-controls="mobileCourseCollapseOne">
                                                                <i class="la la-angle-down"></i>
                                                                <i class="la la-angle-up"></i>
                                                                <span class="fs-15">{{ $course->title }} :
                                                                    {{-- {{ $course->description }}</span> --}}
                                                                    <span class="course-duration">
                                                                        <span>{{ $completedUnitCount }} /
                                                                            {{ $course->units->count() }}</span>
                                                                        {{-- <span>21min</span> --}}
                                                                    </span>
                                                            </button>
                                                        </div><!-- end card-header -->
                                                        <div id="mobileCourseCollapseOne" class="collapse show"
                                                            aria-labelledby="mobileCourseHeadingOne"
                                                            data-parent="#mobileCourseAccordionCourseExample">
                                                            <div class="card-body p-0">
                                                                <ul class="curriculum-sidebar-list">
                                                                    @foreach ($course->units as $unit)
                                                                        <li class="course-item-link   {{ $unit->content_type == 'video' ? '' : 'active-resource' }}  {{ $loop->first ? 'active' : '' }}"
                                                                            @if ($unit->content_type == 'video') onclick="updateContent('{{ $unit->content_type }}', `{{ asset($unit->content) }}`, '{{ $unit->title }}')">
                                                                                        @else
                                                                                            onclick="updateContent('{{ $unit->content_type }}', `{{ $unit->content }}`, '{{ $unit->title }}')"> @endif
                                                                            <div class="course-item-content-wrap">
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="courseCheckbox{{ $unit->id }}"
                                                                                    value="{{ $unit->id }}"
                                                                                    {{ in_array($unit->id, $completedUnitIds) ? 'checked' : '' }}>
                                                                                <label
                                                                                    class="custom-control-label custom--control-label"
                                                                                    for="courseCheckbox{{ $unit->id }}"></label>
                                                                            </div>
                                                                            <div class="course-item-content">
                                                                                <h4 class="fs-15">{{ $unit->title }}
                                                                                </h4>
                                                                                <div class="courser-item-meta-wrap">
                                                                                    <p class="course-item-meta">
                                                                                        <i
                                                                                            class="la la-{{ $unit->content_type === 'video' ? 'play-circle' : 'file' }}"></i>
                                                                                        {{ $unit->content_type === 'video' ? 'video' : 'text' }}
                                                                                    </p>
                                                                                </div>
                                                                            </div>

                                                            </div>
                                                            </li>
                                                            @endforeach
                                                            </ul>
                                                        </div><!-- end card-body -->
                                                    </div><!-- end collapse -->
                                                </div><!-- end card -->
                                            </div><!-- end accordion-->
                                        </div><!-- end mobile-course-menu -->
                                    </div><!-- end tab-pane -->

                                    <div class="tab-pane fade show active" id="overview" role="tabpanel"
                                        aria-labelledby="overview-tab">
                                        <div class="lecture-overview-wrap">
                                            <div class="lecture-overview-item">
                                                <h3 class="fs-24 font-weight-semi-bold pb-2">About this course</h3>
                                                <p>{{ $course->description }}</p>
                                            </div><!-- end lecture-overview-item -->
                                            <div class="section-block"></div>
                                            <div class="lecture-overview-item">
                                                <div class="lecture-overview-stats-wrap d-flex flex-wrap">
                                                    <div class="lecture-overview-stats-item">
                                                        <h3 class="fs-16 font-weight-semi-bold pb-2">By the numbers</h3>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                    <div class="lecture-overview-stats-item">
                                                        <ul class="generic-list-item">
                                                            <li><span>Skill level:</span>Level {{ $course->level }} </li>
                                                            <li><span>Age:</span>{{ $course->category->age_group }} years
                                                                old
                                                            </li>

                                                            <li><span>Students:</span>{{ $numberstd }}</li>
                                                        </ul>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                    <div class="lecture-overview-stats-item">
                                                        <ul class="generic-list-item">
                                                            <li><span>Lectures:</span>{{ $unitnumber }}</li>
                                                            <li><span>Certificate:</span>Yes</li>
                                                        </ul>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                </div><!-- end lecture-overview-stats-wrap -->
                                            </div><!-- end lecture-overview-item -->
                                            <div class="section-block"></div>
                                            <div class="lecture-overview-item">
                                                <div class="lecture-overview-stats-wrap d-flex">
                                                    <div class="lecture-overview-stats-item">
                                                        <h3 class="fs-16 font-weight-semi-bold pb-2">Certificates</h3>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                    <div
                                                        class="lecture-overview-stats-item lecture-overview-stats-wide-item">
                                                        <p class="pb-3">Get Safar AI certificate by completing entire
                                                            course
                                                        </p>
                                                        <a class="btn theme-btn theme-btn-small theme-btn-transparent bg-primary text-white"
                                                            id="certificate-button">Safar
                                                            AI
                                                            Certificate</a>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                </div><!-- end lecture-overview-stats-wrap -->
                                            </div><!-- end lecture-overview-item -->
                                            <div class="section-block"></div>
                                            <div class="lecture-overview-item">
                                                <div class="lecture-overview-stats-wrap d-flex">
                                                    <div class="lecture-overview-stats-item">
                                                        <h3 class="fs-16 font-weight-semi-bold pb-2">Features</h3>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                    <div class="lecture-overview-stats-item">
                                                        <p>Available on <a href="#"
                                                                class="text-color hover-underline">Safar AI</a>
                                                        </p>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                </div><!-- end lecture-overview-stats-wrap -->
                                            </div><!-- end lecture-overview-item -->
                                            <div class="section-block"></div>
                                            {{-- <div class="lecture-overview-item">
                                                                <div class="lecture-overview-stats-wrap d-flex">
                                                                    <div class="lecture-overview-stats-item">
                                                                        <h3 class="fs-16 font-weight-semi-bold pb-2">Description</h3>
                                                                    </div><!-- end lecture-overview-stats-item -->
                                                                    <div
                                                                        class="lecture-overview-stats-item lecture-overview-stats-wide-item lecture-description">
                                                                        <h3 class="fs-16 font-weight-semi-bold pb-2">From the Author of the
                                                                            Best Selling After Effects CC 2020 Complete Course</h3>
                                                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque
                                                                            ea esse inventore odit voluptate. Accusantium asperiores at
                                                                            doloremque ex impedit ipsa itaque. Consequuntur dignissimos
                                                                            itaque mollitia tenetur voluptatem. Aut beatae blanditiis
                                                                            consequuntur corporis delectus deserunt dolores eos et fugit hic
                                                                            illo, impedit in magnam mollitia nulla placeat porro, possimus
                                                                            quae quaerat quasi quidem recusandae reprehenderit saepe,
                                                                            suscipit voluptatibus.</p>
                                                                        <div class="collapse" id="collapseMore">
                                                                            <p>Aliquid odit perspiciatis quidem rerum? Architecto aspernatur
                                                                                beatae consectetur illo labore molestias natus, provident
                                                                                quas tempore ut vitae voluptate! Dicta doloremque dolores
                                                                                doloribus dolorum ea enim est ex harum mollitia natus
                                                                                necessitatibus, non officiis repellat rerum sed sint vel
                                                                                voluptatem. Aspernatur enim error esse facere numquam quidem
                                                                                reiciendis sit soluta suscipit voluptates.</p>
                                                                            <ul class="generic-list-item generic-list-item-bullet">
                                                                                <li>Lorem ipsum dolor sit amet, consectetur adipisicing
                                                                                    elit.</li>
                                                                                <li>Cumque ea esse inventore odit voluptate.</li>
                                                                                <li>Phasellus posuere urna et faucibus</li>
                                                                                <li>Mauris faucibus ligula vestibulum </li>
                                                                                <li>Phasellus posuere urna </li>
                                                                            </ul>
                                                                            <p>impedit in magnam mollitia nulla placeat porro, possimus quae
                                                                                quaerat quasi quidem recusandae reprehenderit saepe,
                                                                                suscipit voluptatibus. Beatae, fugiat? Lorem ipsum dolor sit
                                                                                amet, consectetur adipisicing elit. Cumque ea esse inventore
                                                                                odit voluptate. Accusantium asperiores at doloremque ex
                                                                                impedit ipsa itaque. Consequuntur dignissimos itaque
                                                                                mollitia tenetur voluptatem. Aut beatae blanditiis
                                                                                consequuntur corporis delectus deserunt dolores eos et fugit
                                                                                hic illo, impedit in magnam mollitia nulla placeat porro,
                                                                                possimus quae quaerat quasi quidem recusandae reprehenderit
                                                                                saepe, suscipit voluptatibus. Beatae, fugiat?</p>
                                                                            <p>See you in the course.</p>
                                                                            <p>Alex</p>
                                                                            <h3 class="fs-16 font-weight-semi-bold pb-2">What you’ll learn
                                                                            </h3>
                                                                            <ul class="generic-list-item generic-list-item-bullet">
                                                                                <li>Lorem ipsum dolor sit amet, consectetur adipisicing
                                                                                    elit.</li>
                                                                                <li>Cumque ea esse inventore odit voluptate.</li>
                                                                                <li>Phasellus posuere urna et faucibus</li>
                                                                                <li>Mauris faucibus ligula vestibulum </li>
                                                                                <li>Phasellus posuere urna </li>
                                                                            </ul>
                                                                            <h3 class="fs-16 font-weight-semi-bold pb-2">Are there any
                                                                                course requirements or prerequisites?</h3>
                                                                            <ul class="generic-list-item generic-list-item-bullet">
                                                                                <li>Lorem ipsum dolor sit amet, consectetur adipisicing
                                                                                    elit.</li>
                                                                                <li>Cumque ea esse inventore odit voluptate.</li>
                                                                                <li>Phasellus posuere urna et faucibus</li>
                                                                                <li>Mauris faucibus ligula vestibulum </li>
                                                                            </ul>
                                                                            <h3 class="fs-16 font-weight-semi-bold pb-2">Who this course is
                                                                                for:</h3>
                                                                            <ul class="generic-list-item generic-list-item-bullet">
                                                                                <li>Consectetur adipisicing elit.</li>
                                                                                <li>Cumque ea esse inventore odit voluptate.</li>
                                                                                <li>Phasellus posuere urna et faucibus</li>
                                                                                <li>Phasellus posuere urna et faucibus</li>
                                                                                <li>Mauris faucibus ligula vestibulum</li>
                                                                                <li>Mauris faucibus ligula vestibulum </li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="show-more-btn-box">
                                                                            <a class="collapse-btn collapse--btn fs-15" data-toggle="collapse"
                                                                                href="#collapseMore" role="button" aria-expanded="false"
                                                                                aria-controls="collapseMore">
                                                                                <span class="collapse-btn-hide">Read more<i
                                                                                        class="la la-angle-down ml-1 fs-14"></i></span>
                                                                                <span class="collapse-btn-show">Read less<i
                                                                                        class="la la-angle-up ml-1 fs-14"></i></span>
                                                                            </a>
                                                                        </div><!-- end show-more-btn-box -->
                                                                    </div><!-- end lecture-overview-stats-item -->
                                                                </div><!-- end lecture-overview-stats-wrap -->
                                                            </div><!-- end lecture-overview-item --> --}}
                                            <div class="section-block"></div>
                                            <div class="lecture-overview-item">
                                                <div class="lecture-overview-stats-wrap d-flex flex-wrap">
                                                    <div class="lecture-overview-stats-item">
                                                        <h3 class="fs-16 font-weight-semi-bold pb-2">Instructor</h3>
                                                    </div><!-- end lecture-overview-stats-item -->
                                                    <div
                                                        class="lecture-overview-stats-item lecture-overview-stats-wide-item">
                                                        <div class="media media-card align-items-center">
                                                            <a href="teacher-detail.html"
                                                                class="media-img d-block rounded-full avatar-md">
                                                                <img src="{{ $course->teacher && $course->teacher->user->profile_image ? asset($course->teacher->user->profile_image) : asset('assets/images/avatars/profile-Img.png') }}"
                                                                    class="rounded-full user-img" alt="Instructor avatar">

                                                            </a>
                                                            <div class="media-body">
                                                                <h5><a href="{{ $course->teacher ? '#' : '#' }}">
                                                                        {{ $course->teacher && optional($course->teacher->user)->full_name ? $course->teacher->user->full_name : 'N/A' }}
                                                                    </a>
                                                                </h5>
                                                                <div>
                                                                    <span class="d-block lh-18 pt-2">Years of experience:
                                                                        {{ optional($course->teacher)->years_of_experience ?? 'N/A' }}
                                                                    </span>
                                                                </div>

                                                            </div>

                                                        </div><!-- end lecture-overview-stats-item -->
                                                    </div><!-- end lecture-overview-stats-wrap -->
                                                </div><!-- end lecture-overview-item -->
                                            </div><!-- end lecture-overview-wrap -->
                                        </div><!-- end tab-pane -->

                                    </div><!-- end tab-content -->

                                    <div class="tab-pane fade" id="rating" role="tabpanel"
                                        aria-labelledby="rating-tab">
                                        <div class="modal-header border-bottom-gray">
                                            <div class="pr-2">
                                                <h5 class="modal-title fs-19 font-weight-semi-bold lh-24"
                                                    id="ratingModalTitle">
                                                    How would you rate this course?
                                                </h5>
                                            </div>
                                        </div>
                                        <div class="modal-body text-center py-5">
                                            <form id="ratingForm">
                                                <div class="leave-rating mt-5">
                                                    <input type="radio" name="rate" id="star5"
                                                        value="5" />
                                                    <label for="star5" class="fs-45"></label>
                                                    <input type="radio" name="rate" id="star4"
                                                        value="4" />
                                                    <label for="star4" class="fs-45"></label>
                                                    <input type="radio" name="rate" id="star3"
                                                        value="3" />
                                                    <label for="star3" class="fs-45"></label>
                                                    <input type="radio" name="rate" id="star2"
                                                        value="2" />
                                                    <label for="star2" class="fs-45"></label>
                                                    <input type="radio" name="rate" id="star1" value="1"
                                                        checked />
                                                    <label for="star1" class="fs-45"></label>
                                                    <div class="rating-result-text fs-20 pb-4"></div>
                                                </div>
                                                <div class="mt-4">
                                                    <textarea class="form-control" id="comment" rows="4" placeholder="Leave a comment"></textarea>
                                                </div>
                                                <div class="mt-4">
                                                    <button type="submit" class="btn btn-primary">Submit</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade" id="reviews" role="tabpanel"
                                        aria-labelledby="reviews-tab">
                                        <div class="new-question-body pt-40px d-flex justify-content-between flex-wrap">
                                            <h3 class="fs-20 font-weight-semi-bold">Reviews ({{ $reviewsCount }})</h3>
                                            <h4>
                                                Average Rating:
                                                @if ($reviewsRate > 0)
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @if ($i <= floor($reviewsRate))
                                                            <i class='bx bxs-star' style='color:#ffcc00'></i>
                                                        @elseif ($i == ceil($reviewsRate) && $reviewsRate > floor($reviewsRate))
                                                            <i class='bx bxs-star-half' style='color:#ffcc00'></i>
                                                        @else
                                                            <i class='bx bx-star' style='color:#ffcc00'></i>
                                                        @endif
                                                    @endfor
                                                @else
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class='bx bx-star' style='color:#ffcc00'></i>
                                                    @endfor
                                                @endif

                                            </h4>

                                        </div>

                                        @if ($reviews->isEmpty())
                                            <p>No reviews yet.</p>
                                        @else
                                            <div class="replay-question-body pt-30px">
                                                @foreach ($reviews as $review)
                                                    <div class="question-list-item">
                                                        <div
                                                            class="media media-card border-bottom border-bottom-gray py-4">
                                                            <div class="media-img rounded-full flex-shrink-0 avatar-sm">
                                                                <img class="rounded-full"
                                                                    src="{{ asset($review->user->profile_image ? $review->user->profile_image : 'assets/images/avatars/profile-Img.png') }}"
                                                                    alt="User image">
                                                            </div>
                                                            <div class="media-body">
                                                                <div class="d-flex justify-content-between">
                                                                    <div class="question-meta-content">
                                                                        <a href="javascript:void(0)" class="d-block">
                                                                            <h5 class="fs-16 pb-1">{{ $review->comment }}
                                                                            </h5>
                                                                            <p class="meta-tags fs-13">
                                                                                <a
                                                                                    href="#">{{ $review->user->full_name }}</a>
                                                                                <span>
                                                                                    @php
                                                                                        $created_at =
                                                                                            $review->created_at;
                                                                                        $updated_at =
                                                                                            $review->updated_at;

                                                                                        if ($updated_at > $created_at) {
                                                                                            $created_at = $updated_at;
                                                                                        }
                                                                                        $now = \Carbon\Carbon::now();

                                                                                        $diffInSeconds = $created_at->diffInSeconds(
                                                                                            $now,
                                                                                        );
                                                                                        $diffInMinutes = $created_at->diffInMinutes(
                                                                                            $now,
                                                                                        );
                                                                                        $diffInHours = $created_at->diffInHours(
                                                                                            $now,
                                                                                        );
                                                                                        $diffInDays = $created_at->diffInDays(
                                                                                            $now,
                                                                                        );
                                                                                        $diffInWeeks = $created_at->diffInWeeks(
                                                                                            $now,
                                                                                        );
                                                                                        $diffInMonths = $created_at->diffInMonths(
                                                                                            $now,
                                                                                        );
                                                                                        $diffInYears = $created_at->diffInYears(
                                                                                            $now,
                                                                                        );

                                                                                        $timeDiff = '';

                                                                                        if ($diffInSeconds < 60) {
                                                                                            $timeDiff =
                                                                                                $diffInSeconds .
                                                                                                ' seconds ago';
                                                                                        } elseif ($diffInMinutes < 60) {
                                                                                            $timeDiff =
                                                                                                $diffInMinutes .
                                                                                                ' minutes ago';
                                                                                        } elseif ($diffInHours < 24) {
                                                                                            $timeDiff =
                                                                                                $diffInHours .
                                                                                                ' hours ago';
                                                                                        } elseif ($diffInDays < 7) {
                                                                                            $timeDiff =
                                                                                                $diffInDays .
                                                                                                ' days ago';
                                                                                        } elseif ($diffInWeeks < 4) {
                                                                                            $timeDiff =
                                                                                                $diffInWeeks .
                                                                                                ' weeks ago';
                                                                                        } elseif ($diffInMonths < 12) {
                                                                                            $timeDiff =
                                                                                                $diffInMonths .
                                                                                                ' months ago';
                                                                                        } else {
                                                                                            $timeDiff =
                                                                                                $diffInYears .
                                                                                                ' years ago';
                                                                                        }
                                                                                    @endphp
                                                                                    {{ $timeDiff }}
                                                                                </span>
                                                                            </p>
                                                                        </a>
                                                                    </div>
                                                                    <div class="question-upvote-action">
                                                                        <div
                                                                            class="number-upvotes pb-2 d-flex align-items-center">
                                                                            <span class="rating-stars">
                                                                                {{ str_repeat('★', $review->rate) . '' . str_repeat('☆', 5 - $review->rate) }}
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            </div>
                                            <div class="d-felx justify-content-center">

                                                {{ $reviews->links() }}

                                            </div>
                                        @endif
                                    </div>

                                </div>
                                <div class="section-block"></div>
                                <div class="section-block"></div>
                                <div class="section-block"></div><!-- end lecture-video-detail -->
                                {{-- <div class="cta-area py-4 bg-gray">
                                    <div class="container-fluid">
                                        <div class="row align-items-center">
                                            <div class="col-lg-6">
                                                <div class="cta-content-wrap">
                                                    <h3 class="fs-18 font-weight-semi-bold">Top companies choose <a
                                                            href="for-business.html"
                                                            class="text-color hover-underline">Safar
                                                            AI
                                                            for Business</a> to build in-demand career skills.</h3>
                                                </div>
                                            </div><!-- end col-lg-6 -->
                                            <div class="col-lg-6">
                                                <div class="client-logo-wrap text-right">
                                                    <a href="#"
                                                        class="client-logo-item client--logo-item-2 pr-3"><img
                                                            src="/images/sponsor-img.png" alt="brand image"></a>
                                                    <a href="#"
                                                        class="client-logo-item client--logo-item-2 pr-3"><img
                                                            src="/images/sponsor-img2.png" alt="brand image"></a>
                                                    <a href="#"
                                                        class="client-logo-item client--logo-item-2 pr-3"><img
                                                            src="/images/sponsor-img3.png" alt="brand image"></a>
                                                </div><!-- end client-logo-wrap -->
                                            </div><!-- end col-lg-6 -->
                                        </div><!-- end row -->
                                    </div><!-- end container-fluid -->
                                </div><!-- end cta-area --> --}}
                                <div class="footer-area pt-50px">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-lg-6 responsive-column-half">
                                                <div class="footer-item">
                                                    <a href="index.html">
                                                        <img src="{{ asset('assets/images/logo-img.png') }}"
                                                            alt="footer logo" class="footer__logo"
                                                            style="width:100%;height:100%;">
                                                    </a>
                                                    <ul class="generic-list-item pt-4">
                                                        <li><a href="tel:+1631237884">+163 123 7884</a></li>
                                                        <li><a href="mailto:support@wbsite.com">support@website.com</a>
                                                        </li>
                                                        <li>Melbourne, Australia, 105 South Park Avenue</li>
                                                    </ul>
                                                </div><!-- end footer-item -->
                                            </div><!-- end col-lg-3 -->
                                            <div class="col-lg-6 responsive-column-half">
                                                <div class="footer-item">
                                                    <h3 class="fs-20 font-weight-semi-bold pb-3">Company</h3>
                                                    <ul class="generic-list-item">
                                                        <li><a href="/#about">About us</a></li>
                                                        <li><a href="/#contact">Contact us</a></li>
                                                        <li><a href="/register-teacher">Become a Teacher</a></li>
                                                        <li><a href="/#team">Team</a></li>
                                                        {{-- <li><a href="#">FAQs</a></li>
                                                                <li><a href="#">Blog</a></li> --}}
                                                    </ul>
                                                </div><!-- end footer-item -->
                                            </div><!-- end col-lg-3 -->
                                            {{-- <div class="col-lg-3 responsive-column-half">
                                                        <div class="footer-item">
                                                            <h3 class="fs-20 font-weight-semi-bold pb-3">Courses</h3>
                                                            <ul class="generic-list-item">
                                                                <li><a href="#">Web Development</a></li>
                                                                <li><a href="#">Hacking</a></li>
                                                                <li><a href="#">PHP Learning</a></li>
                                                                <li><a href="#">Spoken English</a></li>
                                                                <li><a href="#">Self-Driving Car</a></li>
                                                                <li><a href="#">Garbage Collectors</a></li>
                                                            </ul>
                                                        </div><!-- end footer-item -->
                                                    </div><!-- end col-lg-3 -->
                                                    <div class="col-lg-3 responsive-column-half">
                                                        <div class="footer-item">
                                                            <h3 class="fs-20 font-weight-semi-bold pb-3">Download App</h3>
                                                            <div class="mobile-app">
                                                                <p class="pb-3 lh-24">Download our mobile app and learn on the go.</p>
                                                                <a href="#" class="d-block mb-2 hover-s"><img
                                                                        src="/images/appstore.png" alt="App store" class="img-fluid"></a>
                                                                <a href="#" class="d-block hover-s"><img src="/images/googleplay.png"
                                                                        alt="Google play store" class="img-fluid"></a>
                                                            </div>
                                                        </div><!-- end footer-item -->
                                                    </div><!-- end col-lg-3 --> --}}
                                        </div><!-- end row -->
                                    </div><!-- end container-fluid -->
                                    <div class="section-block"></div>
                                    <div class="copyright-content py-4">
                                        <div class="container-fluid">
                                            <div class="row align-items-center">
                                                <div class="col-lg-12 text-center">
                                                    <p class="copy-desc">&copy; 2021 Safar AI. All Rights Reserved.
                                                </div><!-- end col-lg-6 -->
                                                {{-- <div class="col-lg-6">
                                                            <div class="d-flex flex-wrap align-items-center justify-content-end">
                                                                <ul class="generic-list-item d-flex flex-wrap align-items-center fs-14">
                                                                    <li class="mr-3"><a href="terms-and-conditions.html">Terms &
                                                                            Conditions</a></li>
                                                                    <li class="mr-3"><a href="privacy-policy.html">Privacy Policy</a>
                                                                    </li>
                                                                </ul>
                                                                <div class="select-container select-container-sm">
                                                                    <select class="select-container-select">
                                                                        <option value="1">English</option>
                                                                        <option value="2">Deutsch</option>
                                                                        <option value="3">Español</option>
                                                                        <option value="4">Français</option>
                                                                        <option value="5">Bahasa Indonesia</option>
                                                                        <option value="6">Bangla</option>
                                                                        <option value="7">日本語</option>
                                                                        <option value="8">한국어</option>
                                                                        <option value="9">Nederlands</option>
                                                                        <option value="10">Polski</option>
                                                                        <option value="11">Português</option>
                                                                        <option value="12">Română</option>
                                                                        <option value="13">Русский</option>
                                                                        <option value="14">ภาษาไทย</option>
                                                                        <option value="15">Türkçe</option>
                                                                        <option value="16">中文(简体)</option>
                                                                        <option value="17">中文(繁體)</option>
                                                                        <option value="17">Hindi</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div><!-- end col-lg-6 --> --}}
                                            </div><!-- end row -->
                                        </div><!-- end container-fluid -->
                                    </div><!-- end copyright-content -->
                                </div><!-- end footer-area -->
                            </div><!-- end course-dashboard-column -->

                            <div class="course-dashboard-sidebar-column">
                                <button class="sidebar-open" type="button"><i class="la la-angle-left"></i> Course
                                    Content</button>
                                <div class="course-dashboard-sidebar-wrap custom-scrollbar-styled">
                                    <div
                                        class="course-dashboard-side-heading d-flex align-items-center justify-content-between">
                                        <h3 class="fs-18 font-weight-semi-bold">Course content</h3>
                                        <button class="sidebar-close" type="button"><i class="la la-times"></i></button>
                                    </div>
                                    <div class="course-dashboard-side-content">
                                        <div class="accordion generic-accordion generic--accordion"
                                            id="accordionCourseExample">
                                            <div class="card">
                                                <div class="card-header" id="headingOne">
                                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                                        data-target="#collapseOne" aria-expanded="true"
                                                        aria-controls="collapseOne">
                                                        <i class="la la-angle-down"></i><i class="la la-angle-up"></i>
                                                        <span class="fs-15">{{ $course->title }} :
                                                            {{-- {{ $course->description }}</span> --}}
                                                            <span class="course-duration"><span>{{ $completedUnitCount }}
                                                                    /
                                                                    {{ $course->units->count() }}</span>{{-- <span>21min</span> --}}</span>
                                                    </button>
                                                </div>
                                                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                                    data-parent="#accordionCourseExample">
                                                    <div class="card-body p-0">
                                                        <ul class="curriculum-sidebar-list">
                                                            @if ($course->units->count() > 0)
                                                                @foreach ($course->units as $unit)
                                                                    <li class="course-item-link  {{ $unit->content_type == 'video' ? '' : 'active-resource' }} {{ $loop->first ? 'active' : '' }}"
                                                                        @if ($unit->content_type == 'video') onclick="updateContent('{{ $unit->content_type }}', `{{ asset($unit->content) }}`, '{{ $unit->title }}')">
                                                                            @else
                                                                            onclick="updateContent('{{ $unit->content_type }}', `{{ $unit->content }}`, '{{ $unit->title }}')"> @endif
                                                                        <div class="course-item-content-wrap">
                                                                        {{-- if hasRole('Student') show the checkbox --}}
                                                                        @if (auth()->user()->hasRole('Student'))
                                                                            <div class="custom-control custom-checkbox">
                                                                                <input type="checkbox"
                                                                                    class="custom-control-input"
                                                                                    id="courseCheckbox{{ $unit->id }}"
                                                                                    value="{{ $unit->id }}"
                                                                                    {{ in_array($unit->id, $completedUnitIds) ? 'checked' : '' }}>
                                                                                <label
                                                                                    class="custom-control-label custom--control-label"
                                                                                    for="courseCheckbox{{ $unit->id }}"></label>
                                                                            </div>
                                                                        @endif

                                                                        <div class="course-item-content">
                                                                            <h4 class="fs-15">{{ $unit->title }}</h4>
                                                                            <div class="courser-item-meta-wrap">
                                                                                <p class="course-item-meta">
                                                                                    <i
                                                                                        class="la la-{{ $unit->content_type === 'video' ? 'play-circle' : 'file' }}"></i>
                                                                                    {{ $unit->content_type === 'video' ? 'video' : 'text' }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                    </div>
                                                    </li>
                                                    @endforeach
                                                @else
                                                    <li class="course-item-link">
                                                        <div class="course-item-content-wrap">
                                                            <div class="course-item-content">
                                                                <h4 class="fs-15">No content available Yet</h4>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    @endif
                                                    </ul>
                                                </div><!-- end card-body -->
                                            </div><!-- end collapse -->
                                        </div><!-- end card -->
                                    </div><!-- end accordion-->
                                </div><!-- end course-dashboard-side-content -->
                            </div><!-- end course-dashboard-sidebar-wrap -->
                        </div><!-- end course-dashboard-sidebar-column -->
                    </div><!-- end course-dashboard-container -->
                </div><!-- end course-dashboard-wrap -->
            </section>
            {{-- <div id="scroll-top">
            <i class="la la-arrow-up" title="Go top"></i>
        </div> --}}
            <!-- end scroll top -->

            <!-- Modal -->
            <div class="modal fade modal-container" id="ratingModal" tabindex="-1" role="dialog"
                aria-labelledby="ratingModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-gray">
                            <div class="pr-2">
                                <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="ratingModalTitle">
                                    How would you rate this course?
                                </h5>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="la la-times"></span>
                            </button>
                        </div><!-- end modal-header -->
                        <div class="modal-body text-center py-5">
                            <form id="ratingForm">
                                <div class="leave-rating mt-5">
                                    <input type="radio" name="rate" id="star5" value="5" />
                                    <label for="star5" class="fs-45"></label>
                                    <input type="radio" name="rate" id="star4" value="4" />
                                    <label for="star4" class="fs-45"></label>
                                    <input type="radio" name="rate" id="star3" value="3" />
                                    <label for="star3" class="fs-45"></label>
                                    <input type="radio" name="rate" id="star2" value="2" />
                                    <label for="star2" class="fs-45"></label>
                                    <input type="radio" name="rate" id="star1" value="1" checked />
                                    <label for="star1" class="fs-45"></label>
                                    <div class="rating-result-text fs-20 pb-4"></div>
                                </div><!-- end leave-rating -->
                                <div class="mt-4">
                                    <textarea class="form-control" id="comment" rows="4" placeholder="Leave a comment"></textarea>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div><!-- end modal-body -->
                    </div><!-- end modal-content -->
                </div><!-- end modal-dialog -->
            </div><!-- end modal -->

            <!-- Modal -->
            <div class="modal fade modal-container" id="shareModal" tabindex="-1" role="dialog"
                aria-labelledby="shareModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-gray">
                            <h5 class="modal-title fs-19 font-weight-semi-bold" id="shareModalTitle">Share this course
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="la la-times"></span>
                            </button>
                        </div><!-- end modal-header -->
                        <div class="modal-body">
                            <div class="copy-to-clipboard">
                                <span class="success-message">Copied!</span>
                                <div class="input-group">
                                    <input type="text" class="form-control form--control copy-input pl-3"
                                        value="https://www.aduca.com/share/101WxMB0oac1hVQQ==/">
                                    <div class="input-group-append">
                                        <button class="btn theme-btn theme-btn-sm copy-btn shadow-none"><i
                                                class="la la-copy mr-1"></i> Copy</button>
                                    </div>
                                </div>
                            </div><!-- end copy-to-clipboard -->
                        </div><!-- end modal-body -->
                        <div class="modal-footer justify-content-center border-top-gray">
                            <ul class="social-icons social-icons-styled">
                                <li><a href="#" class="facebook-bg"><i class="la la-facebook"></i></a></li>
                                <li><a href="#" class="twitter-bg"><i class="la la-twitter"></i></a></li>
                                <li><a href="#" class="instagram-bg"><i class="la la-instagram"></i></a></li>
                            </ul>
                        </div><!-- end modal-footer -->
                    </div><!-- end modal-content-->
                </div><!-- end modal-dialog -->
            </div><!-- end modal -->

            <!-- Modal -->
            <div class="modal fade modal-container" id="reportModal" tabindex="-1" role="dialog"
                aria-labelledby="reportModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-gray">
                            <div class="pr-2">
                                <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="reportModalTitle">Report
                                    Abuse
                                </h5>
                                <p class="pt-1 fs-14 lh-24">Flagged content is reviewed by Safar AI staff to determine
                                    whether
                                    it
                                    violates Terms of Service or Community Guidelines. If you have a question or technical
                                    issue, please contact our
                                    <a href="contact.html" class="text-color hover-underline">Support team here</a>.
                                </p>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="la la-times"></span>
                            </button>
                        </div><!-- end modal-header -->
                        <div class="modal-body">
                            <form method="post">
                                <div class="input-box">
                                    <label class="label-text">Select Report Type</label>
                                    <div class="form-group">
                                        <div class="select-container w-auto">
                                            <select class="select-container-select">
                                                <option value>-- Select One --</option>
                                                <option value="1">Inappropriate Course Content</option>
                                                <option value="2">Inappropriate Behavior</option>
                                                <option value="3">Safar AI Policy Violation</option>
                                                <option value="4">Spammy Content</option>
                                                <option value="5">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-box">
                                    <label class="label-text">Write Message</label>
                                    <div class="form-group">
                                        <textarea class="form-control form--control pl-3" name="message" placeholder="Provide additional details here..."
                                            rows="5"></textarea>
                                    </div>
                                </div>
                                <div class="btn-box text-right pt-2">
                                    <button type="button" class="btn font-weight-medium mr-3"
                                        data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn theme-btn theme-btn-sm lh-30">Submit <i
                                            class="la la-arrow-right icon ml-1"></i></button>
                                </div>
                            </form>
                        </div><!-- end modal-body -->
                    </div><!-- end modal-content -->
                </div><!-- end modal-dialog -->
            </div><!-- end modal -->

            <!-- Modal -->
            <div class="modal fade modal-container" id="insertLinkModal" tabindex="-1" role="dialog"
                aria-labelledby="insertLinkModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-gray">
                            <div class="pr-2">
                                <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="insertLinkModalTitle">Insert
                                    Link</h5>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="la la-times"></span>
                            </button>
                        </div><!-- end modal-header -->
                        <div class="modal-body">
                            <form action="#">
                                <div class="input-box">
                                    <label class="label-text">URL</label>
                                    <div class="form-group">
                                        <input class="form-control form--control" type="text" name="text"
                                            placeholder="Url">
                                        <i class="la la-link input-icon"></i>
                                    </div>
                                </div>
                                <div class="input-box">
                                    <label class="label-text">Text</label>
                                    <div class="form-group">
                                        <input class="form-control form--control" type="text" name="text"
                                            placeholder="Text">
                                        <i class="la la-pencil input-icon"></i>
                                    </div>
                                </div>
                                <div class="btn-box text-right pt-2">
                                    <button type="button" class="btn font-weight-medium mr-3"
                                        data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn theme-btn theme-btn-sm lh-30">Insert <i
                                            class="la la-arrow-right icon ml-1"></i></button>
                                </div>
                            </form>
                        </div><!-- end modal-body -->
                    </div><!-- end modal-content -->
                </div><!-- end modal-dialog -->
            </div><!-- end modal -->

            <!-- Modal -->
            <div class="modal fade modal-container" id="uploadPhotoModal" tabindex="-1" role="dialog"
                aria-labelledby="uploadPhotoModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-bottom-gray">
                            <div class="pr-2">
                                <h5 class="modal-title fs-19 font-weight-semi-bold lh-24" id="uploadPhotoModalTitle">
                                    Upload
                                    an Image</h5>
                            </div>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="la la-times"></span>
                            </button>
                        </div><!-- end modal-header -->
                        <div class="modal-body">
                            <div class="file-upload-wrap">
                                <input type="file" name="files[]" class="multi file-upload-input" multiple>
                                <span class="file-upload-text"><i class="la la-upload mr-2"></i>Drop files here or click
                                    to
                                    upload</span>
                            </div><!-- file-upload-wrap -->
                            <div class="btn-box text-right pt-2">
                                <button type="button" class="btn font-weight-medium mr-3"
                                    data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn theme-btn theme-btn-sm lh-30">Submit <i
                                        class="la la-arrow-right icon ml-1"></i></button>
                            </div>
                        </div><!-- end modal-body -->
                    </div><!-- end modal-content -->
                </div><!-- end modal-dialog -->
            </div>
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
    <script src="{{ asset('js/plyr.js') }}"></script>
    <script src="{{ asset('js/datedropper.min.js') }}"></script>
    <script src="{{ asset('js/emojionearea.min.js') }}"></script>
    <script src="{{ asset('js/jquery.MultiFile.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    {{-- swwet alet cdn and style --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        var player = new Plyr('#player');
    </script>
    @php
        $firstUnit = $course->units->first();
    @endphp
    <script>
        @if ($course->units->count() > 0 && $firstUnit != null)
            // updateContent('{{ $firstUnit->content_type }}', `{!! $firstUnit->content !!}`, '{{ $firstUnit->title }}');
            const viewerContainer = document.querySelector('.lecture-viewer-container');

            // Clear existing content
            viewerContainer.innerHTML = '';
            contentType = `{{ $firstUnit->content_type }}`;
            // Generate and insert appropriate content based on type
            if (contentType === 'video') {
                viewerContainer.innerHTML = `
                    <div class="lecture-video-item">
                    <video controls crossorigin playsinline id="player">
                    <source src="/{!! $firstUnit->content !!}" type="video/mp4">
                    Your browser does not support the video tag.
                    </video>
                    </div>
                    `;
                // Reinitialize Plyr
                new Plyr('#player');
            } else { // Assuming 'text' type content
                viewerContainer.innerHTML = `
                <div class="lecture-viewer-text-wrap active">
                <div class="lecture-viewer-text-content custom-scrollbar-styled">
                <div class="lecture-viewer-text-body">
                    {!! $firstUnit->content !!}
                </div>
                </div>
                </div>
                `;
            }
        @endif

        function updateContent(contentType, content, title) {
            const viewerContainer = document.querySelector('.lecture-viewer-container');

            // Clear existing content
            viewerContainer.innerHTML = '';

            // Generate and insert appropriate content based on type
            if (contentType === 'video') {
                viewerContainer.innerHTML = `
    <div class="lecture-video-item">
        <video controls crossorigin playsinline id="player">
            <source src="${content}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    `;
                // Reinitialize Plyr
                new Plyr('#player');
            } else { // Assuming 'text' type content
                viewerContainer.innerHTML = `
    <div class="lecture-viewer-text-wrap active">
        <div class="lecture-viewer-text-content custom-scrollbar-styled">
            <div class="lecture-viewer-text-body">
                ${content}
            </div>
        </div>
    </div>
    `;
            }
        }

        $(document).ready(function() {
            $('span.la.la-times').click(function() {
                $('.modal').modal('hide');
            });

            function toggleWrapper() {
                if ($(window).width() < 1024) {
                    $(".wrapper").removeClass("toggled");
                } else {
                    $(".wrapper").addClass("toggled");
                    if ($(".wrapper").hasClass("toggled")) {
                        $(".sidebar-wrapper").hover(function() {
                            $(".wrapper").addClass("sidebar-hovered");
                        }, function() {
                            $(".wrapper").removeClass("sidebar-hovered");
                        });
                    } else {
                        $(".sidebar-wrapper").unbind("hover");
                    }
                }
            } // Default state: closed $(".wrapper").removeClass("toggled");
            toggleWrapper
                (); // Reapply the toggle on window resize $(window).resize(function() { toggleWrapper(); });
            $('.course-dashboard-sidebar-column, .course-dashboard-column, .sidebar-open').addClass('active');
        });
        $(document).ready(function() {
            $('.custom-control-input').change(function() {
                var unitId = $(this).val();
                var
                    completed = $(this).is(':checked') ? 1 : 0;
                $.ajax({
                    url: '{{ route('course.updateUnitCompletion') }}',
                    method: 'POST',
                    data: {
                        unit_id: unitId,
                        completed: completed,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Unit completion status updated.');
                        }
                    }
                });
            });
        });
        $(document).ready(function() {
            @if (Auth::user()->hasRole('Student'))
                $('#certificate-button').click(function(e) {
                    e.preventDefault();
                    $.ajax({
                        url: '{{ route('certificate.check') }}',
                        type: 'GET',
                        data: {
                            course_id: '{{ $course->id }}',
                            user_id: '{{ auth()->user()->id }}',
                        },
                        success: function(response) {
                            if (response.allow_Certificate) {
                                window.location.href =
                                    '{{ route('certificate.review', $course->id) }}';
                            } else {
                                if (response.completed) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Oops...',
                                        text: 'You have to complete the course first!',
                                        confirmButtonText: 'OK',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Oops...',
                                        text: 'The course is not yet completed! ,the course still has more lessons will be available soon',
                                        confirmButtonText: 'OK'
                                    });
                                }
                            }
                        },
                        error: function() {
                            Swal.fire('An error occurred. Please try again.');
                        }
                    });
                });
            @else
                $('#certificate-button').click(function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'You have to be a student to get the certificate!',
                        confirmButtonText: 'OK',
                    });
                });
            @endif
        });
        $(document).ready(function() {
            $("#ratingForm").submit(function(event) {
                event.preventDefault();
                @if (!Auth::user()->hasRole('Student'))
                    Swal.fire({
                        icon: 'warning',
                        title: 'Oops...',
                        text: 'You have to be a student to rate the course!',
                        confirmButtonText: 'OK',
                    });
                    return;
                @endif
                var rating = $("input[name='rate']:checked").val();
                var comment = $("#comment").val();
                var course_id = '{{ $course->id }}';

                $.ajax({
                    url: '{{ route('course.rate') }}',
                    type: 'POST',
                    data: {
                        rating: rating,
                        comment: comment,
                        course_id: course_id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        // empty form fields
                        $('#ratingForm').trigger('reset');

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Rating submitted successfully',
                                confirmButtonText: 'OK',
                            });
                            $('#ratingModal').modal('hide');
                        }
                    },
                    error: function() {
                        Swal.fire('An error occurred. Please try again.');

                    }
                });
            });
        });

        $(document).ready(function() {
            // Check if the URL contains ?page=
            if (window.location.search.indexOf('page=') > -1) {
                // Activate the reviews tab
                $('#reviews-tab').tab('show');
            }

            // Handle pagination links click
            $(document).on('click', '.pagination a', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');
                getReviews(url);
                window.history.pushState("", "", url);
            });

            function getReviews(url) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    success: function(data) {
                        $('#reviews').html($(data).find('#reviews').html());
                    }
                });
            }
        });
    </script>
@endsection
