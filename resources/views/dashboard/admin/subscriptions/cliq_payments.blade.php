@extends('layouts_dashboard.main')

@section('styles')
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
@endsection

@section('content')
    <!-- Filter Card with Instructions -->
    <div class="row instruction-card">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Review Instructions</h5>
                    <p class="card-text">1. Click on the image to view it in full size.</p>
                    <p class="card-text">2. Ensure the amount in the image matches the amount displayed.</p>
                    <p class="card-text">3. Use Approve or Reject to process the payment.</p>
                    <p class="card-text">4. If you reject a payment, the user is notified to re-upload.</p>
                    <p class="card-text">5. If you approve, the user gets +1 month (or 12 months for TOLO) of subscription.</p>
                    <p class="card-text">6. For any concerns, contact the user directly (phone/email).</p>
                </div>
            </div>
        </div>
    </div>

    @if ($pendingPayments->isEmpty())
        <div class="no-pending-payments">
            <h4>No pending payments to review.</h4>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 row-cols-xxl-5 product-grid">
            @foreach ($pendingPayments as $payment)
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <img src="{{ asset($payment->payment_image) }}" class="card-img-top" alt="Payment Proof" />
                        <div class="card-body">
                            <h6 class="card-title">Name: {{ $payment->user->full_name }}</h6>
                            <p class="card-text">Amount: ${{ $payment->amount }}</p>
                            <p class="card-text">
                                Subscription Type: 
                                {{ optional($payment->userSubscription->subscription)->subscription_type ?? 'N/A' }}
                            </p>
                            <p class="card-text">
                                Date: {{ \Carbon\Carbon::parse($payment->transaction_date)->format('M d, Y') }}
                            </p>
                            <p class="card-text">Contact info:</p>
                            <div class="phone-email mb-2">
                                <p>
                                    <i class="bi bi-telephone-fill me-2"></i>
                                    <a href="tel:{{ $payment->user->phone_number }}">{{ $payment->user->phone_number }}</a>
                                </p>
                                <p>
                                    <i class="bi bi-envelope-fill me-2"></i>
                                    <a href="mailto:{{ $payment->user->email }}">{{ $payment->user->email }}</a>
                                </p>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button class="btn btn-success btn-sm me-md-2"
                                        onclick="approvePayment({{ $payment->id }})">Approve</button>
                                <button class="btn btn-danger btn-sm"
                                        onclick="rejectPayment({{ $payment->id }})">Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@section('scripts')
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
                new Viewer(img, { toolbar: true });
            });
        });

        function approvePayment(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Approve this payment?',
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
                        data: { _token: '{{ csrf_token() }}' },
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
                showCancelButton: true,
                confirmButtonText: 'Reject',
                showLoaderOnConfirm: true,
                preConfirm: (reason) => {
                    return $.ajax({
                        url: '/admin/reject-payment/' + id,
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
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
@endsection
