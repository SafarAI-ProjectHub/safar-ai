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
</body>

</html>
