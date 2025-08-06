<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"  data-theme="dark">
<head>
    <meta charset="utf-8">

    <link rel="icon" href="/favicon.ico" type="image/x-icon">

    <!-- Standard favicon -->
    {{--<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">--}}

    <!-- Apple Touch Icon (iPhone/iPad homescreen) -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">

    <!-- Web manifest (for Progressive Web Apps) -->
    <link rel="manifest" href="/site.webmanifest">

    <!-- Optional: Safari pinned tab icon -->
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">

    <!-- Theme color for mobile browsers -->
    <meta name="theme-color" content="#ffffff">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', '') }}</title>
    <!-- Styles -->
    @php
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    @endphp
    @vite('resources/css/app.css')
    @vite('resources/css/login.css')
</head>
<body class=''>
<div class="bg"></div>
<div class="bg-overlay"></div>
    @yield('content')
</body>

</html>
