@extends('layout.main')

@section('styles')
    <style>
        .team .member .member-img {
            position: relative;
            overflow: hidden;
            height: 150px !important;
        }

        .team .member {
            overflow: hidden;
            text-align: center;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0px 0 30px rgba(1, 41, 112, 0.08);
            transition: 0.3s;
            height: 300px !important;
        }

        .member-img .img-fluid {
            max-width: 100%;
            height: auto;
            height: 150px;
        }

        .testimonials .testimonial-item .testimonial-img {
            width: 72px !important;
            height: 60px !important;
            border-radius: 50% !important;
        }

        /* .navbar-mobile ul {
            display: block;
            position: absolute;
            top: 55px;
            right: 15px;
            bottom: 15px;
            left: 15px;
            padding: 10px 0;
            border-radius: 10px;
            background-color: #fff;
            overflow-y: auto;
            transition: 0.3s;
        } */

        .navbar-mobile {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1); 
    backdrop-filter: blur(15px); 
    -webkit-backdrop-filter: blur(15px); 
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); 
    border: 1px solid rgba(255, 255, 255, 0.2);  
    z-index: 9999;
}
.navbar-mobile ul {
    transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
}




        .hero-slider-item {
            position: relative;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            /* Adjust this height as needed */
            display: flex;
            align-items: center;
        }

        @media (max-width: 525px) {
            .hero-slider-item {
                height: 80vh;
            }
        }

        .owl-nav {
            display: none;
        }
        button{
            border:none;
        }
    </style>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/line-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fancybox.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tooltipster.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
@endsection



