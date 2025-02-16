@extends('layouts_dashboard.main')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <style>
        .feature-list {
            list-style: none;
            padding-left: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .loader-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255, 255, 255, 0.7);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .loader-message {
            margin-top: 15px;
            font-size: 1.2rem;
            color: #007bff;
        }
        #cliqUserName .bi-clipboard {
            font-size: 1.5rem;
            cursor: pointer;
        }
        /* Pricing card styles */
        .pricing-card {
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: box-shadow 0.3s ease;
            padding: 20px;
            margin: 15px 0;
        }
        .pricing-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .pricing-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')

@php
    // حالة الاشتراك الحالي
    $subscriptionStatus = $subscription ? $subscription->status : 'free';
    $subscriptionDate   = $subscription && $subscription->start_date
        ? $subscription->start_date->format('M d, Y')
        : 'N/A';
    $nextBillingDate    = $subscription && $subscription->next_billing_time
        ? $subscription->next_billing_time->format('M d, Y')
        : 'N/A';

    // لو عندك حقل features كـ cast => مصفوفة مباشرة
    $features = $planDetails && is_array($planDetails->features)
        ? $planDetails->features
        : [];

    $payment = $payment ?? null;
@endphp

<div class="mb-4">
    <h4>Your Current Subscription</h4>
    <p>
        Status:
        @if($subscriptionStatus == 'active')
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-secondary">{{ ucfirst($subscriptionStatus) }}</span>
        @endif
    </p>
    <!-- <p>Next Billing: {{ $nextBillingDate }}</p> -->
</div>

