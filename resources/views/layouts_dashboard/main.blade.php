<!doctype html>
<html lang="en">
<style>
    .alert {
        z-index: 5000;
    }
</style>
@yield('styles')
@include('layouts_dashboard.app')

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!--sidebar wrapper -->
        @include('layouts_dashboard.sidebar')
        <!--end sidebar wrapper -->

        <!--start header -->
        @include('layouts_dashboard.header')
        <!--end header -->
        <div class="page-wrapper">
            <div class="page-content">
                @yield('content')
                <!-- Start Floating Alert -->
                @if (session('alert-message'))
                    <x-alert :type="session('alert-type', 'info')" :message="session('alert-message')" :icon="session('alert-icon')" />
                @endif
                <!-- End Floating Alert -->
                <!--start overlay-->
                <!-- <div class="overlay toggle-icon"></div> -->
                <!--end overlay-->
                <!--Start Back To Top Button-->
                <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
            </div>
        </div>
    </div>
    <!--end wrapper-->
    <!-- Scripts -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js"
        integrity="sha512-o36qZrjup13zLM13tqxvZTaXMXs+5i4TL5UWaDCsmbp5qUcijtdCFuW9a/3qnHGfWzFHBAln8ODjf7AnUNebVg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="{{ asset('assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/metismenu/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs/js/chart.js') }}"></script>

    <!--app JS-->
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script>
        new PerfectScrollbar(".app-container")
    </script>

    @yield('scripts')
</body>

</html>
