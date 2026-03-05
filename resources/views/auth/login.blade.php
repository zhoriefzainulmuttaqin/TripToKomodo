<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Login | {{ $siteName ?? config('app.name', 'Trip to Komodo') }}</title>
        <meta name="robots" content="noindex,nofollow">
        <link rel="canonical" href="{{ url()->current() }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 antialiased">
        <div class="mx-auto flex min-h-screen w-full max-w-7xl items-center px-4 py-8 lg:px-6 lg:py-10">
            <div class="grid w-full overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:grid-cols-2">
            <section class="order-2 flex items-center bg-white px-5 py-7 lg:order-1 lg:min-h-[680px] lg:px-8 lg:py-10 xl:px-10">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 flex items-center gap-3">
                        @if (!empty($siteLogoUrl))
                            <img src="{{ $siteLogoUrl }}" alt="{{ $siteName ?? config('app.name', 'Trip to Komodo') }}" class="h-10 w-10 rounded-lg object-contain" />
                        @else
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-slate-900 text-sm font-bold text-white">TK</span>
                        @endif
                        <p class="text-3xl font-bold tracking-tight text-slate-900">{{ $siteName ?? 'Trip to Komodo' }}</p>
                    </div>

                    <h1 class="text-3xl font-bold leading-tight text-slate-900">Welcome Back</h1>
                    <p class="mt-2 text-base text-slate-500">Sign in to your admin dashboard</p>

                    <x-auth-session-status class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

                    @if ($errors->any())
                        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-base font-semibold text-slate-900">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="email@contoh.com" class="mt-2 h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        </div>

                        <div>
                            <label for="password" class="block text-base font-semibold text-slate-900">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="mt-2 h-12 w-full rounded-xl border border-slate-200 bg-white px-4 text-base text-slate-900 placeholder:text-slate-400 focus:border-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-100" />
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-1">
                            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-3 text-base text-slate-700">
                                <input id="remember_me" type="checkbox" name="remember" class="h-5 w-5 rounded border-slate-300 text-emerald-700 focus:ring-emerald-600" />
                                <span>Keep me logged in</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-base font-semibold text-emerald-800 hover:text-emerald-900">Forgot Password?</a>
                            @endif
                        </div>

                        <button type="submit" class="mt-2 inline-flex h-12 w-full items-center justify-center rounded-xl bg-emerald-900 px-6 text-base font-semibold text-white transition hover:bg-emerald-800">
                            Sign In
                        </button>
                    </form>
                </div>
            </section>

            <section class="order-1 relative hidden overflow-hidden bg-slate-900 lg:order-2 lg:block">
                @if (!empty($loginSideImageUrl))
                    <img src="{{ $loginSideImageUrl }}" alt="Login visual" class="h-full w-full object-cover" />
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/30 to-transparent"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-emerald-950 to-slate-900"></div>
                    <div class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-emerald-500/20 blur-3xl"></div>
                    <div class="absolute -bottom-24 -left-16 h-80 w-80 rounded-full bg-cyan-500/10 blur-3xl"></div>
                @endif
            </section>
            </div>
        </div>
    </body>
</html>
