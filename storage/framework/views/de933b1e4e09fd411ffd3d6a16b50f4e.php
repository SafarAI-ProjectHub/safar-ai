<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/line-awesome.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/owl.carousel.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/owl.theme.default.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/bootstrap-select.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/fancybox.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/tooltipster.bundle.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('css/style.css')); ?>">

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
            background: linear-gradient(135deg, #a0007d, #ed68f8b8, #844dcd14);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
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
            text-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }
        .my-course-progress-bar-wrap {
            margin-top: auto;
            margin-bottom: 1rem;
            }
            .skillbar-box {
                flex: 1;
                margin-right: 10px;
                background: #f2f2f2;
                border-radius: 4px;
                overflow: hidden;
            }
            .skillbar {
                width: 100%;
                height: 8px;
                background: #e9e9e9;
                border-radius: 4px;
                position: relative;
            }
            .skillbar-bar {
                height: 100%;
                transition: width 0.4s ease-in-out;
            }
            .bg-primary {
                background-color: #be09cd !important;
            }
            .skill-bar-percent {
                font-size: 0.9rem;
                color: #666;
            }
                .review-stars span {
            color: #FFD700;
            font-size: 1.2rem;
            margin-right: 2px;
        }
        .review-stars {
            direction: ltr; 
            margin-bottom: 0.5rem;
        }

        .lesson-card {
            position: relative;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            min-height: 280px; 
            display: flex;
            flex-direction: column;
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
            color: #fff;
        }

        .lesson-card h5.card-title {
            color:#000;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        .lesson-card p {
            color : #000;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .lesson-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 30px rgba(190, 9, 205, 0.3);
            margin-bottom: auto; 

        }

        /* زر العرض */
        .lesson-card .btn-primary {
            background-color: #be09cd;
            border: none;
            margin-top: auto; /* يجعل الزر دائمًا بأسفل البطاقة */
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }

        .lesson-card .btn-primary:hover {
            background-color: #a0007d;
        }

        /* نص محتوى الدرس (lesson_details) مع تنسيق أفضل */
        .lesson-content {
            background: linear-gradient(to right, #e1e1e1, #f7f7f7);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }

        .lesson-content p {
            color: #333;
            line-height: 1.8;
        }

        /* يفضل التأكد من ملائمة الفيديو لنمط الـGlassmorphism */
        .lesson-content video, 
        .lesson-content iframe {
            border: 3px solid #be09cd;
            border-radius: 5px;
            max-height: 500px;
            width: 100%;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">

    <?php if($stage === 'blocks'): ?>
        <div class="row text-center mb-4">
            <h1>My Blocks</h1>
        </div>
        <div class="row d-flex justify-content-center">
            <?php $__empty_1 = true; $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-md-3 mb-4 d-flex justify-content-center">
                    <div class="block-circle">
                        <a href="<?php echo e(route('student.myCourses', ['block_id' => $block->id])); ?>">
                            <?php echo e($block->name); ?>

                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12 text-center">
                    <h3>You have no blocks.</h3>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($stage === 'units'): ?>
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1>Units in This Block</h1>
            </div>
            <?php if($courses->isEmpty()): ?>
                <div class="col-md-12 text-center">
                    <h3>You have not enrolled in any units yet.</h3>
                </div>
            <?php else: ?>
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-4 responsive-column-half mb-4">
                        <div class="card card-item">
                            <div class="card-image">
                                <a href="<?php echo e(route('student.myCourses', ['unit_id' => $course->id])); ?>" class="d-block">
                                    <img class="card-img-top lazy" src="<?php echo e($course->image); ?>" alt="Card image cap" style="width:100%; min-height:212px; object-fit:cover;">
                                    <div class="play-button">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                                             viewBox="-307.4 338.8 91.8 91.8" xml:space="preserve">
                                            <style type="text/css">
                                                .st0 {
                                                    opacity: 0.6;
                                                    fill: #000000;
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
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <a href="<?php echo e(route('student.myCourses', ['unit_id' => $course->id])); ?>">
                                        <?php echo e($course->title); ?>

                                    </a>
                                </h5>
                                <p class="card-text description lh-22 pt-2">
                                    <?php if(strlen($course->description) > 100): ?>
                                        <?php echo e(substr($course->description, 0, 100)); ?>...
                                    <?php else: ?>
                                        <?php echo e($course->description); ?>

                                    <?php endif; ?>
                                </p>
                                <p class="card-text lh-22 pt-2">
                                    <?php echo e($course->teacher ? $course->teacher->user->full_name : 'No teacher assigned yet'); ?>

                                </p>

                                
                                <div class="my-course-progress-bar-wrap justify-content-between d-flex flex-nowrap align-items-center mt-3 position-relative w-100">
                                    <div class="skillbar-box">
                                        <div class="skillbar skillbar-skillbar-2" data-percent="<?php echo e($course->progress); ?>%">
                                            <div class="skillbar-bar skillbar--bar-2 bg-primary" style="width: <?php echo e($course->progress); ?>%;"></div>
                                        </div>
                                    </div>
                                    <div class="skill-bar-percent text-nowrap">
                                        <?php echo e($course->progress); ?>%
                                    </div>
                                </div>

                                <?php
                                    $rating = $course->RateAvg();
                                    $fullStars = floor($rating);
                                    $halfStar = $rating - $fullStars >= 0.5;
                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                ?>
                                <div class="review-stars pt-3">
                                    <?php for($i = 0; $i < $fullStars; $i++): ?>
                                        <span class="la la-star"></span>
                                    <?php endfor; ?>
                                    <?php if($halfStar): ?>
                                        <span class="la la-star-half-o"></span>
                                    <?php endif; ?>
                                    <?php for($i = 0; $i < $emptyStars; $i++): ?>
                                        <span class="la la-star-o"></span>
                                    <?php endfor; ?>
                                </div>

                                <a href="<?php echo e(route('student.myCourses', ['unit_id' => $course->id])); ?>"
                                   class="btn btn-primary mt-auto d-block">
                                   View Lessons
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($stage === 'lessons'): ?>
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1>Lessons in This Unit</h1>
            </div>
            <?php if($lessons->isEmpty()): ?>
                <div class="col-md-12 text-center">
                    <h3>No Lessons found.</h3>
                </div>
            <?php else: ?>
                <?php $__currentLoopData = $lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-4">
                        <div class="card lesson-card">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <?php echo e($lesson->title ?? 'Lesson Title'); ?>

                                </h5>
                                <p><?php echo $lesson->subtitle; ?></p>
                                <a href="<?php echo e(route('student.myCourses', ['lesson_id' => $lesson->id])); ?>"
                                   class="btn btn-primary d-block">
                                   View Lesson
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if($stage === 'lesson_details'): ?>
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <h1><?php echo e($lesson->title ?? 'Lesson Title'); ?></h1>
            </div>
            <div class="col-12 lesson-content">
                <?php if($lesson->content_type === 'text'): ?>
                    <p><?php echo $lesson->content; ?></p>
                <?php elseif($lesson->content_type === 'video'): ?>
                    <video controls>
                        <source src="<?php echo e(asset($lesson->content)); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php elseif($lesson->content_type === 'youtube'): ?>
                    <div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden;">
                        <iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                src="https://www.youtube.com/embed/<?php echo e($lesson->content); ?>"
                                frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                <?php else: ?>
                    <p><?php echo nl2br(e($lesson->content ?? 'No content')); ?></p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/bootstrap-select.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/owl.carousel.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/isotope.js')); ?>"></script>
    <script src="<?php echo e(asset('js/waypoint.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.counterup.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/fancybox.js')); ?>"></script>
    <script src="<?php echo e(asset('js/datedropper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/emojionearea.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/tooltipster.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/jquery.lazy.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/main.js')); ?>"></script>

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/student/myCourses.blade.php ENDPATH**/ ?>