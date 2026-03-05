@extends('layouts.app')

@php
    $name = $translation?->name ?? 'Rental Car';
    $metaTitle = $translation?->meta_title ?: $name;
    $metaDesc = $translation?->meta_description ?: ($translation?->excerpt ?? '');
    $metaKeywords = $translation?->meta_keywords ?? '';

    $tr = trans('rentals.show');
    $tr = is_array($tr) ? $tr : [];

    $ogImage = !empty($car->image) ? $car->image : asset('favicon.ico');

    $currencyCode = strtoupper((string) ($pricing['currency_code'] ?? 'IDR'));

    $currencySymbols = [
        'IDR' => 'Rp',
        'USD' => '$',
        'EUR' => '€',
        'SGD' => 'S$',
        'AUD' => 'A$',
    ];

    $symbol = $activeCurrencies->firstWhere('code', $currencyCode)?->symbol
        ?? ($currencySymbols[$currencyCode] ?? $currencyCode);

    $amount = (float) ($pricing['price_per_day_converted'] ?? 0);
    $formatted = ($currencyCode === 'IDR')
        ? number_format((int) round($amount), 0, ',', '.')
        : number_format($amount, 2, '.', ',');

    $specs = [
        [
            'label' => $tr['seats_label'] ?? 'Seats',
            'value' => !empty($car->seats)
                ? __('rentals.show.seats_value', ['count' => (int) $car->seats])
                : '—',
            'icon' => 'airline_seat_recline_normal',
        ],
        [
            'label' => $tr['luggage_label'] ?? 'Luggage',
            'value' => !empty($car->luggage)
                ? __('rentals.show.luggage_value', ['count' => (int) $car->luggage])
                : '—',
            'icon' => 'luggage',
        ],
        [
            'label' => $tr['transmission_label'] ?? 'Transmission',
            'value' => !empty($car->transmission) ? $car->transmission : '—',
            'icon' => 'settings',
        ],
        [
            'label' => $tr['fuel_label'] ?? 'Fuel',
            'value' => !empty($car->fuel) ? $car->fuel : '—',
            'icon' => 'local_gas_station',
        ],
    ];

    $rentalIndexUrl = route('rental.mobil', ['lang' => app()->getLocale()]);
    $contactUrl = route('home', ['lang' => app()->getLocale()]) . '#contact';

    $waBase = $contactSettings['whatsapp_url'] ?? null;
    $waText = __('rentals.show.whatsapp_text', [
        'name' => $name,
        'price' => $symbol . ' ' . $formatted,
        'per_day' => $tr['per_day'] ?? 'per day',
    ]);
    $waUrl = !empty($waBase)
        ? ($waBase . (str_contains($waBase, '?') ? '&' : '?') . 'text=' . urlencode($waText))
        : null;

    $byLang = ($car->translations ?? collect())->keyBy('language_code');
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeywords)
@section('canonical', url()->current())
@section('og_type', 'product')
@section('og_image', $ogImage)

@section('hreflang')
    @php
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
    @endphp
    @foreach ($activeLanguages as $language)
        @php
            $t = $byLang->get($language->code);
        @endphp
        @if ($t && ($t->is_active ?? true))
            <link rel="alternate" hreflang="{{ $language->code }}" href="{{ route('rental.mobil.show', ['lang' => $language->code, 'slug' => $t->slug]) }}">
        @endif
    @endforeach
    @php
        $x = $byLang->get($fallbackLocale);
        $xHref = $x && ($x->is_active ?? true) && !empty($x->slug)
            ? route('rental.mobil.show', ['lang' => $fallbackLocale, 'slug' => $x->slug])
            : url($fallbackLocale);
    @endphp
    <link rel="alternate" hreflang="x-default" href="{{ $xHref }}">
@endsection