@section('content')
    <!-- ======= Hero Section ======= -->
    <section id="hero" class="hero d-flex align-items-center">

        <div class="container">
            <div class="row">
                <div class="col-lg-6 d-flex flex-column justify-content-center">
                    <h1 data-aos="fade-up">Welcome to <span style="color: #844DCD"><span style="color:#C45ACD">Safar</span>
                            AI</span></h1>
                    <h2 data-aos="fade-up" data-aos-delay="400"> Learn English with AI 🌟</h2>
                    <div data-aos="fade-up" data-aos-delay="600">
                        <!-- <div class="text-center text-lg-start">
                            <a href="/register"
                                class="btn-get-started scrollto d-inline-flex align-items-center justify-content-center align-self-center">
                                <span>Get Started</span>
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div> -->
                        <div class="text-center text-lg-start">
                            <button type="button"
                                    class="btn-get-started scrollto d-inline-flex align-items-center justify-content-center align-self-center"
                                    onclick="chooseRegistration()">
                                <span>Get Started</span>
                                <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 hero-img" data-aos="zoom-out" data-aos-delay="200">
                    <img src="assets/img/hero-img.png" class="img-fluid" alt="">
                </div>
            </div>
        </div>

    </section><!-- End Hero -->

    <main id="main">
        <!-- ======= About Section ======= -->
        <section id="about" class="about">
            <div class="container" data-aos="fade-up">
                <div class="row gx-0 d-flex justify-content-between">
                    <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up" data-aos-delay="200">
                        <div class="content">
                            <h2>About SafarAI</h2>
                            <h3>At SafarAI, we make learning English easy and fun with advanced AI technology.</h3>
                            <p>
                                SafarAI is dedicated to transforming how English is learned around the world. By integrating
                                cutting-edge AI, we provide a learning experience that is not only personalized but also
                                deeply engaging. Our platform tailors content to meet individual learning styles and speeds,
                                making it suitable for learners of all ages.
                            </p>
                            <p>
                                With a diverse range of courses from basic communication to advanced linguistic challenges,
                                SafarAI is equipped to help you achieve fluency and confidence in English. Our teaching
                                methods include interactive exercises, real-world application, and continuous feedback to
                                ensure a comprehensive learning journey.
                            </p>
                            <p style="font-weight: bold">
                                Join SafarAI today and start your journey to mastering English with the power of AI. Let's
                                make learning an exciting and effective adventure together!
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-5 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                        <img src="assets/img/logo2.png" class="img-fluid" alt="About SafarAI">
                    </div>
                </div>
            </div>
        </section><!-- End About Section -->

        <!-- ======= Services Section ======= -->
        <section id="services" class="services">
            <div class="container" data-aos="fade-up">
                <header class="section-header">
                    <h2>Why Choose Safar AI?</h2>
                    <p>Discover the Advantages of Learning with Us</p>
                </header>

                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="service-box blue">
                            <i class="ri-user-heart-line icon"></i>
                            <h3>Personalized Learning</h3>
                            <p>Lessons tailored to your age and English level for a custom learning experience.</p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="service-box orange">
                            <i class="ri-test-tube-line icon"></i>
                            <h3>Placement Test</h3>
                            <p>Determine your English proficiency with our comprehensive placement tests, similar to IELTS
                                and TOEFL.</p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="service-box green">
                            <i class="ri-user-voice-line icon"></i>
                            <h3>Expert Teachers</h3>
                            <p>Learn from experienced, native English-speaking teachers who are committed to your success.
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
                        <div class="service-box red">
                            <i class="ri-money-dollar-box-line icon"></i>
                            <h3>Affordable</h3>
                            <p>Access high-quality learning experiences at a low cost, making education accessible to
                                everyone.</p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="600">
                        <div class="service-box purple">
                            <i class="ri-time-line icon"></i>
                            <h3>Flexible Learning</h3>
                            <p>Study at your own pace and convenience from home, fitting your learning into your lifestyle.
                            </p>

                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="700">
                        <div class="service-box pink">
                            <i class="ri-group-line icon"></i>
                            <h3>Continuous Support</h3>
                            <p>Benefit from ongoing support as you master English, just like studying in an English-speaking
                                country.</p>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- End Services Section -->

        <!-- ======= Values Section ======= -->
        <section id="values" class="values">
            <div class="container" data-aos="fade-up">
                <header class="section-header">
                    <h2>Our Core Principles</h2>
                    <p>Discover what drives us in delivering exceptional English learning experiences</p>
                </header>

                <div class="row justify-content-center">
                    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
                        <div class="box">
                            <i class="ri-robot-line icon" style="font-size: 60px; color: #007BFF;"></i> <!-- Blue -->
                            <h3>AI-Powered Lessons</h3>
                            <p>Our AI-driven approach customizes lessons to match your unique needs and learning pace,
                                offering a truly personalized experience.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="400">
                        <div class="box">
                            <i class="ri-play-circle-line icon" style="font-size: 60px; color: #FFC107;"></i>
                            <!-- Amber -->
                            <h3>Video Lessons</h3>
                            <p>Engage deeply with our structured video lessons designed to improve listening skills and
                                provide real-world application through follow-up discussions.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-4 mt-lg-0" data-aos="fade-up" data-aos-delay="600">
                        <div class="box">
                            <i class="ri-mic-line icon" style="font-size: 60px; color: #28A745;"></i> <!-- Green -->
                            <h3>Interactive Speaking Practice</h3>
                            <p>Enhance your speaking skills by interacting with our AI teaching assistants, discussing video
                                content to simulate real conversations.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-4" data-aos="fade-up" data-aos-delay="800">
                        <div class="box">
                            <i class="ri-pencil-line icon" style="font-size: 60px; color: #DC3545;"></i> <!-- Red -->
                            <h3>Comprehensive Reading and Writing</h3>
                            <p>Develop your reading and writing proficiency through interactive exercises and discussions,
                                crafted to challenge and build your skills.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 mt-4" data-aos="fade-up" data-aos-delay="1000">
                        <div class="box">
                            <i class="ri-check-double-line icon" style="font-size: 60px; color: #6F42C1;"></i>
                            <!-- Purple -->
                            <h3>Progress Tests</h3>
                            <p>Track and measure your progress with regular assessments, ensuring you remain on the path to
                                mastering English effectively.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- End Values Section -->

        <!-- ======= Counts Section ======= -->
        <section id="counts" class="counts">
            <div class="container" data-aos="fade-up">

                <div class="row gy-4">

                    <div class="col-lg-4 col-md-6">
                        <div class="count-box">
                            <i class="bi bi-emoji-smile"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="{{ $totalStudents }}"
                                    data-purecounter-duration="1" class="purecounter"></span>
                                <p>Satisfied Students</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <div class="count-box">
                            <i class="bi bi-journal-richtext" style="color: #ee6c20;"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="{{ $totalCourses }}"
                                    data-purecounter-duration="1" class="purecounter"></span>
                                <p>Courses Offered</p>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="col-lg-3 col-md-6">
                        <div class="count-box">
                            <i class="bi bi-headset" style="color: #15be56;"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="{{ $learningHours }}"
                                    data-purecounter-duration="1" class="purecounter"></span>
                                <p>Learning Hours</p>
                            </div>
                        </div>
                    </div> --}}

                    <div class="col-lg-4 col-md-6">
                        <div class="count-box">
                            <i class="bi bi-people" style="color: #bb0852;"></i>
                            <div>
                                <span data-purecounter-start="0" data-purecounter-end="{{ $totalTeachers }}"
                                    data-purecounter-duration="1" class="purecounter"></span>
                                <p>Dedicated Instructors</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </section>
        <!-- End Counts Section -->




        <!-- ======= Pricing Section ======= -->
        {{-- <section id="pricing" class="pricing">
                <div class="container" data-aos="fade-up">
                    <header class="section-header">
                        <h2>Pricing</h2>
                        <p>Check our Pricing</p>
                    </header>
                    <ul class="nav nav-tabs top-50 start-50" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="monthly-tab" data-bs-toggle="tab"
                                data-bs-target="#month" type="button" role="tab" aria-controls="home"
                                aria-selected="true">Monthly</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="yearly-tab" data-bs-toggle="tab" data-bs-target="#year"
                                type="button" role="tab" aria-controls="home" aria-selected="false">Yearly</button>
                        </li>

                    </ul>
                    <div class=" row gy-4 tab-content" id="myTabContent">
                        <!-- Monthly Pricing -->
                        <div class="row gy-4 tab-pane fade show active" id="month" role="tabpanel"
                            aria-labelledby="monthly-tab">
                            <!-- Monthly Plan Cards -->
                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                                <div class="box">
                                    <h3 style="color: #07d5c0;">Free Plan</h3>
                                    <div class="price"><sup>$</sup>0<span> / mo</span></div>
                                    <img src="assets/img/pricing-free.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li class="na">Pharetra massa</li>
                                        <li class="na">Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                                <div class="box">
                                    <span class="featured">Featured</span>
                                    <h3 style="color: #65c600;">Starter Plan</h3>
                                    <div class="price"><sup>$</sup>19<span> / mo</span></div>
                                    <img src="assets/img/pricing-starter.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li>Pharetra massa</li>
                                        <li class="na">Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                                <div class="box">
                                    <h3 style="color: #ff901c;">Business Plan</h3>
                                    <div class="price"><sup>$</sup>29<span> / mo</span></div>
                                    <img src="assets/img/pricing-business.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li>Pharetra massa</li>
                                        <li>Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                                <div class="box">
                                    <h3 style="color: #ff0071;">Ultimate Plan</h3>
                                    <div class="price"><sup>$</sup>49<span> / mo</span></div>
                                    <img src="assets/img/pricing-ultimate.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li>Pharetra massa</li>
                                        <li>Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>


                        </div>

                        <!-- Yearly Pricing (Initially Hidden) -->
                        <div class="row gy-4 tab-pane fade" id="year" role="tabpanel" aria-labelledby="yearly-tab">
                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="100">
                                <div class="box">
                                    <h3 style="color: #07d5c0;">Free Plan</h3>
                                    <div class="price"><sup>$</sup>0<span> / year</span></div>
                                    <img src="assets/img/pricing-free.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li class="na">Pharetra massa</li>
                                        <li class="na">Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                                <div class="box">
                                    <span class="featured">Featured</span>
                                    <h3 style="color: #65c600;">Starter Plan</h3>
                                    <div class="price"><sup>$</sup>19<span> / year</span></div>
                                    <img src="assets/img/pricing-starter.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li>Pharetra massa</li>
                                        <li class="na">Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="300">
                                <div class="box">
                                    <h3 style="color: #ff901c;">Business Plan</h3>
                                    <div class="price"><sup>$</sup>29<span> / year</span></div>
                                    <img src="assets/img/pricing-business.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li>Pharetra massa</li>
                                        <li>Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-6" data-aos="zoom-in" data-aos-delay="400">
                                <div class="box">
                                    <h3 style="color: #ff0071;">Ultimate Plan</h3>
                                    <div class="price"><sup>$</sup>49<span> / year</span></div>
                                    <img src="assets/img/pricing-ultimate.png" class="img-fluid" alt="">
                                    <ul>
                                        <li>Aida dere</li>
                                        <li>Nec feugiat nisl</li>
                                        <li>Nulla at volutpat dola</li>
                                        <li>Pharetra massa</li>
                                        <li>Massa ultricies mi</li>
                                    </ul>
                                    <a href="#" class="btn-buy">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </section> --}}



        <!-- ======= F.A.Q Section ======= -->
        <section id="faq" class="faq">

            <div class="container" data-aos="fade-up">

                <header class="section-header">
                    <h2>F.A.Q</h2>
                    <p>Frequently Asked Questions</p>
                </header>

                <div class="row">
                    <div class="col-lg-6">
                        <!-- F.A.Q List 1-->
                        <div class="accordion accordion-flush" id="faqlist1">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-content-1">
                                        What is Safar AI?
                                    </button>
                                </h2>
                                <div id="faq-content-1" class="accordion-collapse collapse" data-bs-parent="#faqlist1">
                                    <div class="accordion-body">
                                        Safar AI leverages advanced AI technology to offer personalized English learning
                                        experiences. Our platform is designed to make learning English both easy and
                                        effective, suitable for all age groups.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-content-2">
                                        What types of courses does Safar AI offer?
                                    </button>
                                </h2>
                                <div id="faq-content-2" class="accordion-collapse collapse" data-bs-parent="#faqlist1">
                                    <div class="accordion-body">
                                        From basic communication skills to advanced linguistic challenges, our courses cover
                                        a comprehensive range of English learning topics.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq-content-3">
                                        How do I start learning with Safar AI?
                                    </button>
                                </h2>
                                <div id="faq-content-3" class="accordion-collapse collapse" data-bs-parent="#faqlist1">
                                    <div class="accordion-body">
                                        You can sign up directly on our website to begin your personalized English learning
                                        journey.
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">

                        <!-- F.A.Q List 2-->
                        <div class="accordion accordion-flush" id="faqlist2">

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2-content-1">
                                        Is Safar AI suitable for non-native English speakers?
                                    </button>
                                </h2>
                                <div id="faq2-content-1" class="accordion-collapse collapse" data-bs-parent="#faqlist2">
                                    <div class="accordion-body">
                                        Absolutely, our AI-driven lessons are designed to assist learners at all proficiency
                                        levels, making it ideal for non-native speakers.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2-content-2">
                                        Does Safar AI offer certifications or qualifications?
                                    </button>
                                </h2>
                                <div id="faq2-content-2" class="accordion-collapse collapse" data-bs-parent="#faqlist2">
                                    <div class="accordion-body">
                                        Upon completion of certain courses, Safar AI provides certificates that may be
                                        useful for academic or professional purposes.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2-content-3">
                                        How can I contact Safar AI for more information?
                                    </button>
                                </h2>
                                <div id="faq2-content-3" class="accordion-collapse collapse" data-bs-parent="#faqlist2">
                                    <div class="accordion-body">
                                        For more inquiries, you can reach us via the contact form on our website, or by
                                        email at <a href="mailto:{{ env('Email_Adrees') }}">{{ env('Email_Adrees') }}</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </section><!-- End F.A.Q Section -->


        <section class="hero-area">
            <div class="hero-slider owl-carousel owl-theme aos-init aos-animate" data-aos="fade-up">
                @foreach ($offers as $index => $offer)
                    <div class="hero-slider-item"
                        style="background-image: url('{{ asset('storage/' . $offer->background_image) }}');">
                        <div class="container">
                            <div class="hero-content text-{{ $offer->alignment }}">
                                <div class="section-heading">
                                    <h2 class="section__title text-white fs-65 lh-80 pb-3">{{ $offer->title }}</h2>
                                    <p class="section__desc text-white pb-4">{{ $offer->description }}</p>
                                </div><!-- end section-heading -->
                                <div
                                    class="hero-btn-box d-flex flex-wrap align-items-center pt-1 justify-content-{{ $offer->alignment }}">
                                    @if ($offer->action_type === 'link')
                                        <a href="{{ $offer->action_value }}" class="btn theme-btn mr-4 mb-4">Join
                                            with Us <i class="la la-arrow-right icon ml-1"></i></a>
                                    @elseif($offer->action_type === 'email')
                                        <a href="mailto:{{ $offer->action_value }}"
                                            class="btn theme-btn mr-4 mb-4">Contact Us <i
                                                class="la la-envelope icon ml-1"></i></a>
                                    @endif
                                </div><!-- end hero-btn-box -->
                            </div><!-- end hero-content -->
                        </div><!-- end container -->
                    </div><!-- end hero-slider-item -->
                @endforeach
            </div><!-- end hero-slider -->
        </section><!-- end hero-area -->



        <!-- ======= Portfolio Section ======= -->
        <!--     <section id="portfolio" class="portfolio"> -->

        <!--       <div class="container" data-aos="fade-up"> -->

        <!--         <header class="section-header"> -->
        <!--           <h2>Portfolio</h2> -->
        <!--           <p>Check our latest work</p> -->
        <!--         </header> -->

        <!--         <div class="row" data-aos="fade-up" data-aos-delay="100"> -->
        <!--           <div class="col-lg-12 d-flex justify-content-center"> -->
        <!--             <ul id="portfolio-flters"> -->
        <!--               <li data-filter="*" class="filter-active">All</li> -->
        <!--               <li data-filter=".filter-app">App</li> -->
        <!--               <li data-filter=".filter-card">Card</li> -->
        <!--               <li data-filter=".filter-web">Web</li> -->
        <!--             </ul> -->
        <!--           </div> -->
        <!--         </div> -->

        <!--         <div class="row gy-4 portfolio-container" data-aos="fade-up" data-aos-delay="200"> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-app"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-1.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>App 1</h4> -->
        <!--                 <p>App</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-1.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="App 1"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-web"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-2.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>Web 3</h4> -->
        <!--                 <p>Web</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-2.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="Web 3"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-app"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-3.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>App 2</h4> -->
        <!--                 <p>App</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-3.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="App 2"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-card"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-4.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>Card 2</h4> -->
        <!--                 <p>Card</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-4.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="Card 2"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-web"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-5.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>Web 2</h4> -->
        <!--                 <p>Web</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-5.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="Web 2"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-app"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-6.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>App 3</h4> -->
        <!--                 <p>App</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-6.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="App 3"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-card"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-7.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>Card 1</h4> -->
        <!--                 <p>Card</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-7.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="Card 1"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-card"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-8.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>Card 3</h4> -->
        <!--                 <p>Card</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-8.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="Card 3"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--           <div class="col-lg-4 col-md-6 portfolio-item filter-web"> -->
        <!--             <div class="portfolio-wrap"> -->
        <!--               <img src="assets/img/portfolio/portfolio-9.jpg" class="img-fluid" alt=""> -->
        <!--               <div class="portfolio-info"> -->
        <!--                 <h4>Web 3</h4> -->
        <!--                 <p>Web</p> -->
        <!--                 <div class="portfolio-links"> -->
        <!--                   <a href="assets/img/portfolio/portfolio-9.jpg" data-gallery="portfolioGallery" class="portfokio-lightbox" title="Web 3"><i class="bi bi-plus"></i></a> -->
        <!--                   <a href="portfolio-details.html" title="More Details"><i class="bi bi-link"></i></a> -->
        <!--                 </div> -->
        <!--               </div> -->
        <!--             </div> -->
        <!--           </div> -->

        <!--         </div> -->

        <!--       </div> -->

        </section><!-- End Portfolio Section -->

        <!-- ======= Testimonials Section ======= -->
        <section id="testimonials" class="testimonials">
            <div class="container" data-aos="fade-up">
                <header class="section-header">
                    <h2>Testimonials</h2>
                    <p>What they are saying about us</p>
                </header>

                <div class="testimonials-slider swiper" data-aos="fade-up" data-aos-delay="200">
                    <div class="swiper-wrapper">
                        @foreach ($reviews as $review)
                            @php
                                $created_at = $review->created_at;
                                $now = \Carbon\Carbon::now();

                                $diffInSeconds = $created_at->diffInSeconds($now);
                                $diffInMinutes = $created_at->diffInMinutes($now);
                                $diffInHours = $created_at->diffInHours($now);
                                $diffInDays = $created_at->diffInDays($now);
                                $diffInWeeks = $created_at->diffInWeeks($now);
                                $diffInMonths = $created_at->diffInMonths($now);
                                $diffInYears = $created_at->diffInYears($now);

                                if ($diffInSeconds < 60) {
                                    $timeDiff = $diffInSeconds . ' seconds ago';
                                } elseif ($diffInMinutes < 60) {
                                    $timeDiff = $diffInMinutes . ' minutes ago';
                                } elseif ($diffInHours < 24) {
                                    $timeDiff = $diffInHours . ' hours ago';
                                } elseif ($diffInDays < 7) {
                                    $timeDiff = $diffInDays . ' days ago';
                                } elseif ($diffInWeeks < 4) {
                                    $timeDiff = $diffInWeeks . ' weeks ago';
                                } elseif ($diffInMonths < 12) {
                                    $timeDiff = $diffInMonths . ' months ago';
                                } else {
                                    $timeDiff = $diffInYears . ' years ago';
                                }
                            @endphp

                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <div class="stars">
                                        @php
                                            $rating = $review->rate;
                                        @endphp
                                        @for ($i = 1; $i <= 5; $i++)
                                            @if ($i <= $rating)
                                                <i class="bi bi-star-fill"></i>
                                            @else
                                                <i class="bi bi-star"></i>
                                            @endif
                                        @endfor
                                    </div>

                                    <p>"{{ $review->comment }}"</p>

                                    <h4>Course:'{{ $review->course->title }}'</h4>
                                    <div class="profile mt-auto">
                                        <img src="{{ asset($review->user->profile_image ? $review->user->profile_image : 'assets/images/avatars/profile-Img.png') }}"
                                            class="testimonial-img" alt="">
                                        <h4>{{ $timeDiff }}</h4>
                                        <h3>{{ $review->user->full_name }}</h3>

                                    </div>
                                </div>
                            </div><!-- End testimonial item -->
                        @endforeach
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
        </section><!-- End Testimonials Section -->


        <!-- ======= Team Section ======= -->
        {{-- <section id="team" class="team">

                <div class="container" data-aos="fade-up">

                    <header class="section-header">
                        <h2>Team</h2>
                        <p>Our hard working team</p>
                    </header>

                    <div class="row gy-4">

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up"
                            data-aos-delay="100">
                            <div class="member">
                                <div class="member-img">
                                    <img src="assets/img/team/team-1.jpg" class="img-fluid" alt="">
                                    <div class="social">
                                        <a href=""><i class="bi bi-twitter"></i></a>
                                        <a href=""><i class="bi bi-facebook"></i></a>
                                        <a href=""><i class="bi bi-instagram"></i></a>
                                        <a href=""><i class="bi bi-linkedin"></i></a>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4>Walter White</h4>
                                    <span>Chief Executive Officer</span>
                                    <p>Velit aut quia fugit et et. Dolorum ea voluptate vel tempore tenetur ipsa quae aut.
                                        Ipsum
                                        exercitationem iure minima enim corporis et voluptate.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up"
                            data-aos-delay="200">
                            <div class="member">
                                <div class="member-img">
                                    <img src="assets/img/team/team-2.jpg" class="img-fluid" alt="">
                                    <div class="social">
                                        <a href=""><i class="bi bi-twitter"></i></a>
                                        <a href=""><i class="bi bi-facebook"></i></a>
                                        <a href=""><i class="bi bi-instagram"></i></a>
                                        <a href=""><i class="bi bi-linkedin"></i></a>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4>Sarah Jhonson</h4>
                                    <span>Product Manager</span>
                                    <p>Quo esse repellendus quia id. Est eum et accusantium pariatur fugit nihil minima
                                        suscipit
                                        corporis. Voluptate sed quas reiciendis animi neque sapiente.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up"
                            data-aos-delay="300">
                            <div class="member">
                                <div class="member-img">
                                    <img src="assets/img/team/team-3.jpg" class="img-fluid" alt="">
                                    <div class="social">
                                        <a href=""><i class="bi bi-twitter"></i></a>
                                        <a href=""><i class="bi bi-facebook"></i></a>
                                        <a href=""><i class="bi bi-instagram"></i></a>
                                        <a href=""><i class="bi bi-linkedin"></i></a>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4>William Anderson</h4>
                                    <span>CTO</span>
                                    <p>Vero omnis enim consequatur. Voluptas consectetur unde qui molestiae deserunt.
                                        Voluptates
                                        enim aut architecto porro aspernatur molestiae modi.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up"
                            data-aos-delay="400">
                            <div class="member">
                                <div class="member-img">
                                    <img src="assets/img/team/team-4.jpg" class="img-fluid" alt="">
                                    <div class="social">
                                        <a href=""><i class="bi bi-twitter"></i></a>
                                        <a href=""><i class="bi bi-facebook"></i></a>
                                        <a href=""><i class="bi bi-instagram"></i></a>
                                        <a href=""><i class="bi bi-linkedin"></i></a>
                                    </div>
                                </div>
                                <div class="member-info">
                                    <h4>Amanda Jepson</h4>
                                    <span>Accountant</span>
                                    <p>Rerum voluptate non adipisci animi distinctio et deserunt amet voluptas. Quia aut
                                        aliquid
                                        doloremque ut possimus ipsum officia.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </section> --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                new Swiper('.team-slider', {
                    speed: 600,
                    loop: false,
                    slidesPerView: 1, // Default number of slides per view
                    spaceBetween: 30, // Default space between slides
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false
                    },
                    pagination: {
                        el: '.swiper-pagination',
                        clickable: true,
                    },
                    navigation: {
                        nextEl: '.swiper-button-next',
                        prevEl: '.swiper-button-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2,
                            spaceBetween: 20
                        },
                        768: {
                            slidesPerView: 3,
                            spaceBetween: 30
                        },
                        1024: {
                            slidesPerView: 4,
                            spaceBetween: 40
                        }
                    }
                });
            });
        </script>
        <section id="team" class="team">
            <div class="container" data-aos="fade-up">
                <header class="section-header">
                    <h2>Team</h2>
                    <p>Our hard working team</p>
                </header>

                <div class="team-slider swiper" data-aos="fade-up" data-aos-delay="200">
                    <div class="swiper-wrapper">
                        @foreach ($teachers as $teacher)
                            <div class="swiper-slide">
                                <div class="member">
                                    <div class="member-img">
                                        <img src="{{ asset($teacher->user->profile_image ?? 'assets/images/avatars/profile-Img.png') }}"
                                            class="img-fluid" alt="{{ $teacher->user->full_name }}">

                                        <div class="social">
                                            @if ($teacher->cv_link)
                                                <a href="{{ $teacher->cv_link }}" target="_blank"><i
                                                        class="bi bi-file-earmark-text"></i></a>
                                            @else
                                                No CV available
                                            @endif
                                        </div>
                                    </div>
                                    <div class="member-info">
                                        <h4>{{ $teacher->user->full_name }}</h4>
                                        <p>Experience: {{ $teacher->years_of_experience }} years</p>
                                    </div>
                                </div>
                            </div><!-- End member item -->
                        @endforeach
                    </div>
                    <!-- Add Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>

            </div>
        </section>
        <!-- End Team Section -->

        <!-- ======= Clients Section ======= -->
        {{-- <section id="clients" class="clients">

                <div class="container" data-aos="fade-up">

                    <header class="section-header">
                        <h2>Our Clients</h2>
                        <p>Temporibus omnis officia</p>
                    </header>

                    <div class="clients-slider swiper">
                        <div class="swiper-wrapper align-items-center">
                            <div class="swiper-slide"><img src="assets/img/clients/client-1.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-2.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-3.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-4.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-5.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-6.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-7.png" class="img-fluid"
                                    alt=""></div>
                            <div class="swiper-slide"><img src="assets/img/clients/client-8.png" class="img-fluid"
                                    alt=""></div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

            </section><!-- End Clients Section --> --}}

        {{-- <!-- ======= Recent Blog Posts Section ======= -->
        <section id="recent-blog-posts" class="recent-blog-posts">

            <div class="container" data-aos="fade-up">

                <header class="section-header">
                    <h2>Blog</h2>
                    <p>Recent posts form our Blog</p>
                </header>

                <div class="row">

                    <div class="col-lg-4">
                        <div class="post-box">
                            <div class="post-img"><img src="assets/img/blog/blog-1.jpg" class="img-fluid"
                                    alt=""></div>
                            <span class="post-date">Tue, September 15</span>
                            <h3 class="post-title">Eum ad dolor et. Autem aut fugiat debitis voluptatem consequuntur sit
                            </h3>
                            <a href="blog-single.html" class="readmore stretched-link mt-auto"><span>Read More</span><i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="post-box">
                            <div class="post-img"><img src="assets/img/blog/blog-2.jpg" class="img-fluid"
                                    alt=""></div>
                            <span class="post-date">Fri, August 28</span>
                            <h3 class="post-title">Et repellendus molestiae qui est sed omnis voluptates magnam</h3>
                            <a href="blog-single.html" class="readmore stretched-link mt-auto"><span>Read More</span><i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="post-box">
                            <div class="post-img"><img src="assets/img/blog/blog-3.jpg" class="img-fluid"
                                    alt=""></div>
                            <span class="post-date">Mon, July 11</span>
                            <h3 class="post-title">Quia assumenda est et veritatis aut quae</h3>
                            <a href="blog-single.html" class="readmore stretched-link mt-auto"><span>Read More</span><i
                                    class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>

                </div>

            </div>

        </section><!-- End Recent Blog Posts Section --> --}}

        <!-- ======= Contact Section ======= -->
        <section id="contact" class="contact">

            <div class="container" data-aos="fade-up">

                <header class="section-header">
                    <h2>Contact</h2>
                    <p>Contact Us</p>
                </header>

                <div class="row gy-4">

                    <div class="col-lg-6">

                        <div class="row gy-4">
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-geo-alt"></i>
                                    <h3>Address</h3>
                                    
                                    <p>Mecca Street,<br>Amman, Jordan 11185</p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-telephone"></i>
                                    <h3>Call Us</h3>
                                    <p>{{ env('phone_number') }}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-envelope"></i>
                                    <h3>Email Us</h3>
                                    <p>
                                        <a href="mailto:{{ env('Email_Adrees') }}" style="color: black; text-decoration: none;" 
                                        onmouseover="this.style.color='purple'" 
                                        onmouseout="this.style.color='black'">
                                            {{ env('Email_Adrees') }}
                                        </a>
                                    </p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box">
                                    <i class="bi bi-clock"></i>
                                    <h3>Open Hours</h3>
                                    <p>Saturday - Thursday<br>8:00AM - 08:00PM</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-6">
                        <form action="/forms/contact" method="post" class="php-email-form">
                            @csrf
                            <div class="row gy-4">

                                <div class="col-md-6">
                                    <input type="text" name="name" class="form-control" placeholder="Your Name"
                                        required>
                                </div>

                                <div class="col-md-6 ">
                                    <input type="email" class="form-control" name="email" placeholder="Your Email"
                                        required>
                                </div>

                                <div class="col-md-12">
                                    <input type="text" class="form-control" name="subject" placeholder="Subject"
                                        required>
                                </div>

                                <div class="col-md-12">
                                    <textarea class="form-control" name="message" rows="6" placeholder="Message" required></textarea>
                                </div>

                                <div class="col-md-12 text-center">
                                    <div class="loading">Loading</div>
                                    <div class="error-message"></div>
                                    <div class="sent-message">Your message has been sent. Thank you!</div>

                                    <button type="submit">Send Message</button>
                                </div>

                            </div>
                        </form>

                    </div>

                </div>

            </div>

        </section><!-- End Contact Section -->

    </main><!-- End #main -->
