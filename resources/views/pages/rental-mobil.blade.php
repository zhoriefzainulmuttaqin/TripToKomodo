@extends('layouts.app')

@php
    $p = trans('pages.rental');

    $cms = (isset($cmsRental) && is_array($cmsRental)) ? $cmsRental : [];
    if (!empty($cms)) {
        $p = array_replace_recursive($p, $cms);
    }
@endphp

@section('title', $p['page']['title'] ?? 'Rental Mobil Labuan Bajo')
@section('meta_description', $p['page']['meta'] ?? 'Rental mobil Labuan Bajo: driver profesional, unit nyaman, dan itinerary fleksibel untuk trip Flores & sekitarnya.')
@section('meta_keywords', $p['page']['keywords'] ?? '')

@section('content')
    @php
        $cars = $cars ?? collect();

        $currencyCode = strtoupper((string) ($currentCurrency ?? 'IDR'));
        $currencySymbols = [
            'IDR' => 'Rp',
            'USD' => '$',
            'EUR' => '€',
            'SGD' => 'S$',
            'AUD' => 'A$',
        ];
        $symbol = $activeCurrencies->firstWhere('code', $currencyCode)?->symbol
            ?? ($currencySymbols[$currencyCode] ?? $currencyCode);

        $carsUi = $p['cars'] ?? [];
    @endphp

    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $p['hero']['tag'] ?? 'Rental' }}</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">{{ $p['hero']['title'] ?? 'Rental Mobil Labuan Bajo' }}</h1>
        <p class="mt-4 text-sm text-slate-600">{{ $p['hero']['desc'] ?? '' }}</p>

        <div class="mt-12">
            <div class="flex items-end justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">{{ $carsUi['title'] ?? 'Pilihan Mobil' }}</h2>
                    @if (!empty($carsUi['subtitle'] ?? null))
                        <p class="mt-2 text-sm text-slate-600">{{ $carsUi['subtitle'] }}</p>
                    @endif
                </div>
            </div>

            @if (($cars ?? collect())->isEmpty())
                <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-8 text-sm text-slate-600">
                    {{ $carsUi['empty'] ?? 'Belum ada unit rental yang ditampilkan.' }}
                </div>
            @else
                <div class="mt-6 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($cars as $car)
                        @php
                            $t = $car->display_translation ?? null;
                            $name = $t?->name ?? 'Rental Car';
                            $slug = $t?->slug ?? '';
                            $pricing = is_array($car->pricing ?? null) ? $car->pricing : [];

                            $pricingCurrencyCode = strtoupper((string) ($pricing['currency_code'] ?? $currencyCode));
                            $priceSymbol = $activeCurrencies->firstWhere('code', $pricingCurrencyCode)?->symbol
                                ?? ($currencySymbols[$pricingCurrencyCode] ?? $pricingCurrencyCode);

                            $amount = (float) ($pricing['price_per_day_converted'] ?? 0);
                            $formatted = ($pricingCurrencyCode === 'IDR')
                                ? number_format((int) round($amount), 0, ',', '.')
                                : number_format($amount, 2, '.', ',');
                        @endphp

                        @php $carUrl = $slug !== '' ? route('rental.mobil.show', ['lang' => app()->getLocale(), 'slug' => $slug]) : null; @endphp
                        @if ($carUrl)
                        <a href="{{ $carUrl }}" class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                @if (!empty($car->image))
                                    <img src="{{ $car->image }}" alt="{{ $name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" decoding="async" />
                                @endif
                            </div>

                            <div class="p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="text-lg font-semibold text-slate-900">{{ $name }}</div>
                                        @if (!empty($t?->excerpt))
                                            <div class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $t->excerpt }}</div>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600">
                                    @if (!empty($car->transmission))
                                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $car->transmission }}</span>
                                    @endif
                                    @if (!empty($car->fuel))
                                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $car->fuel }}</span>
                                    @endif
                                    @if (!empty($car->seats))
                                        <span class="rounded-full bg-slate-100 px-3 py-1">{{ $car->seats }} seats</span>
                                    @endif
                                </div>

                                <div class="mt-5 flex items-end justify-between gap-3">
                                    <div>
                                        <div class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ $carsUi['from'] ?? 'Mulai' }}</div>
                                        <div class="mt-1 text-lg font-semibold text-emerald-700">{{ $priceSymbol }} {{ $formatted }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ $carsUi['per_day'] ?? 'per hari' }}</div>
                                    </div>

                                    <span class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700">
                                        <span>{{ $carsUi['see_detail'] ?? 'Lihat Detail' }}</span>
                                        <span class="material-symbols-outlined text-[20px] leading-none" aria-hidden="true">arrow_forward</span>
                                    </span>
                                </div>
                            </div>
                        </a>
                        @else
                        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm opacity-70">
                            <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
                                @if (!empty($car->image))
                                    <img src="{{ $car->image }}" alt="{{ $name }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                                @endif
                            </div>
                            <div class="p-5">
                                <div class="text-lg font-semibold text-slate-900">{{ $name }}</div>
                                <div class="mt-3 text-sm text-slate-500">{{ $carsUi['unavailable'] ?? 'Unit tidak tersedia.' }}</div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-14 rounded-3xl border border-emerald-100 bg-emerald-50 p-8">
            <h2 class="text-xl font-semibold text-slate-900">{{ $p['cta']['title'] ?? 'Butuh rekomendasi cepat?' }}</h2>
            <p class="mt-2 text-sm text-emerald-800">{{ $p['cta']['desc'] ?? '' }}</p>
            <a href="{{ route('home', ['lang' => app()->getLocale()]) }}#contact" class="mt-6 inline-flex rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">{{ $p['cta']['button'] ?? 'Konsultasi' }}</a>
        </div>
    </section>
@endsection
