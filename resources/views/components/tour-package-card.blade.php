@props([
    'package',
])

@php
    $locale = app()->getLocale();
    $fallbackLocale = (string) config('app.fallback_locale', 'en');

    $translation = ($package->translations ?? collect())->firstWhere('language_code', $locale)
        ?? ($package->translations ?? collect())->firstWhere('language_code', $fallbackLocale)
        ?? ($package->translations ?? collect())->first();

    $linkLang = $translation?->language_code ?? $locale;

    $primaryImage = ($package->images ?? collect())->firstWhere('is_primary', true);
    $image = $primaryImage ?? ($package->images ?? collect())->sortBy('sort_order')->first();
    $imageUrl = $image?->url ?? null;

    $url = !empty($translation?->slug)
        ? route('tours.show', ['lang' => $linkLang, 'slug' => $translation->slug])
        : null;

    $pricing = is_array($package->pricing ?? null) ? $package->pricing : null;
    $currencyCode = strtoupper((string) ($pricing['currency_code'] ?? ($currentCurrency ?? 'IDR')));

    $currencySymbols = [
        'IDR' => 'Rp',
        'USD' => '$',
        'EUR' => '€',
        'SGD' => 'S$',
        'AUD' => 'A$',
    ];

    $symbol = ($activeCurrencies ?? collect())->firstWhere('code', $currencyCode)?->symbol
        ?? ($currencySymbols[$currencyCode] ?? $currencyCode);

    $amount = (float) ($pricing['selling_price_converted'] ?? 0);
    $formatted = ($currencyCode === 'IDR')
        ? number_format((int) round($amount), 0, ',', '.')
        : number_format($amount, 2, '.', ',');

    $chipDuration = trim((string) ($package->duration_days ?? '')) !== ''
        ? ((int) $package->duration_days . ' ' . __('pages.tours.filters.duration_day') . ' • ' . (int) ($package->duration_nights ?? 0) . ' ' . __('pages.tours.filters.duration_night'))
        : null;

    $chipPax = null;
    if (!empty($package->min_people) || !empty($package->max_people)) {
        $min = (int) ($package->min_people ?? 0);
        $max = (int) ($package->max_people ?? 0);
        $chipPax = $max > 0 ? ($min . '-' . $max . ' pax') : ($min . '+ pax');
    }

    $chipCategory = $package->category?->name ?? null;

    $title = $translation?->title ?? ($package->code ?? 'Tour Package');
    $summary = $translation?->summary ?? __('pages.tours.filters.card_summary_fallback');

    $cardClass = 'group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md';
    $disabledClass = 'overflow-hidden rounded-3xl border border-slate-200 bg-white opacity-70';
@endphp

@if ($url)
    <a href="{{ $url }}" class="{{ $cardClass }}">
        <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100">
            @if (!empty($imageUrl))
                <img src="{{ $imageUrl }}" alt="{{ $title }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.03]" loading="lazy" decoding="async" />
            @endif
        </div>

        <div class="p-5">
            <div class="text-lg font-semibold text-slate-900">{{ $title }}</div>
            <div class="mt-1 line-clamp-2 text-sm text-slate-600">{{ $summary }}</div>

            <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600">
                @if (!empty($chipCategory))
                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $chipCategory }}</span>
                @endif
                @if (!empty($chipDuration))
                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $chipDuration }}</span>
                @endif
                @if (!empty($chipPax))
                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $chipPax }}</span>
                @endif
            </div>

            <div class="mt-5 flex items-end justify-between gap-3">
                <div>
                    <div class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ __('pages.tours.cards.from') }}</div>
                    <div class="mt-1 text-lg font-semibold text-emerald-700">{{ $symbol }} {{ $formatted }}</div>
                    <div class="mt-1 text-xs text-slate-500">{{ __('pages.tours.cards.per_person') }}</div>
                </div>

                <span class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700">
                    <span>{{ __('pages.tours.cards.see_detail') }}</span>
                    <span class="material-symbols-outlined text-[20px] leading-none" aria-hidden="true">arrow_forward</span>
                </span>
            </div>
        </div>
    </a>
@else
    <div class="{{ $disabledClass }}">
        <div class="aspect-[4/3] w-full overflow-hidden bg-slate-100"></div>
        <div class="p-5">
            <div class="text-lg font-semibold text-slate-900">{{ $title }}</div>
            <div class="mt-3 text-sm text-slate-500">{{ __('pages.tours.filters.card_unavailable') }}</div>
        </div>
    </div>
@endif
