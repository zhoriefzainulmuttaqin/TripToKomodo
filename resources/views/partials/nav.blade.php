<nav x-data="{ mobileOpen: false, userOpen: false, langOpen: false, curOpen: false, toursOpen: false }" class="sticky top-0 z-40 border-b border-emerald-100 bg-white/90 backdrop-blur">
    @php
        $langIcons = [
            'id' => '🇮🇩',
            'en' => '🇬🇧',
            'zh' => '🇨🇳',
            'es' => '🇪🇸',
            'de' => '🇩🇪',
            'ru' => '🇷🇺',
        ];

        $currencySymbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'SGD' => 'S$',
            'AUD' => 'A$',
        ];

        $localeCode = app()->getLocale();
        $langLabel = strtoupper($localeCode);
        $langIcon = $langIcons[$localeCode] ?? '🌐';

        $curCode = $currentCurrency ?? 'IDR';
        $curSymbol = $activeCurrencies->firstWhere('code', $curCode)?->symbol ?? ($currencySymbols[$curCode] ?? $curCode);
        $homeUrl = route('home', ['lang' => $localeCode]);
        $contactPageUrl = route('contact', ['lang' => $localeCode]);
        $aboutPageUrl = route('about', ['lang' => $localeCode]);
        $toursUrl = route('tours.index', ['lang' => $localeCode]);

        $contactEmail = $contactSettings['email'] ?? 'hello@triptokomodo.com';
        $contactPhone = $contactSettings['phone'] ?? '+62 812 0000 0000';
        $contactWhatsapp = $contactSettings['whatsapp'] ?? $contactPhone;
        $contactWhatsappUrl = $contactSettings['whatsapp_url'] ?? 'https://wa.me/6281200000000';

        $nav = trans('nav');
    @endphp

    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <a href="{{ $homeUrl }}" class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700 font-semibold">LB</span>
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-600">{{ $nav['brand_tagline'] }}</p>
                <p class="text-base font-semibold text-slate-900">{{ $nav['brand_name'] }}</p>
            </div>
        </a>

        <div class="hidden items-center gap-6 text-sm text-slate-700 md:flex">

            <div class="relative" @mouseenter="toursOpen = true" @mouseleave="toursOpen = false">
                <button type="button" class="inline-flex items-center gap-2 hover:text-emerald-700" @click="toursOpen = !toursOpen">
                    {{ $nav['menu_trips'] }}
                    <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-cloak x-show="toursOpen" x-transition class="absolute left-0 mt-2 w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <a href="{{ $toursUrl }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['menu_all_trips'] }}</a>

                    @if (($activeTourCategories ?? collect())->isNotEmpty())
                        <div class="border-t border-slate-100"></div>
                        @foreach ($activeTourCategories as $category)
                            <a href="{{ $toursUrl }}?category={{ $category->slug }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $category->name }}</a>
                        @endforeach
                    @else
                        <div class="border-t border-slate-100 px-4 py-3 text-xs text-slate-500">{{ $nav['menu_category_empty'] }}</div>
                    @endif
                </div>
            </div>

            <a href="{{ route('rental.mobil', ['lang' => $localeCode]) }}" class="hover:text-emerald-700">{{ $nav['menu_rental'] }}</a>
            <a href="{{ route('blog.index', ['lang' => $localeCode]) }}" class="hover:text-emerald-700">{{ $nav['menu_blog'] }}</a>
            <a href="{{ $contactPageUrl }}" class="hover:text-emerald-700">{{ $nav['menu_contact'] }}</a>
        </div>

        <div class="hidden items-center gap-3 md:flex">
            <div class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-2 py-1">
                <div class="relative" @click.outside="curOpen = false">
                    <button type="button" @click="curOpen = !curOpen" class="inline-flex items-center gap-2 bg-transparent px-2 py-1 text-xs font-semibold text-slate-800">
                        <svg class="h-4 w-4 text-emerald-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M12 2v20"></path>
                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                        <span>{{ $curSymbol }} {{ $curCode }}</span>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-cloak x-show="curOpen" x-transition class="absolute left-0 mt-2 w-40 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        @foreach ($activeCurrencies as $currency)
                            @php
                                $symbol = $currency->symbol ?? ($currencySymbols[$currency->code] ?? $currency->code);
                            @endphp
                            <a href="{{ url('/currency') }}/{{ $currency->code }}" class="flex items-center justify-between px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">
                                <span>{{ $symbol }}</span>
                                <span class="font-semibold">{{ $currency->code }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <span class="h-4 w-px bg-slate-200"></span>

                <div class="relative" @click.outside="langOpen = false">
                    <button type="button" @click="langOpen = !langOpen" class="inline-flex items-center gap-2 bg-transparent px-2 py-1 text-xs font-semibold text-slate-800">
                        <span class="text-base leading-none">{{ $langIcon }}</span>
                        <span>{{ $langLabel }}</span>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-cloak x-show="langOpen" x-transition class="absolute right-0 mt-2 w-44 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        @foreach ($activeLanguages as $language)
                            @php $icon = $langIcons[$language->code] ?? '🌐'; @endphp
                            <a href="{{ url('/lang') }}/{{ $language->code }}" class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">
                                <span class="text-base leading-none">{{ $icon }}</span>
                                <span class="font-semibold">{{ strtoupper($language->code) }}</span>
                                <span class="text-slate-500">{{ $language->name ?? '' }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @auth
                <div class="relative" @click.outside="userOpen = false">
                    <button type="button" @click="userOpen = !userOpen" class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:text-emerald-700">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </span>
                        <span class="max-w-[120px] truncate">{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4 text-slate-500" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                        </svg>
                    </button>

                    <div x-cloak x-show="userOpen" x-transition class="absolute right-0 mt-2 w-52 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        @if (auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['admin_panel'] }}</a>
                        @endif
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['profile'] }}</a>
                        <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 text-left text-sm text-rose-600 hover:bg-rose-50">{{ $nav['logout'] }}</button>
                        </form>
                    </div>
                </div>
            @else
                <a href="{{ route('login') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800 hover:text-emerald-700">{{ $nav['login'] }}</a>
            @endauth

            <a href="{{ $contactPageUrl }}" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">{{ $nav['cta_consult'] }}</a>
        </div>

        <button type="button" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-800 md:hidden" @click="mobileOpen = !mobileOpen">
            {{ $nav['menu_button'] }}
        </button>
    </div>

    <div x-cloak x-show="mobileOpen" x-transition class="border-t border-emerald-100 bg-white md:hidden">
        <div class="mx-auto max-w-6xl space-y-3 px-6 py-4 text-sm">
            <a href="{{ $homeUrl }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['menu_home'] }}</a>

            <details class="rounded-2xl border border-slate-200 bg-white px-4 py-3">
                <summary class="cursor-pointer list-none font-semibold text-slate-800">{{ $nav['menu_trips'] }}</summary>
                <div class="mt-3 space-y-1">
                    <a href="{{ $toursUrl }}" class="block rounded-xl px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['menu_all_trips'] }}</a>
                    @if (($activeTourCategories ?? collect())->isNotEmpty())
                        @foreach ($activeTourCategories as $category)
                            <a href="{{ $toursUrl }}?category={{ $category->slug }}" class="block rounded-xl px-3 py-2 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $category->name }}</a>
                        @endforeach
                    @else
                        <div class="px-3 py-2 text-xs text-slate-500">{{ $nav['menu_category_empty'] }}</div>
                    @endif
                </div>
            </details>

            <a href="{{ route('rental.mobil', ['lang' => $localeCode]) }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['menu_rental'] }}</a>
            <a href="{{ route('blog.index', ['lang' => $localeCode]) }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['menu_blog'] }}</a>
            <a href="{{ $contactPageUrl }}" class="block rounded-2xl px-4 py-3 text-slate-700 hover:bg-emerald-50 hover:text-emerald-800">{{ $nav['menu_contact'] }}</a>

            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2">
                <span class="text-xs font-semibold text-slate-600">{{ $nav['currency_label'] }}</span>
                <div class="ml-auto flex flex-wrap gap-2">
                    @foreach ($activeCurrencies as $currency)
                        @php $symbol = $currency->symbol ?? ($currencySymbols[$currency->code] ?? $currency->code); @endphp
                        <a href="{{ url('/currency') }}/{{ $currency->code }}" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-700 hover:text-emerald-700">{{ $symbol }} {{ $currency->code }}</a>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2">
                <span class="text-xs font-semibold text-slate-600">{{ $nav['language_label'] }}</span>
                <div class="ml-auto flex flex-wrap gap-2">
                    @foreach ($activeLanguages as $language)
                        @php $icon = $langIcons[$language->code] ?? '🌐'; @endphp
                        <a href="{{ url('/lang') }}/{{ $language->code }}" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs text-slate-700 hover:text-emerald-700">{{ $icon }} {{ strtoupper($language->code) }}</a>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                @auth
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">{{ $nav['admin_panel'] }}</a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800">{{ $nav['profile'] }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-rose-600">{{ $nav['logout'] }}</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800">{{ $nav['login'] }}</a>
                @endauth

                <a href="{{ $contactPageUrl }}" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">{{ $nav['cta_consult'] }}</a>
            </div>
        </div>
    </div>
</nav>
