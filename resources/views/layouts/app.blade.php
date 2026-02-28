<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Trip to Komodo'))</title>
        <meta name="description" content="@yield('meta_description', 'Trip Labuan Bajo: curated trips, premium boats, and unforgettable Komodo experiences.')">
        <link rel="canonical" href="@yield('canonical', url()->current())">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @yield('hreflang')
        @stack('schema')
    </head>

    <body class="bg-white text-slate-900 antialiased">
        @if (isset($slot))
            <div class="min-h-screen bg-gray-100">
                @include('layouts.navigation')

                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main>
                    {{ $slot }}
                </main>
            </div>
        @else
            <div class="min-h-screen bg-white">
                @include('partials.nav')

                <main>
                    @yield('content')
                </main>

                @include('partials.footer')
            </div>
        @endif
    </body>
</html>
