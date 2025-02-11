<div class="alert alert-<?php echo e($type); ?> border-0 bg-<?php echo e($type); ?> alert-dismissible fade show py-2 position-fixed top-0 end-0 m-3"
    role="alert" style="z-index: 10">
    <div class="d-flex align-items-center">
        <div class="font-35 text-white">
            <?php if($icon): ?>
                <i class="<?php echo e($icon); ?>"></i>
            <?php else: ?>
                <?php switch($type):
                    case ('primary'): ?>
                        <i class='bx bx-bookmark-heart'></i>
                    <?php break; ?>

                    <?php case ('secondary'): ?>
                        <i class='bx bx-tag-alt'></i>
                    <?php break; ?>

                    <?php case ('success'): ?>
                        <i class='bx bxs-check-circle'></i>
                    <?php break; ?>

                    <?php case ('danger'): ?>
                        <i class='bx bxs-message-square-x'></i>
                    <?php break; ?>

                    <?php case ('warning'): ?>
                        <i class='bx bx-info-circle'></i>
                    <?php break; ?>

                    <?php case ('info'): ?>
                        <i class='bx bx-info-square'></i>
                    <?php break; ?>

                    <?php case ('dark'): ?>
                        <i class='bx bx-bell'></i>
                    <?php break; ?>

                    <?php default: ?>
                        <i class='bx bx-info-circle'></i>
                <?php endswitch; ?>
            <?php endif; ?>
        </div>
        <div class="ms-3">
            <h6 class="mb-0 text-white"><?php echo e($title); ?></h6>
            <div class="text-white"><?php echo e($message); ?></div>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<style>
    .alert {
        transition: opacity 0.5s ease-in-out;
        z-index: 10 !important;
    }

    .alert-dismissible .btn-close {
        position: absolute;
        top: 0.25rem;
        right: 0.25rem;
    }
</style>
<?php /**PATH /var/www/html/safar-ai-staging/resources/views/components/alert.blade.php ENDPATH**/ ?>