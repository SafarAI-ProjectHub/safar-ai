<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Zoom Meeting Details</h5>

            <p>
                <strong>Teacher Name:</strong>
                <?php echo e($userMeeting->meeting->user->full_name); ?>

            </p>

            <p>
                <strong>Topic:</strong>
                <?php echo e($userMeeting->meeting->topic); ?>

            </p>

            <p>
                <strong>Agenda:</strong>
                <?php echo e($userMeeting->meeting->agenda); ?>

            </p>

            <p>
                <strong>Start Time:</strong>
                <?php echo e($userMeeting->meeting->start_time->format('d-m-Y / h:i A')); ?>

            </p>

            <p>
                <strong>Duration:</strong>
                <?php
                    $hours = intdiv($userMeeting->meeting->duration, 60);
                    $minutes = $userMeeting->meeting->duration % 60;
                ?>
                <?php if($hours > 0): ?>
                    <?php echo e($hours); ?> hour<?php echo e($hours > 1 ? 's' : ''); ?>

                    <?php if($minutes > 0): ?>
                        <?php echo e($minutes); ?> minute<?php echo e($minutes > 1 ? 's' : ''); ?>

                    <?php endif; ?>
                <?php else: ?>
                    <?php echo e($minutes); ?> minute<?php echo e($minutes > 1 ? 's' : ''); ?>

                <?php endif; ?>
            </p>

            <div class="mt-3">
                <a href="<?php echo e($userMeeting->meeting->join_url); ?>" class="btn btn-primary" target="_blank">
                    Join Meeting
                </a>
                <a href="<?php echo e(route('student.meetings.index')); ?>" class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/student/meeting-details.blade.php ENDPATH**/ ?>