<div class="text-center mb-4">
    <h2>Our Pricing Plans</h2>
    <p>Please select a plan to upgrade, or pay with Cliq if needed.</p>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    @foreach($activePlans as $plan)
        @php
            // تحديد نوع الخطة وأيقونتها
            $typeLower   = strtolower($plan->subscription_type); 
            $iconClass   = 'bi-star';
            if ($typeLower === 'yolo') {
                $iconClass = 'bi-youtube';
            } elseif ($typeLower === 'solo') {
                $iconClass = 'bi-person';
            } elseif ($typeLower === 'tolo') {
                $iconClass = 'bi-people';
            }

            $planName     = $plan->product_name ?: ucfirst($typeLower).' Plan';
            $planFeatures = is_array($plan->features) ? $plan->features : [];
            $isFree       = ($typeLower === 'yolo' && floatval($plan->price) == 0);
        @endphp

        <div class="col">
            <div class="card pricing-card text-center h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <i class="pricing-icon bi {{ $iconClass }}"></i>
                        <h5 class="card-title fw-bold">{{ $planName }}</h5>
                        @if($isFree)
                            <h2 class="fw-bold text-primary">Free</h2>
                        @else
                            <h2 class="fw-bold text-primary">{{ $plan->price }}</h2>
                        @endif
                        <p class="text-muted">{{ ucfirst($typeLower) }} Plan</p>

                        {{-- قائمة الميزات --}}
                        @if($planFeatures)
                            <ul class="feature-list mt-3">
                                @foreach($planFeatures as $feat)
                                    <li><i class="bi bi-check-circle text-success me-2"></i>{{ $feat }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="mt-3">
                        @if($isFree)
                            <button class="btn btn-secondary w-100 start-free-btn"
                                    data-plan-type="yolo">
                                Start Now (Free)
                            </button>
                        @else
                            {{-- باي بال --}}
                            <button class="btn btn-success w-100 mb-2 upgrade-plan-btn"
                                    data-plan-id="{{ $plan->paypal_plan_id }}"
                                    data-plan-type="{{ $typeLower }}">
                                Upgrade with PayPal
                            </button>

                            {{-- كليك (للمستخدمين في الأردن مثلاً) --}}
                            @if (auth()->user()->country_location == 'Jordan')
                                <button class="btn btn-primary w-100 pay-with-cliq-btn"
                                        data-plan-type="{{ $typeLower }}"
                                        data-price="{{ $plan->price }}">
                                    Pay with Cliq
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="mt-5">
    @if ($subscriptionStatus == 'active')
        @if ($payment 
             && $payment->payment_type == 'cliq' 
             && $payment->payment_status == 'pending')
            <span class="badge bg-warning">Payment Pending...</span>
        @else
            <button id="cancel-subscription" class="btn btn-danger">
                Cancel Current Subscription
            </button>
        @endif
    @elseif($subscriptionStatus == 'suspended')
        <button id="reactivate-subscription" class="btn btn-success">
            Reactivate Subscription
        </button>
    @endif

    @if ($payment 
         && $payment->payment_type == 'cliq' 
         && $payment->payment_status == 'rejected')
        <button id="reupload-payment" 
                class="btn btn-danger"
                data-payment-id="{{ $payment->id }}">
            Reupload Payment Proof
        </button>
    @endif
</div>

@endsection

@section('scripts')
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            FilePond.registerPlugin(FilePondPluginFileValidateSize, FilePondPluginFileValidateType);

            document.querySelectorAll('.start-free-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    showLoader('Subscribing you to YOLO free plan...');
                    fetch('{{ route("subscription.subscribeFree") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            showAlert('success', data.message || 'Free plan activated!');
                            setTimeout(() => {
                                window.location.href = '/student';
                            }, 1500);
                        } else {
                            showAlert('error', data.message || 'Failed to subscribe to free plan.');
                        }
                    })
                    .catch(err => {
                        hideLoader();
                        showAlert('error', 'Something went wrong. Please try again.');
                    });
                });
            });

            // باي بال
            document.querySelectorAll('.upgrade-plan-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const planId   = btn.getAttribute('data-plan-id');
                    const planType = btn.getAttribute('data-plan-type');
                    showLoader(`Subscribing to ${planType} plan via PayPal...`);

                    fetch('{{ route('subscriptions.create') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            plan_id: planId,
                            email: '{{ Auth::user()->email }}',
                            user_id: '{{ Auth::id() }}'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            window.location.href = data.approval_url;
                        } else {
                            showAlert('error', data.message || 'Failed to create subscription.');
                        }
                    })
                    .catch(err => {
                        hideLoader();
                        showAlert('error', 'Something went wrong. Please try again.');
                    });
                });
            });

            // الدفع بـCliq
            document.querySelectorAll('.pay-with-cliq-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const planType = btn.getAttribute('data-plan-type');
                    const price    = btn.getAttribute('data-price') || '0';
                    showCliqModal(planType, price);
                });
            });

            // إلغاء الاشتراك (باي بال أو أي اشتراك)
            const cancelBtn = document.getElementById('cancel-subscription');
            if (cancelBtn) {
                cancelBtn.addEventListener('click', () => {
                    showLoader('Cancelling your subscription, please wait...');
                    fetch('{{ route('subscriptions.cancel') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            subscription_id: '{{ $subscription ? $subscription->subscription_id : '' }}'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            showAlert('success', 'Subscription cancelled successfully.');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', data.message || 'Failed to cancel subscription.');
                        }
                    })
                    .catch(() => {
                        hideLoader();
                        showAlert('error', 'Something went wrong. Try again.');
                    });
                });
            }

            // إعادة التفعيل
            const reactivateBtn = document.getElementById('reactivate-subscription');
            if (reactivateBtn) {
                reactivateBtn.addEventListener('click', () => {
                    showLoader('Reactivating your subscription, please wait...');
                    fetch('{{ route('subscriptions.reactivate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            subscription_id: '{{ $subscription ? $subscription->subscription_id : '' }}'
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        hideLoader();
                        if (data.success) {
                            showAlert('success', 'Subscription reactivated successfully.');
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            showAlert('error', data.message || 'Failed to reactivate subscription.');
                        }
                    })
                    .catch(() => {
                        hideLoader();
                        showAlert('error', 'Something went wrong. Try again.');
                    });
                });
            }

            // إعادة الرفع في حال رفض إثبات الدفع عبر Cliq
            const reuploadBtn = document.getElementById('reupload-payment');
            if (reuploadBtn) {
                reuploadBtn.addEventListener('click', () => {
                    const paymentId = reuploadBtn.getAttribute('data-payment-id');
                    showReuploadCliqModal(paymentId);
                });
            }
        });

        /**
         * نافذة رفع إثبات الدفع بالـCliq
         */
        function showCliqModal(planType, price) {
            const action = planType;
            let htmlContent = `
                <form id="cliqPaymentForm">
                    <div class="mb-3 text-center">
                        <div>
                            <h3><strong>Pay with</strong> 
                                <img src="{{ asset('img/cliq.svg') }}" alt="Cliq Logo" style="max-width: 50px;">
                            </h3>
                        </div>
                        <div>
                            <h2 id="cliqUserName" class="border fw-bold d-inline-block"
                                onclick="copyCliqUserName()"
                                data-toggle="tooltip" 
                                data-placement="top" 
                                title="Click to copy">
                                {{ $cliqUserName ?? 'CliqAccountName' }}
                                <i class="bi bi-clipboard" style="cursor: pointer;" onclick="copyCliqUserName()"></i>
                            </h2>
                        </div>
                        <div>
                            <h6>You can pay to the above Cliq account, then upload the proof below.</h6>
                            <h5>The amount to be paid is <span class="text-primary">$${price}</span> (Plan: ${planType.toUpperCase()})</h5>
                            <h6 class="text-success">Payment confirmation might take 24-48 hours.</h6>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Your Full Name</label>
                        <input type="text" class="form-control" id="userName" name="userName" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_image" class="form-label">Proof of Payment</label>
                        <input type="file" class="form-control" id="payment_image" name="payment_image" accept="image/*" required>
                    </div>
                </form>
            `;

            Swal.fire({
                title: '',
                html: htmlContent,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                didOpen: () => {
                    const inputElement = document.querySelector('input[name="payment_image"]');
                    window.pond = FilePond.create(inputElement, {
                        allowFileTypeValidation: true,
                        acceptedFileTypes: ['image/*'],
                        fileValidateTypeLabelExpectedTypes: 'Expected file type: Image'
                    });
                },
                preConfirm: () => {
                    const form = document.getElementById('cliqPaymentForm');
                    const formData = new FormData();
                    if (!form.userName.value) {
                        Swal.showValidationMessage('Please enter your full name.');
                        return;
                    }
                    formData.append('userName', form.userName.value);

                    if (pond.getFiles().length > 0) {
                        const file = pond.getFile();
                        formData.append('payment_image', file.file);
                    } else {
                        Swal.showValidationMessage('Please upload a proof of payment.');
                        return;
                    }

                    // إرسال الطلب
                    return fetch(`/pay-with-cliq?action=${action}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error submitting your payment proof.');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Submitted!',
                        'Your payment proof has been submitted. Await admin approval.',
                        'success'
                    );
                    setTimeout(() => window.location.reload(), 1500);
                }
            });
        }

        /**
         * مودال إعادة الرفع في حال رفض إثبات الدفع
         */
        function showReuploadCliqModal(paymentId) {
            let htmlContent = `
                <form id="reuploadCliqForm">
                    <div class="mb-3 text-center">
                        <h4>Reupload Payment Proof</h4>
                        <p>Please upload a new proof of payment.</p>
                    </div>
                    <div class="mb-3">
                        <label for="userName" class="form-label">Your Full Name</label>
                        <input type="text" class="form-control" id="userName" name="userName" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_image" class="form-label">Proof of Payment</label>
                        <input type="file" class="form-control" id="payment_image" name="payment_image" accept="image/*" required>
                    </div>
                </form>
            `;
            Swal.fire({
                title: '',
                html: htmlContent,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                didOpen: () => {
                    const inputElement = document.querySelector('input[name="payment_image"]');
                    window.pondReupload = FilePond.create(inputElement, {
                        allowFileTypeValidation: true,
                        acceptedFileTypes: ['image/*'],
                        fileValidateTypeLabelExpectedTypes: 'Expected file type: Image'
                    });
                },
                preConfirm: () => {
                    const form = document.getElementById('reuploadCliqForm');
                    const formData = new FormData();
                    if (!form.userName.value) {
                        Swal.showValidationMessage('Please enter your full name.');
                        return;
                    }
                    formData.append('userName', form.userName.value);

                    if (pondReupload.getFiles().length > 0) {
                        const file = pondReupload.getFile();
                        formData.append('payment_image', file.file);
                    } else {
                        Swal.showValidationMessage('Please upload a proof of payment.');
                        return;
                    }

                    // إرسال الطلب
                    return fetch(`/reupload-payment-proof/${paymentId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            throw new Error(data.message || 'Error reuploading your payment proof.');
                        }
                        return data;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'Submitted!',
                        'Your reuploaded proof has been submitted. Await admin approval.',
                        'success'
                    );
                    setTimeout(() => window.location.reload(), 1500);
                }
            });
        }

        // عرض لودينغ
        function showLoader(message) {
            const loaderHtml = `
                <div class="loader-overlay">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only"></span>
                    </div>
                    <div class="loader-message">${message}</div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', loaderHtml);
        }

        function hideLoader() {
            const overlay = document.querySelector('.loader-overlay');
            if (overlay) overlay.remove();
        }

        function showAlert(type, message) {
            Swal.fire({
                icon: type,
                title: type.charAt(0).toUpperCase() + type.slice(1),
                text: message,
            });
        }

        // نسخ اسم حساب Cliq
        function copyCliqUserName() {
            const cliqUserName = document.getElementById('cliqUserName').innerText.trim();
            navigator.clipboard.writeText(cliqUserName).then(function() {
                // success
            }, function(err) {
                // error
            });
        }
    </script>
@endsection
