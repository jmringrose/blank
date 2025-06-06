<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"  data-theme="dark">
<head>
    <meta charset="utf-8">
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
