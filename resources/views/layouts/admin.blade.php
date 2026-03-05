<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin | ' . ($siteName ?? 'Trip to Komodo'))</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />
        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
                line-height: 1;
                vertical-align: middle;
            }

            [x-cloak] { display: none !important; }
        </style>

        @stack('styles')
        @stack('schema')
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        @php
            $routeIs = fn (string $name) => request()->routeIs($name);
            $activeLink = 'bg-emerald-900 text-white shadow-sm';
            $inactiveLink = 'text-slate-600 hover:bg-slate-100 hover:text-slate-900';
            $isWebSettings = $routeIs('admin.web-settings.*');
            $isRentalCars = $routeIs('admin.rental-cars.*');
            $isRentalPage = $routeIs('admin.rental.*');
            $isAnalytics = $routeIs('admin.analytics.*');
            $wsSection = (string) request()->query('section', 'all');
        @endphp

        <div class="min-h-screen lg:flex">
            <aside class="hidden h-screen w-72 shrink-0 border-r border-slate-200 bg-white lg:sticky lg:top-0 lg:block">
                <div class="flex h-full flex-col overflow-y-auto px-4 py-6">
                    <div class="flex items-center gap-3 border-b border-slate-200 pb-6">
                        @if (!empty($siteLogoUrl))
                            <span class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl bg-white border border-slate-200">
                                <img src="{{ $siteLogoUrl }}" alt="{{ $siteName ?? 'Trip to Komodo' }}" class="h-full w-full object-contain" loading="eager" decoding="async" />
                            </span>
                        @else
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-semibold">{{ $siteInitials ?? 'TK' }}</span>
                        @endif

                        <div>
                            <p class="text-lg font-bold leading-none text-slate-900">{{ $siteName ?? 'Trip to Komodo' }}</p>
                            <p class="mt-1 text-xs font-medium uppercase tracking-[0.2em] text-slate-400">Admin Dashboard</p>
                        </div>
                    </div>

                    <nav class="mt-5 space-y-5 text-sm">
                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Dashboards</p>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.dashboard') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">dashboard</span>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('admin.analytics.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $isAnalytics ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">monitoring</span>
                                <span>Analytics</span>
                            </a>
                        </div>

                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Konten</p>
                            <a href="{{ route('admin.destinations.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.destinations.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">map</span>
                                <span>Destinasi</span>
                            </a>
                            <a href="{{ route('admin.faqs.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.faqs.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">help</span>
                                <span>FAQ</span>
                            </a>
                            <a href="{{ route('admin.blog-posts.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.blog-posts.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">article</span>
                                <span>Komodo Insider</span>
                            </a>
                        </div>

                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Trip & Rental</p>
                            <a href="{{ route('admin.tour-categories.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.tour-categories.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">category</span>
                                <span>Kategori Trip</span>
                            </a>
                            <a href="{{ route('admin.tour-packages.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.tour-packages.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">luggage</span>
                                <span>Paket Trip</span>
                            </a>
                            <a href="{{ route('admin.rental-cars.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $isRentalCars ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">directions_car</span>
                                <span>Rental Mobil</span>
                            </a>
                            <a href="{{ route('admin.rental.edit') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $isRentalPage ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">settings</span>
                                <span>Halaman Rental</span>
                            </a>
                        </div>

                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">CRM & Tools</p>
                            <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.customers.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">groups</span>
                                <span>Customers</span>
                            </a>
                            <a href="{{ route('admin.tools.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.tools.index') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">build</span>
                                <span>Tools</span>
                            </a>
                            <a href="{{ route('admin.tools.invoices.index') }}" class="mt-1 flex items-center gap-3 rounded-xl px-3 py-2.5 {{ $routeIs('admin.tools.invoices.*') ? $activeLink : $inactiveLink }}">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                <span>Invoices</span>
                            </a>
                        </div>

                        <div>
                            <p class="px-3 pb-2 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-400">Pengaturan</p>
                            <details class="rounded-xl" {{ $isWebSettings ? 'open' : '' }}>
                                <summary class="flex cursor-pointer list-none items-center justify-between rounded-xl px-3 py-2.5 {{ $isWebSettings ? $activeLink : $inactiveLink }}">
                                    <span class="flex items-center gap-3">
                                        <span class="material-symbols-outlined text-[18px]">tune</span>
                                        <span>Website Settings</span>
                                    </span>
                                    <span class="material-symbols-outlined text-[18px] leading-none">expand_more</span>
                                </summary>

                                <div class="mt-2 space-y-1 pl-3 pr-1">
                                    <a href="{{ route('admin.web-settings.edit', ['section' => 'all']) }}" class="block rounded-lg px-3 py-2 text-xs {{ ($isWebSettings && $wsSection === 'all') || ($isWebSettings && $wsSection === '') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">Semua</a>
                                    <a href="{{ route('admin.web-settings.edit', ['section' => 'identity']) }}" class="block rounded-lg px-3 py-2 text-xs {{ ($isWebSettings && $wsSection === 'identity') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">Identitas</a>
                                    <a href="{{ route('admin.web-settings.edit', ['section' => 'footer']) }}" class="block rounded-lg px-3 py-2 text-xs {{ ($isWebSettings && $wsSection === 'footer') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">Footer</a>
                                    <a href="{{ route('admin.web-settings.edit', ['section' => 'about']) }}" class="block rounded-lg px-3 py-2 text-xs {{ ($isWebSettings && $wsSection === 'about') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">About Us</a>
                                    <a href="{{ route('admin.web-settings.edit', ['section' => 'contact']) }}" class="block rounded-lg px-3 py-2 text-xs {{ ($isWebSettings && $wsSection === 'contact') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">Kontak</a>
                                    <a href="{{ route('admin.web-settings.edit', ['section' => 'home-hero']) }}" class="block rounded-lg px-3 py-2 text-xs {{ ($isWebSettings && $wsSection === 'home-hero') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-500 hover:bg-slate-100 hover:text-slate-700' }}">Home Hero</a>
                                </div>
                            </details>
                        </div>
                    </nav>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-4 lg:px-8">
                        <div class="flex min-w-0 flex-1 items-center gap-3">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 lg:hidden">
                                <span class="material-symbols-outlined">menu</span>
                            </span>

                            <div class="relative w-full max-w-md">
                                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                                <input type="text" placeholder="Search..." class="h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-4 text-sm text-slate-700 placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none" />
                            </div>
                        </div>

                        <div class="flex items-center gap-2 sm:gap-3">
                            <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-medium text-slate-600 hover:text-emerald-700">Lihat Website</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="flex-1 p-4 lg:p-8">
                    @if (session('status'))
                        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
