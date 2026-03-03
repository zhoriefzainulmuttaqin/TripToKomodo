<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $metaTitle = trim((string) $__env->yieldContent('title', $siteName ?? config('app.name', 'Trip to Komodo')));
            $metaDesc = trim((string) $__env->yieldContent('meta_description', 'Trip Labuan Bajo: curated trips, premium boats, and unforgettable Komodo experiences.'));
            $metaKeywords = trim((string) $__env->yieldContent('meta_keywords', ''));
            $canonicalUrl = trim((string) $__env->yieldContent('canonical', url()->current()));
            $metaRobots = trim((string) $__env->yieldContent('meta_robots', ''));
            $ogTitle = trim((string) $__env->yieldContent('og_title', $metaTitle));
            $ogDesc = trim((string) $__env->yieldContent('og_description', $metaDesc));
            $hasQuery = request()->query() !== [];
        @endphp

        <title>{{ $metaTitle }}</title>
        <meta name="description" content="{{ $metaDesc }}">
        @if ($metaKeywords !== '')
            <meta name="keywords" content="{{ $metaKeywords }}">
        @endif
        <link rel="canonical" href="{{ $canonicalUrl }}">

        @if ($metaRobots !== '')
            <meta name="robots" content="{{ $metaRobots }}">
        @elseif ($hasQuery)
            <meta name="robots" content="noindex,follow">
        @endif

        <link rel="icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" href="{{ asset('favicon.ico') }}">

        {{-- Open Graph / Twitter --}}
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDesc }}">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:site_name" content="{{ $siteName ?? config('app.name', 'Trip to Komodo') }}">
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:image" content="@yield('og_image', asset('favicon.ico'))">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $ogTitle }}">
        <meta name="twitter:description" content="{{ $ogDesc }}">
        <meta name="twitter:image" content="@yield('og_image', asset('favicon.ico'))">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

        {{-- UIcons (Flaticon) for brand icons (e.g., WhatsApp) --}}
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.4.0/uicons-brands/css/uicons-brands.css">

        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
                line-height: 1;
                vertical-align: middle;
            }
        </style>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @hasSection('hreflang')
            @yield('hreflang')
        @else
            @php
                $route = request()->route();
                $routeName = $route?->getName();
                $routeParams = $route?->parameters() ?? [];
            @endphp
            @if (!empty($routeName) && array_key_exists('lang', $routeParams))
                @foreach ($activeLanguages as $language)
                    @php
                        $href = null;
                        try {
                            $href = route($routeName, array_merge($routeParams, ['lang' => $language->code]));
                        } catch (\Throwable) {
                            $href = null;
                        }
                    @endphp
                    @if (!empty($href))
                        <link rel="alternate" hreflang="{{ $language->code }}" href="{{ $href }}">
                    @endif
                @endforeach
                <link rel="alternate" hreflang="x-default" href="{{ url('/') }}">
            @endif
        @endif

        {{-- Site-wide schema --}}
        @php
            $siteUrl = url('/');
            $org = [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => config('app.name', 'Trip to Komodo'),
                'url' => $siteUrl,
            ];
            $webSite = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => config('app.name', 'Trip to Komodo'),
                'url' => $siteUrl,
            ];
        @endphp
        <script type="application/ld+json">{!! json_encode($org, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($webSite, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

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
                    $whatsappCta = __('home.contact.cta_whatsapp');
                @endphp

                <a
                    href="{{ $whatsappUrl }}"
                    class="fixed bottom-6 right-6 z-50 flex items-center gap-3"
                    aria-label="{{ $whatsappCta }}"
                >
                    <span class="max-w-[220px] rounded-full border border-slate-200 bg-white/95 px-4 py-2 text-xs font-semibold text-slate-800 shadow-sm backdrop-blur-sm truncate">
                        {{ $whatsappCta }}
                    </span>

                    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition hover:-translate-y-0.5 hover:bg-[#1ebe5a]">
                        <i class="fi fi-brands-whatsapp text-[28px] leading-none" aria-hidden="true"></i>
                    </span>
                </a>
            </div>
        @endif

        @stack('scripts')
    </body>
</html>
