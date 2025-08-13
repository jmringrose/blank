<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ Auth()->user()->theme ?? 'retroish' }}">
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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

        <!-- Scripts -->
       {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen" id="app">
            {{--  @include('layouts.navigation')--}}
            <headingsmall title="Test page"
                          name="{{ Auth()->user()->name}}"
                          id="{{ Auth()->user()->id }}"

                          link1="Dashboard"
                          url1="/dashboard"

                          link2="Form Data"
                          url2="/formdata"

                          link3="Sequences"
                          url3="/sequences"

                          link4=""
                          url4="/test4"

                          theme="{{ Auth()->user()->theme }}"
            ></headingsmall>
            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset
            <!-- Page Content -->
            <main>
                @yield('content')
            </main>
        </div>
    </body>
</html>
