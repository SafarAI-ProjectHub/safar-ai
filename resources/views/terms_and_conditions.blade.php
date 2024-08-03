@extends('layout.main')

@section('styles')
    <style>
        section.bg-light.py-5 {
            margin-top: 90px;
        }
    </style>
@endsection

@section('content')
    <section class="bg-light py-5">
        <div class="container">
            <header class="section-header">
                <p>Terms & Conditions</p>
                <br>
                <h2>Read our terms below to learn more about your rights and responsibilities as a Safar AI user.</h2>
            </header>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12 col-12">
                    <p class="fs-5 mb-4">
                        Welcome to Safar AI! We offer online English courses through our platform. These Terms of Service
                        govern your use of our website and services.
                    </p>
                    <h3 class="fw-bold mb-3">1. Eligibility</h3>
                    <p class="fs-5 mb-4">By using our Services, you confirm that you are at least 18 years old or have legal
                        parental or guardian consent. You also confirm that you have the right, authority, and capacity to
                        enter into these agreements.</p>
                    <h3 class="fw-bold mb-3">2. Registration and Account Security</h3>
                    <p class="fs-5 mb-4">To access our courses, you must register and create an account. You agree to
                        provide accurate, current, and complete information during the registration process and to update
                        such information to keep it accurate, current, and complete.</p>
                    <h3 class="fw-bold mb-3">3. Course Enrollment and Fees</h3>
                    <p class="fs-5 mb-4">We offer various courses categorized by age and proficiency levels. Each course
                        requires enrollment through our website. Upon subscribing, users gain access to all courses
                        available for their age group. Fees for the subscription are listed on our website and must be paid
                        at the time of enrollment. </p>
                    <h3 class="fw-bold mb-3">4. Agreement to Terms</h3>
                    <p class="fs-5 mb-4">
                        By registering on the website, you agree to the terms and conditions outlined in our policy.
                    </p>
                    <h3 class="fw-bold mb-3">5. Free Video Content</h3>
                    <p class="fs-5 mb-4">
                        Safar AI offers a selection of free video content accessible to users without a subscription. This
                        content is available to provide value and an introduction to our teaching style and quality.
                    </p>
                    <h3 class="fw-bold mb-3">6. User Conduct</h3>
                    <p class="fs-5 mb-4">
                        Users agree to use the platform in accordance with all applicable laws and not to engage in any
                        behavior that is harmful, threatening, abusive, or otherwise objectionable.
                    </p>
                    <h3 class="fw-bold mb-3">7. Privacy Policy</h3>
                    <p class="fs-5 mb-4">
                        Your use of our services is also governed by our Privacy Policy. Please review our <a
                            href="{{ route('privacy') }}"> Privacy Policy </a>to
                        understand our practices.
                    </p>
                    <h3 class="fw-bold">Changes to Terms</h3>
                    <p class="fs-5 mb-4">
                        We reserve the right, at our sole discretion, to modify or replace these Terms at any time. We will
                        provide notice before any new terms taking effect.
                    </p>
                    <p class="fs-5">
                        Questions? Please email us at <a
                            href="mailto:{{ env('Email_Adrees') }}">{{ env('Email_Adrees') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const currentPath = window.location.pathname;
            const navLinks = document.querySelectorAll('nav#navbar a.nav-link');
            navLinks.forEach(link => {
                if (link.href.endsWith(currentPath)) {
                    link.classList.add('active');
                } else {
                    link.classList.remove('active');
                }
            });
        });
    </script>
@endsection
