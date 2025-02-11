<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title"><?php echo e(isset($zoomMeeting) ? 'Edit Zoom Meeting' : 'Add New Zoom Meeting'); ?></h5>
            <form id="zoom-meeting-form"
                action="<?php echo e(isset($zoomMeeting) ? route('zoom-meetings.update', $zoomMeeting->id) : route('zoom-meetings.store')); ?>"
                method="POST">
                <?php echo csrf_field(); ?>
                <?php if(isset($zoomMeeting)): ?>
                    <?php echo method_field('PUT'); ?>
                <?php endif; ?>
                <div class="mb-3">
                    <label for="topic" class="form-label">Topic</label>
                    <input type="text" class="form-control" id="topic" name="topic"
                        value="<?php echo e(isset($zoomMeeting) ? $zoomMeeting->topic : ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="datetime-local" class="form-control" id="start_time" name="start_time"
                        value="<?php echo e(isset($zoomMeeting) ? $zoomMeeting->start_time->format('Y-m-d\TH:i') : ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="duration" class="form-label">Duration (in minutes)</label>
                    <input type="number" class="form-control" id="duration" name="duration"
                        value="<?php echo e(isset($zoomMeeting) ? $zoomMeeting->duration : ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="agenda" class="form-label">Agenda</label>
                    <textarea class="form-control" id="agenda" name="agenda"><?php echo e(isset($zoomMeeting) ? $zoomMeeting->agenda : ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="invite_option" class="form-label">Invite Option</label>
                    <select class="form-select" id="invite_option" name="invite_option" required>
                        <option value="all"
                            <?php echo e(isset($zoomMeeting) && $zoomMeeting->invite_option == 'all' ? 'selected' : ''); ?>>All Students
                        </option>
                        <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'Admin|Super Admin')): ?>
                            <option value="teachers"
                                <?php echo e(isset($zoomMeeting) && $zoomMeeting->invite_option == 'teachers' ? 'selected' : ''); ?>>All
                                Teachers</option>
                        <?php endif; ?>
                        <option value="course_specific"
                            <?php echo e(isset($zoomMeeting) && $zoomMeeting->invite_option == 'course_specific' ? 'selected' : ''); ?>>
                            Specific Unit</option>
                    </select>
                </div>
                <div class="mb-3" id="course_select_div"
                    style="display: <?php echo e(isset($zoomMeeting) && $zoomMeeting->invite_option == 'course_specific' ? 'block' : 'none'); ?>;">
                    <label for="course_id" class="form-label">Unit</label>
                    <select class="form-select" id="course_id" name="course_id">
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->id); ?>"
                                <?php echo e(isset($zoomMeeting) && $zoomMeeting->course_id == $course->id ? 'selected' : ''); ?>>
                                <?php echo e($course->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <button type="submit"
                    class="btn btn-primary"><?php echo e(isset($zoomMeeting) ? 'Update Meeting' : 'Create Meeting'); ?></button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#invite_option').on('change', function() {
                if ($(this).val() === 'course_specific') {
                    $('#course_select_div').show();
                } else {
                    $('#course_select_div').hide();
                }
            });

            $('#zoom-meeting-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    url: "<?php echo e(isset($zoomMeeting) ? route('zoom-meetings.update', $zoomMeeting->id) : route('zoom-meetings.store')); ?>",
                    method: "<?php echo e(isset($zoomMeeting) ? 'PUT' : 'POST'); ?>",
                    data: $(this).serialize(),
                    success: function(response) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Zoom meeting ' + (typeof zoomMeeting !==
                                    'undefined' ? 'updated' : 'created') +
                                ' successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        window.location.href = "<?php echo e(route('zoom-meetings.index')); ?>";
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessages = '';
                        for (let error in errors) {
                            errorMessages += errors[error] + '\n';
                        }
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to ' + (typeof zoomMeeting !== 'undefined' ?
                                    'update' : 'create') + ' Zoom meeting: \n' +
                                errorMessages,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        // Check if user needs to authorize Zoom
                        if (xhr.responseJSON.url) {
                            window.location.href = xhr.responseJSON.url;
                        }
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/teacher/create_zoom_meetings.blade.php ENDPATH**/ ?>