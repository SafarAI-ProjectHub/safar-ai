<section>
    <header class="mb-4">
        <h2 class="h4 font-weight-bold text-dark">
            <?php echo e(__('Update Password')); ?>

        </h2>
        <p class="text-muted">
            <?php echo e(__('Ensure your account is using a long, random password to stay secure.')); ?>

        </p>
    </header>

    <form id="update-password-form" method="post" action="<?php echo e(route('password.update')); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('put'); ?>

        <div class="form-group">
            <label for="update_password_current_password" class="form-label"><?php echo e(__('Current Password')); ?></label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control"
                autocomplete="current-password">
            <span class="text-danger" id="current_password_error"></span>
        </div>

        <div class="form-group mt-3">
            <label for="update_password_password" class="form-label"><?php echo e(__('New Password')); ?></label>
            <input id="update_password_password" name="password" type="password" class="form-control"
                autocomplete="new-password">
            <span class="text-danger" id="password_error"></span>
        </div>

        <div class="form-group mt-3">
            <label for="update_password_password_confirmation" class="form-label"><?php echo e(__('Confirm Password')); ?></label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                class="form-control" autocomplete="new-password">
            <span class="text-danger" id="password_confirmation_error"></span>
        </div>

        <div class="d-flex align-items-center mt-4">
            <button type="submit" class="btn btn-primary"><?php echo e(__('Save')); ?></button>
            <p class="text-success ms-3 mb-0" id="status_message" style="display: none;">
                <?php echo e(__('Saved.')); ?>

            </p>
        </div>
    </form>
</section>
<script>
    $(document).ready(function() {
        $('#update-password-form').on('submit', function(event) {
            event.preventDefault();

            $('#current_password_error').text('');
            $('#password_error').text('');
            $('#password_confirmation_error').text('');
            $('#status_message').hide();

            var formData = $(this).serialize(); 

            $.ajax({
                url: $(this).attr('action'), 
                method: 'POST',
                data: formData,
                success: function(response) {

                    $('#status_message').show();
                },
                error: function(xhr) {

                    if (xhr.status === 422) { 
                        var errors = xhr.responseJSON.errors;
                        if (errors.current_password) {
                            $('#current_password_error').text(errors.current_password[0]);
                        }
                        if (errors.password) {
                            $('#password_error').text(errors.password[0]);
                        }
                        if (errors.password_confirmation) {
                            $('#password_confirmation_error').text(errors.password_confirmation[0]);
                        }
                    } else {
                        alert('An error occurred while updating the password.');
                    }
                }
            });
        });
    });
</script>

<?php /**PATH /var/www/html/safar-ai-staging/resources/views/profile/partials/update-password-form.blade.php ENDPATH**/ ?>