<nav class="sticky top-0 z-40 border-b border-emerald-100 bg-white/90 backdrop-blur">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" class="flex items-center gap-3">

            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-semibold">LB</span>
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-600">Labuan Bajo</p>
                <p class="text-base font-semibold text-slate-900">Trip to Komodo</p>
            </div>
        </a>

        <div class="hidden items-center gap-6 text-sm text-slate-700 md:flex">
            <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="hover:text-emerald-700">Paket Trip</a>

            <a href="#experiences" class="hover:text-emerald-700">Experience</a>
            <a href="#faq" class="hover:text-emerald-700">FAQ</a>
            <a href="#contact" class="hover:text-emerald-700">Kontak</a>
        </div>

        <div class="flex items-center gap-3 text-sm">
            @auth
                @if (auth()->user()->is_admin)
                    <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-700 hover:text-emerald-700">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs text-slate-700 hover:text-emerald-700">Login</a>
            @endauth

            <form action="{{ route('currency.switch', ['code' => 'IDR']) }}" method="get" class="relative">
                <select name="code" onchange="window.location='{{ url('/currency') }}/'+this.value" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700">
                    @foreach ($activeCurrencies as $currency)
                        <option value="{{ $currency->code }}" @selected($currentCurrency === $currency->code)>
                            {{ $currency->code }}
                        </option>
                    @endforeach
                </select>
            </form>
            <form action="{{ route('lang.switch', ['lang' => 'id']) }}" method="get" class="relative">
                <select name="lang" onchange="window.location='{{ url('/lang') }}/'+this.value" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs text-slate-700">
                    @foreach ($activeLanguages as $language)
                        <option value="{{ $language->code }}" @selected(app()->getLocale() === $language->code)>
                            {{ strtoupper($language->code) }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="#contact" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">Konsultasi Trip</a>
        </div>
    </div>
</nav>
