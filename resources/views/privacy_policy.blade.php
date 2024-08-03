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
                <p>Privacy Policy</p>
                <br>
                <h2>Your privacy is critically important to us. Read below to understand how we handle your personal
                    information.</h2>
            </header>
        </div>
    </section>

    <section class="py-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-12 col-12">
                    <p class="fs-5 mb-4">
                        This Privacy Policy explains how we collect, use, and share information about you when you use our
                        Safar AI website and services.
                    </p>
                    <h3 class="fw-bold mb-3">1. Information We Collect</h3>
                    <p class="fs-5 mb-4">
                        We collect information you provide directly to us when you register for an account, enroll in a
                        course, participate in quizzes, or otherwise communicate with us. This includes your name, email
                        address.
                    </p>
                    <h3 class="fw-bold mb-3">2. Use of Information</h3>
                    <p class="fs-5 mb-4">
                        We use the information we collect to operate, maintain, and provide to you the features and
                        functionality of the Service, to communicate with you, and to conduct research and analysis.
                    </p>
                    <h3 class="fw-bold mb-3">3. Sharing of Information</h3>
                    <p class="fs-5 mb-4">
                        We do not share your personal information with third parties except as necessary to provide our
                        services, comply with the law, or protect our rights. We may share your information with vendors,
                        consultants, and other service providers who need access to such information to carry out work on
                        our behalf.
                    </p>
                    <h3 class="fw-bold mb-3">4. Data Security</h3>
                    <p class="fs-5 mb-4">
                        We implement a variety of security measures to maintain the safety of your personal information
                        when you enter, submit, or access your personal information.
                    </p>
                    <h3 class="fw-bold mb-3">5. Your Choices</h3>
                    <p class="fs-5 mb-4">
                        You can access and update your personal information through your account settings. You may also
                        contact us to request the deletion of your personal information. However, some information may
                        remain in archived/backup copies for our records or as required by law.
                    </p>
                    <h3 class="fw-bold mb-3">6. Free Video Content</h3>
                    <p class="fs-5 mb-4">
                        Safar AI offers a selection of free video content accessible to users without a subscription. This
                        content is available to provide value and an introduction to our teaching style and quality.
                    </p>
                    <h3 class="fw-bold">Contact Us</h3>
                    <p class="fs-5">
                        If you have any questions about this Privacy Policy, please contact us at <a
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
