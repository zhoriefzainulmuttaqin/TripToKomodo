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
        @stack('styles')
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

                @php
                    $whatsappUrl = $contactSettings['whatsapp_url'] ?? 'https://wa.me/6281200000000';
                @endphp
                <a href="{{ $whatsappUrl }}" class="fixed bottom-6 right-6 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-emerald-700" aria-label="WhatsApp Chat">
                    <svg viewBox="0 0 32 32" class="h-6 w-6" fill="currentColor" aria-hidden="true">
                        <path d="M19.11 17.21c-.28-.14-1.64-.81-1.9-.9-.26-.1-.45-.14-.64.14-.19.28-.73.9-.9 1.09-.16.19-.33.21-.6.07-.28-.14-1.18-.43-2.25-1.37-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.12-.12.28-.33.42-.5.14-.16.19-.28.28-.47.1-.19.05-.35-.02-.5-.07-.14-.64-1.54-.88-2.11-.23-.56-.47-.49-.64-.5h-.55c-.19 0-.5.07-.76.35-.26.28-1 1-1 2.43 0 1.43 1.03 2.81 1.18 3 .14.19 2.02 3.08 4.88 4.32.68.3 1.21.48 1.62.61.68.22 1.29.19 1.78.12.54-.08 1.64-.67 1.87-1.32.23-.64.23-1.19.16-1.32-.07-.12-.26-.19-.54-.33z" />
                        <path d="M16 3C9.38 3 4 8.26 4 14.75c0 2.1.6 4.15 1.74 5.92L4 29l8.63-2.72a12.1 12.1 0 003.37.47c6.62 0 12-5.26 12-11.75C28 8.26 22.62 3 16 3zm0 21.3c-1.1 0-2.19-.17-3.23-.52l-.67-.22-5.1 1.61 1.66-4.87-.44-.76a9.8 9.8 0 01-1.38-4.99c0-5.4 4.5-9.8 10.16-9.8 5.6 0 10.16 4.4 10.16 9.8 0 5.4-4.56 9.8-10.16 9.8z" />
                    </svg>
                </a>
            </div>
        @endif
    </body>
</html>
