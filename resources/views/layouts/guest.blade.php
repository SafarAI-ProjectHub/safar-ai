<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Safar AI</title>
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/logo-icon.png') }}" type="image/png" />
    <!-- Plugins CSS -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    <link href="{{ asset('assets/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <!-- Loader CSS -->
    <link href="{{ asset('assets/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/pace.min.js') }}" defer></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/bootstrap-extended.css') }}" rel="stylesheet">
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.css') }}" rel="stylesheet">

</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="wrapper">
        <div class="section-authentication-cover">
            <div class="">
                <div class="row g-0">
                    <!-- Left Image Section -->
                    <div
                        class="col-12 col-xl-7 col-xxl-8 auth-cover-left align-items-center justify-content-center d-none d-xl-flex">
                        <div class="card shadow-none bg-transparent shadow-none rounded-0 mb-0">
                            <div class="card-body">
                                {{ $imageSlot ?? '' }}
                            </div>
                        </div>
                    </div>
                    <!-- Main Content Section -->
                    <div class="col-12 col-xl-5 col-xxl-4 auth-cover-right align-items-center justify-content-center">
                        <div class="card rounded-0 m-3 shadow-none bg-transparent mb-0">
                            <div class="card-body p-sm-5">
                                <div class="text-center mb-4">
                                    <a href="/">
                                        <img src="{{ asset('assets/images/logo-icon.png') }}" width="60"
                                            alt="">
                                    </a>
                                    <h5 class="mt-3"><span style="color: #844DCD"><span
                                                style="color:#C45ACD">Safar</span> AI</span></h5>
                                    <p class="mb-0"></p>
                                </div>
                                <div class="form-body">
                                    {{ $slot }}
                                </div>
                                {{-- <div class="login-separater text-center mb-5">
                                        <span>OR SIGN IN WITH</span>
                                        <hr>
                                    </div>
                                    <div class="list-inline contacts-social text-center">
                                        <a href="javascript:;" class="list-inline-item bg-facebook text-white border-0 rounded-3"><i class="bx bxl-facebook"></i></a>
                                        <a href="javascript:;" class="list-inline-item bg-twitter text-white border-0 rounded-3"><i class="bx bxl-twitter"></i></a>
                                        <a href="javascript:;" class="list-inline-item bg-google text-white border-0 rounded-3"><i class="bx bxl-google"></i></a>
                                        <a href="javascript:;" class="list-inline-item bg-linkedin text-white border-0 rounded-3"><i class="bx bxl-linkedin"></i></a>
                                    </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End row -->
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
<script>
    $(document).ready(function() {
        $("#show_hide_password a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });

        $("#show_hide_password_confirmation a").on('click', function(event) {
            event.preventDefault();
            if ($('#show_hide_password_confirmation input').attr("type") == "text") {
                $('#show_hide_password_confirmation input').attr('type', 'password');
                $('#show_hide_password_confirmation i').addClass("bx-hide");
                $('#show_hide_password_confirmation i').removeClass("bx-show");
            } else if ($('#show_hide_password_confirmation input').attr("type") == "password") {
                $('#show_hide_password_confirmation input').attr('type', 'text');
                $('#show_hide_password_confirmation i').removeClass("bx-hide");
                $('#show_hide_password_confirmation i').addClass("bx-show");
            }
        });
    });
</script>

</html>
