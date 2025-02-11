<?php $__env->startSection('styles'); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.10.2/viewer.min.css">
    <style>
        .card-img-top {
            cursor: pointer;
        }

        .no-pending-payments {
            text-align: center;
            margin-top: 50px;
        }

        .instruction-card {
            margin-bottom: 20px;
        }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Filter Card with Instructions -->
    <div class="row instruction-card">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Review Instructions</h5>
                    <p class="card-text">1. Click on the image to view it in full size.</p>
                    <p class="card-text">2. Ensure the amount in the image matches the amount displayed on the card.</p>
                    <p class="card-text">3. Use the Approve or Reject buttons to process the payment.</p>
                    <p class="card-text">4. If you reject a payment, the user will be notified to update the image they have
                        uploaded.</p>
                    <p class="card-text">5. If you approve a payment, the user will have 1 month of subscription until their
                        account reverts to the free plan.</p>
                    <p class="card-text">6. If you have any questions or concerns, please contact the user directly by
                        contacting them through the phone number or email provided.</p>
                    <p class="card-text">7. In case of rejection, please provide a reason for the rejection.</p>
                </div>
            </div>
        </div>
    </div>

    <?php if($pendingPayments->isEmpty()): ?>
        <div class="no-pending-payments">
            <h4>No pending payments to review.</h4>
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid">
            <?php $__currentLoopData = $pendingPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="<?php echo e(asset($payment->payment_image)); ?>" class="card-img-top" alt="Payment Image"
                            onclick="viewImage(this)">
                        <div class="card-body">
                            <h6 class="card-title">Name: <?php echo e($payment->user->full_name); ?></h6>
                            <p class="card-text">Amount: $<?php echo e($payment->amount); ?></p>
                            <p class="card-text">Subscription Type :
                                <?php echo e($payment->usersubscription->subscription->subscription_type); ?>

                            <p class="card-text">Date:
                                <?php echo e(\Carbon\Carbon::parse($payment->transaction_date)->format('M d, Y')); ?></p>
                            <p class="card-text">Contact info:</p>
                            <div class="phone-email mb-2">
                                <p><i class="bi bi-telephone-fill me-2"></i><a
                                        href="tel:<?php echo e($payment->user->phone_number); ?>"><?php echo e($payment->user->phone_number); ?></a>
                                </p>
                                <p><i class="bi bi-envelope-fill me-2"></i><a
                                        href="mailto:<?php echo e($payment->user->email); ?>"><?php echo e($payment->user->email); ?></a></p>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button class="btn btn-success btn-sm me-md-2"
                                    onclick="approvePayment(<?php echo e($payment->id); ?>)">Approve</button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="rejectPayment(<?php echo e($payment->id); ?>)">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.10.2/viewer.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imageElements = document.querySelectorAll('.card-img-top');
            imageElements.forEach((img) => {
                new Viewer(img, {
                    toolbar: true
                });
            });
        });

        function approvePayment(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to approve this payment?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/admin/approve-payment/' + id,
                        method: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Approved!', 'Payment has been approved.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Failed!', 'Failed to approve payment.', 'error');
                            }
                        },
                        error: function(error) {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                        }
                    });
                }
            });
        }

        function rejectPayment(id) {
            Swal.fire({
                title: 'Enter rejection reason:',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Reject',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    return $.ajax({
                        url: '/admin/reject-payment/' + id,
                        method: 'POST',
                        data: {
                            _token: '<?php echo e(csrf_token()); ?>',
                            reason: reason
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire('Rejected!', 'Payment has been rejected.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Failed!', 'Failed to reject payment.', 'error');
                            }
                        },
                        error: function(error) {
                            Swal.fire('Error!', 'An error occurred. Please try again.', 'error');
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            });
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts_dashboard.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/safar-ai-staging/resources/views/dashboard/admin/subscriptions/cliq_payments.blade.php ENDPATH**/ ?>