<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin | Trip to Komodo')</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

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

            <div class="mx-auto grid max-w-6xl gap-8 px-6 py-8 lg:grid-cols-[220px_1fr]">
                <aside class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                    <nav class="space-y-1 text-sm">
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-2xl px-4 py-3 text-slate-800 hover:bg-white hover:text-emerald-700">Dashboard</a>
                        <a href="{{ route('admin.destinations.index') }}" class="block rounded-2xl px-4 py-3 text-slate-800 hover:bg-white hover:text-emerald-700">Destinasi</a>
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
    </body>
</html>
