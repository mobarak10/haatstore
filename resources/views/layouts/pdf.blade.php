<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <link rel="icon" href="{{ asset('public/images/favicon.png') }}" type="image/png" sizes="16x16">
    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css">--}}
    {{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/css/lightbox.min.css">--}}

    <title>@yield('title') | {{ __(config('app.name', 'Laravel')) }} | User Panel</title>

    <!-- Styles -->
    <link href="{{ asset('public/css/app-user.css') }}" rel="stylesheet">

    {{-- add stylesheet --}}
    @stack('style')
</head>
<body>
<div id="app">
    <section>
        @section('content')
        @show
    </section>
</div>

<!-- Scripts -->
<script src="{{ asset('public/js/app-user.js') }}"></script>
<script src="{{ asset('public/js/app.js') }}"></script>

</body>
</html>