@push('schema')
    @php
        $canonical = url()->current();

        $imageAbs = $ogImage;
        if (!empty($imageAbs) && !\Illuminate\Support\Str::startsWith($imageAbs, ['http://', 'https://'])) {
            $imageAbs = url($imageAbs);
        }

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('rentals.show.breadcrumb_home'),
                    'item' => route('home', ['lang' => app()->getLocale()]),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('rentals.show.breadcrumb_rental'),
                    'item' => $rentalIndexUrl,
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $name,
                    'item' => $canonical,
                ],
            ],
        ];

        $productSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => $name,
            'description' => \Illuminate\Support\Str::limit(strip_tags((string) $metaDesc), 160, ''),
            'url' => $canonical,
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $currencyCode,
                'price' => $amount,
                'url' => $canonical,
                'availability' => 'https://schema.org/InStock',
            ],
        ];

        if (!empty($imageAbs)) {
            $productSchema['image'] = [$imageAbs];
        }
    @endphp

    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($productSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endpush

@section('content')
    <section class="relative overflow-hidden">
        @if (!empty($car->image))
            <img
                src="{{ $car->image }}"
                alt="{{ $name }}"
                class="absolute inset-0 h-full w-full object-cover"
                loading="eager"
                decoding="async"
                fetchpriority="high"
            />
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/55 to-white"></div>
        @else
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.22),transparent_65%)]"></div>
        @endif

        <div class="relative mx-auto max-w-6xl px-6 py-14">
            <nav class="text-xs text-white/80">
                <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" class="hover:text-white">{{ __('rentals.show.breadcrumb_home') }}</a>
                <span class="px-2">/</span>
                <a href="{{ $rentalIndexUrl }}" class="hover:text-white">{{ __('rentals.show.breadcrumb_rental') }}</a>
                <span class="px-2">/</span>
                <span class="text-white">{{ $name }}</span>
            </nav>

            <div class="mt-6 grid gap-10 lg:grid-cols-[1.5fr_0.7fr] lg:items-start">
                <div>
                    <a href="{{ $rentalIndexUrl }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/90 hover:text-white">
                        <span class="material-symbols-outlined text-[20px] leading-none" aria-hidden="true">arrow_back</span>
                        <span>{{ __('rentals.show.back_to_rental') }}</span>
                    </a>

                    <h1 class="mt-6 text-4xl font-semibold leading-tight text-white md:text-5xl">{{ $name }}</h1>

                    @if (!empty($translation?->excerpt))
                        <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/80">{{ $translation->excerpt }}</p>
                    @endif

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="#overview" class="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white">{{ __('rentals.show.view_detail') }}</a>
                        <a href="{{ $contactUrl }}" class="rounded-full border border-white/25 bg-white/10 px-6 py-3 text-sm font-semibold text-white">{{ __('rentals.show.consult_booking') }}</a>
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-white">
                            <p class="text-xs text-white/70">{{ __('rentals.show.price_from') }}</p>
                            <p class="mt-2 text-xl font-semibold">{{ $symbol }} {{ $formatted }}</p>
                            <p class="mt-1 text-xs text-white/70">{{ __('rentals.show.per_day') }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-white">
                            <p class="text-xs text-white/70">{{ __('rentals.show.quick_specs') }}</p>
                            <p class="mt-2 text-sm font-semibold">
                                {{ !empty($car->seats) ? __('rentals.show.seats_value', ['count' => (int) $car->seats]) : '—' }} •
                                {{ !empty($car->transmission) ? $car->transmission : '—' }} •
                                {{ !empty($car->fuel) ? $car->fuel : '—' }}
                            </p>
                        </div>
                    </div>
                </div>

                <aside class="rounded-3xl border border-white/15 bg-white/10 p-6 text-white backdrop-blur lg:sticky lg:top-28">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/70">{{ $tr['quick_booking'] ?? 'Quick booking' }}</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $symbol }} {{ $formatted }} <span class="text-lg font-normal text-white/80">/ {{ $tr['per_day'] ?? 'per hari' }}</span></p>
                    <p class="mt-2 text-sm text-white/80">{{ $tr['price_follows_currency'] ?? 'Harga mengikuti mata uang yang kamu pilih di navbar.' }}</p>

                    <div class="mt-6 space-y-3">
                        <a href="{{ $contactUrl }}" class="block rounded-full bg-emerald-500 px-6 py-3 text-center text-sm font-semibold text-white">Booking / Tanya Unit</a>
                        @if (!empty($waUrl))
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="block rounded-full border border-white/25 bg-white/10 px-6 py-3 text-center text-sm font-semibold text-white">{{ $tr['chat_whatsapp'] ?? 'Chat WhatsApp' }}</a>
                        @elseif (!empty($waBase))
                            <a href="{{ $waBase }}" target="_blank" rel="noopener" class="block rounded-full border border-white/25 bg-white/10 px-6 py-3 text-center text-sm font-semibold text-white">Chat WhatsApp</a>
                        @endif
                    </div>

                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-white/85">
                        <p class="font-semibold text-white">{{ $tr['notes_title'] ?? 'Catatan' }}</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            <li>{{ $tr['note_1'] ?? 'Harga dapat berbeda tergantung rute dan durasi.' }}</li>
                            <li>{{ $tr['note_2'] ?? 'Konfirmasi ketersediaan unit sebelum booking.' }}</li>
                        </ul>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-14" id="overview">
        <div class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                <h2 class="text-2xl font-semibold text-slate-900">{{ __('rentals.show.about_unit') }}</h2>

                @if (!empty($translation?->description))
                    <div class="prose prose-slate mt-4 max-w-none break-words">
                        {!! nl2br(e($translation->description)) !!}
                    </div>
                @else
                    <p class="mt-4 text-sm text-slate-600">{{ __('rentals.show.about_fallback') }}</p>
                @endif
            </div>

            <div>
                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <h3 class="text-sm font-semibold text-slate-900">Spesifikasi</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($specs as $s)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-[18px] leading-none text-emerald-700" aria-hidden="true">{{ $s['icon'] }}</span>
                                    <div class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ $s['label'] }}</div>
                                </div>
                                <div class="mt-2 font-semibold text-slate-900">{{ $s['value'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 rounded-3xl border border-emerald-100 bg-emerald-50 p-6">
                    <h3 class="text-sm font-semibold text-emerald-900">{{ __('rentals.show.need_recommendation') }}</h3>
                    <p class="mt-2 text-sm text-emerald-800">{{ __('rentals.show.need_recommendation_desc') }}</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ $contactUrl }}" class="inline-flex rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">{{ __('rentals.show.consult') }}</a>
                        @if (!empty($waUrl))
                            <a href="{{ $waUrl }}" target="_blank" rel="noopener" class="inline-flex rounded-full border border-emerald-200 bg-white px-6 py-3 text-sm font-semibold text-emerald-800">{{ __('rentals.show.whatsapp_short') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
