<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo $__env->make('layout.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->yieldContent('styles'); ?>

</head>

<body>

    <?php echo $__env->make('layout.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldContent('content'); ?>


    <?php echo $__env->make('layout.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldContent('scripts'); ?>

    <style>
        #loom-companion-mv3 {
            display: none !important;
        }
    </style>
</body>

</html>
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/layout/main.blade.php ENDPATH**/ ?>