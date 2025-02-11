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
        .card.card-item.youtube {
            height: 315px;
        }
        .card-body.d-flex.flex-column.youtube {
            padding: 10px 20px 0;
        }
        
    </style>
    <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $isSUB = true;
?>

<?php if(Auth::user()->status == 'pending'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Your account is pending',
                text: 'You need to take the level test to start your learning journey.',
                icon: 'warning',
                confirmButtonText: 'Take the level test'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?php echo e(route('student.dashboard')); ?>';
                }
            });
        });
    </script>
<?php elseif(Auth::user()->student->subscription_status == 'subscribed' && isset($subscription->status) && $subscription->status == 'active'): ?>
    <?php if($courses->isEmpty()): ?>
        <div class="container my-5">
            <div class="row text-center mb-4">
                <h1>Free Videos</h1>
            </div>
            <?php if($videos->isEmpty()): ?>
                <div class="row">
                    <div class="col-12 text-center">
                        <p>No videos available</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4 mb-4">
                        <div class="card card-item youtube h-100">
                            <div class="card-image">
                                <a href="javascript:void(0);" 
                                   class="d-block video-link"
                                   data-video-id="<?php echo e($video->video_id); ?>"
                                   data-video-title="<?php echo e($video->title); ?>">
                                    <img class="card-img-top lazy" src="<?php echo e($video->thumbnail_url); ?>" alt="Video Thumbnail">
                                    <div class="play-button">
                                    </div>
                                </a>
                            </div>
                            <div class="card-body d-flex flex-column youtube">
                                <h5 class="card-title mb-1"><?php echo e($video->title); ?></h5>
                                <div class="d-flex justify-content-between mt-auto">
                                    <p class="card-text"><i class="bi bi-eye"></i> <?php echo e($video->view_count); ?> Views</p>
                                    <p class="card-text"><i class="bi bi-hand-thumbs-up"></i> <?php echo e($video->like_count); ?> Likes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="d-flex justify-content-center">
                    <?php echo $videos->links(); ?>

                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="container my-5">
            <div class="row text-center mb-4">
                <h1>All units</h1>
                <p>Found <?php echo e($courses->count()); ?> units</p>
            </div>
            <div class="row">
                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card card-item">
                        <div class="card-image">
                            <a href="javascript:void(0);" class="d-block course-link"
                               data-course-id="<?php echo e($course->id); ?>"
                               data-enrolled="<?php echo e(in_array($course->id, $enrolledCourseIds)); ?>">
                                <img class="card-img-top lazy" src="<?php echo e($course->image); ?>" alt="Card image cap">
                                <div class="play-button">
                                </div>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo e($course->title); ?></h5>
                            <p class="card-text">
                                <i class="bi bi-person"></i> <?php echo e($course->number_of_students); ?> Students
                            </p>
                            <p class="card-text">
                                <?php if(in_array($course->id, $enrolledCourseIds)): ?>
                                    <span class="badge bg-success">Enrolled</span>
                                <?php endif; ?>
                            </p>
                            <p class="card-text">
                                <?php if($course->type == 'weekly'): ?>
                                    <i class="bi bi-calendar-week"></i> Weekly Unit
                                <?php else: ?>
                                    <i class="bi bi-lightning"></i> Intensive Unit
                                <?php endif; ?>
                            </p>
                            <?php
                                $rating = $course->RateAvg();
                                $fullStars = floor($rating);
                                $halfStar = $rating - $fullStars >= 0.5 ? true : false;
                                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            ?>
                            <div class="d-flex align-items-center justify-content-between pt-2">
                                <div class="review-stars">
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
                            </div>
                            <div class="mt-auto">
                                <?php if(in_array($course->id, $enrolledCourseIds)): ?>
                                    <a href="<?php echo e(route('admin.showcourse', $course->id)); ?>"
                                       class="btn btn-secondary w-100">View Unit</a>
                                <?php else: ?>
                                    <button class="btn btn-primary w-100 enroll-btn"
                                            data-course-id="<?php echo e($course->id); ?>">
                                        Enroll in Unit
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="container my-5">
        <div class="row text-center mb-4">
            <h1>Free Videos</h1>
        </div>
        <?php if($videos->isEmpty()): ?>
            <div class="row">
                <div class="col-12 text-center">
                    <p>No videos available</p>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php $__currentLoopData = $videos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $video): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card card-item youtube h-100">
                        <div class="card-image">
                            <a href="javascript:void(0);" 
                               class="d-block video-link"
                               data-video-id="<?php echo e($video->video_id); ?>"
                               data-video-title="<?php echo e($video->title); ?>">
                                <img class="card-img-top lazy" src="<?php echo e($video->thumbnail_url); ?>" alt="Video Thumbnail">
                                <div class="play-button">
                                </div>
                            </a>
                        </div>
                        <div class="card-body d-flex flex-column youtube">
                            <h5 class="card-title mb-1"><?php echo e($video->title); ?></h5>
                            <div class="d-flex justify-content-between mt-auto">
                                <p class="card-text"><i class="bi bi-eye"></i> <?php echo e($video->view_count); ?> Views</p>
                                <p class="card-text"><i class="bi bi-hand-thumbs-up"></i> <?php echo e($video->like_count); ?> Likes</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="d-flex justify-content-center">
                <?php echo $videos->links(); ?>

            </div>
        <?php endif; ?>
    </div>
    <?php if(auth()->user()->getAgeGroup() !== '1-5'): ?>
        <div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5 class="modal-title">Unlock Premium Features</h5>
                        <p>Get exclusive access to advanced units and resources. Enhance your learning with our
                            Premium Plan!</p>
                        <button class="btn btn-detail btn-primary"
                                onclick="location.href='<?php echo e(route('subscription.details')); ?>'">See Details</button>
                        <p class="mt-4"><strong>Note:</strong> if you have subscribed and still see this message,
                            please wait for up to one minute. The page will reload, and you will gain access to the
                            units. The delay is due to payment processing by PayPal. Thank you for your patience!</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>



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

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        <?php if(Auth::user()->student->subscription_status !== 'subscribed' || $subscription->status !== 'active'): ?>
            <?php if(auth()->user()->getAgeGroup() !== '1-5'): ?>
                $('#subscriptionModal').modal('show');
            <?php endif; ?>
        <?php endif; ?>

        $('.course-link').click(function() {
            var courseId = $(this).data('course-id');
            var isEnrolled = $(this).data('enrolled');

            if (isEnrolled) {
                window.location.href = '/courses/' + courseId + '/show';
            } else {
                $.ajax({
                    url: '<?php echo e(route('student.getCourseDetails')); ?>',
                    type: 'GET',
                    data: {
                        course_id: courseId
                    },
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

        $('.enroll-btn').click(function() {
            var courseId = $(this).data('course-id');
            $.ajax({
                url: '<?php echo e(route('student.getCourseDetails')); ?>',
                type: 'GET',
                data: {
                    course_id: courseId
                },
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

        $('#enrollButton').click(function() {
            var courseId = $(this).data('course-id');
            $.ajax({
                url: '<?php echo e(route('student.enroll')); ?>',
                type: 'POST',
                data: {
                    course_id: courseId,
                    _token: '<?php echo e(csrf_token()); ?>'
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

        $(document).ready(function() {
    $(document).on('click', '.video-link', function() {
        var videoId = $(this).data('video-id');
        var videoTitle = $(this).data('video-title');

        $('#videoTitle').text(videoTitle);
        $('#videoFrame').attr('src', 'https://www.youtube.com/embed/' + videoId);
        $('#videoModal').modal('show');
    });

    $('#videoModal').on('hide.bs.modal', function() {
        $('#videoFrame').attr('src', '');
    });
});

        function fetchVideos(page) {
            $.ajax({
                url: '<?php echo e(route('student.dashboard')); ?>',
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
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/student/dashboard.blade.php ENDPATH**/ ?>