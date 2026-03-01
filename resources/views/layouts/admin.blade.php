<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin | Trip to Komodo')</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @stack('styles')
        @stack('schema')
    </head>
    <body class="bg-white text-slate-900 antialiased">
        <div class="min-h-screen">
            <div class="border-b border-emerald-100 bg-white">
                <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-semibold">A</span>
                        <div>
                            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Admin Panel</p>
                            <p class="text-base font-semibold">Trip to Komodo</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-sm">
                        <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-700 hover:text-emerald-700">Lihat Website</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">Logout</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="mx-auto grid max-w-6xl gap-8 px-6 py-8 lg:grid-cols-[240px_1fr]">
                @php
                    $routeIs = fn (string $name) => request()->routeIs($name);
                    $activeLink = 'bg-white text-emerald-700';
                    $inactiveLink = 'text-slate-800 hover:bg-white hover:text-emerald-700';
                    $isWebSettings = $routeIs('admin.web-settings.*');
                    $wsSection = (string) request()->query('section', 'all');
                @endphp

                <aside class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                    <nav class="space-y-4 text-sm">
                        <div>
                            <p class="px-4 pb-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-700/80">Umum</p>
                            <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 {{ $routeIs('admin.dashboard') ? $activeLink : $inactiveLink }}">Dashboard</a>
                        </div>

                        <div>
                            <p class="px-4 pb-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-700/80">Konten</p>
                            <a href="{{ route('admin.destinations.index') }}" class="block rounded-2xl px-4 py-3 {{ $routeIs('admin.destinations.*') ? $activeLink : $inactiveLink }}">Destinasi</a>
                            <a href="{{ route('admin.faqs.index') }}" class="mt-1 block rounded-2xl px-4 py-3 {{ $routeIs('admin.faqs.*') ? $activeLink : $inactiveLink }}">FAQ</a>
                        </div>

                        <div>
                            <p class="px-4 pb-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-700/80">Trip</p>
                            <a href="{{ route('admin.tour-categories.index') }}" class="block rounded-2xl px-4 py-3 {{ $routeIs('admin.tour-categories.*') ? $activeLink : $inactiveLink }}">Kategori Trip</a>
                            <a href="{{ route('admin.tour-packages.index') }}" class="mt-1 block rounded-2xl px-4 py-3 {{ $routeIs('admin.tour-packages.*') ? $activeLink : $inactiveLink }}">Paket Trip</a>
                        </div>

                        <div>
                            <p class="px-4 pb-2 text-[11px] font-semibold uppercase tracking-[0.25em] text-emerald-700/80">Pengaturan</p>

                            <details class="rounded-2xl" {{ $isWebSettings ? 'open' : '' }}>
                                <summary class="flex cursor-pointer list-none items-center justify-between rounded-2xl px-4 py-3 {{ $isWebSettings ? $activeLink : $inactiveLink }}">
                                    <span>Website Settings</span>
                                    <span class="text-xs">⌄</span>
                                </summary>

                                <div class="mt-2 space-y-1 pl-2">
                                    @php
                                        $wsBase = route('admin.web-settings.edit');
                                        $wsAll = route('admin.web-settings.edit', ['section' => 'all']);
                                        $wsAbout = route('admin.web-settings.edit', ['section' => 'about']);
                                        $wsContact = route('admin.web-settings.edit', ['section' => 'contact']);
                                        $wsHero = route('admin.web-settings.edit', ['section' => 'home-hero']);
                                    @endphp

                                    <a href="{{ $wsAll }}" class="block rounded-xl px-4 py-2 text-xs {{ ($isWebSettings && $wsSection === 'all') || ($isWebSettings && $wsSection === '') ? $activeLink : $inactiveLink }}">Semua</a>
                                    <a href="{{ $wsAbout }}" class="block rounded-xl px-4 py-2 text-xs {{ ($isWebSettings && $wsSection === 'about') ? $activeLink : $inactiveLink }}">About Us</a>
                                    <a href="{{ $wsContact }}" class="block rounded-xl px-4 py-2 text-xs {{ ($isWebSettings && $wsSection === 'contact') ? $activeLink : $inactiveLink }}">Kontak</a>
                                    <a href="{{ $wsHero }}" class="block rounded-xl px-4 py-2 text-xs {{ ($isWebSettings && $wsSection === 'home-hero') ? $activeLink : $inactiveLink }}">Home Hero</a>
                                </div>
                            </details>
                        </div>
                    </nav>
                </aside>

                <main>
                    @if (session('status'))
                        <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
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
