<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-stone-500 h-screen">
<img src="/img/logos/logo@0.25x.png" class="h-48 ml-12 mt-12">
<div class="flex items-center justify-center">
    @yield('content')
</div>
</body>
</html>
