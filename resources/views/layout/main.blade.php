<!DOCTYPE html>
<html lang="en">

<head>
    @include('layout.app')
    @yield('styles')

</head>

<body>

    @include('layout.header')

    @yield('content')


    @include('layout.footer')

    @yield('scripts')

    <style>
        #loom-companion-mv3 {
            display: none !important;
        }
    </style>
</body>

</html>