@endsection

@section('scripts')
    <script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('js/isotope.js') }}"></script>
    <script src="{{ asset('js/waypoint.min.js') }}"></script>
    <script src="{{ asset('js/jquery.counterup.min.js') }}"></script>
    <script src="{{ asset('js/fancybox.js') }}"></script>
    <script src="{{ asset('js/datedropper.min.js') }}"></script>
    <script src="{{ asset('js/emojionearea.min.js') }}"></script>
    <script src="{{ asset('js/tooltipster.bundle.min.js') }}"></script>
    <script src="{{ asset('js/jquery.lazy.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            AOS.init({
                offset: 0, // Trigger animations as soon as elements come into view
                delay: 0, // No delay before the animation starts
                duration: 300, // Adjusted duration for smoother animations
                easing: 'ease-in-out', // Smoother easing function
                once: true, // Only animate once
                mirror: false, // Elements do not animate out while scrolling past them
            });
        });
        // دالة لاختيار نوع التسجيل عبر SweetAlert
        function chooseRegistration() {
            Swal.fire({
                title: 'Register as:',
                text: 'Please choose how you want to sign up:',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Student',
                cancelButtonText: 'Teacher',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // تسجيل كطالب
                    window.location.href = '/register';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // تسجيل كمدرس
                    window.location.href = '/register-teacher';
                }
            });
        }
    </script>
@endsection
