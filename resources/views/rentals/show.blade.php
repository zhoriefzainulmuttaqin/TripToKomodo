@extends('layouts.app')

@php
    $name = $translation?->name ?? 'Rental Car';
    $metaTitle = $translation?->meta_title ?: $name;
    $metaDesc = $translation?->meta_description ?: ($translation?->excerpt ?? '');
    $metaKeywords = $translation?->meta_keywords ?? '';

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
@endphp

@section('title', $metaTitle)
@section('meta_description', $metaDesc)
@section('meta_keywords', $metaKeywords)

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-10">
        <a href="{{ route('rental.mobil', ['lang' => app()->getLocale()]) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
            <span class="material-symbols-outlined text-[20px] leading-none" aria-hidden="true">arrow_back</span>
            <span>Kembali ke Rental Mobil</span>
        </a>

        <div class="mt-6 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <div>
                @if (!empty($car->image))
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white">
                        <img src="{{ $car->image }}" alt="{{ $name }}" class="h-[320px] w-full object-cover" loading="eager" decoding="async" />
                    </div>
                @endif

                <h1 class="mt-6 text-3xl font-semibold text-slate-900">{{ $name }}</h1>

                @if (!empty($translation?->excerpt))
                    <p class="mt-3 text-sm text-slate-600">{{ $translation->excerpt }}</p>
                @endif

                @if (!empty($translation?->description))
                    <div class="prose prose-slate mt-6 max-w-none">
                        {!! nl2br(e($translation->description)) !!}
                    </div>
                @endif
            </div>

            <aside class="space-y-4">
                <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-6">
                    <div class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-700">Harga</div>
                    <div class="mt-2 text-3xl font-semibold text-slate-900">{{ $symbol }} {{ $formatted }}</div>
                    <div class="mt-1 text-sm text-emerald-800">per hari</div>

                    <a href="{{ route('home', ['lang' => app()->getLocale()]) }}#contact" class="mt-5 inline-flex w-full justify-center rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Booking / Tanya Unit</a>
                    @if (!empty($contactSettings['whatsapp_url'] ?? null))
                        <a href="{{ $contactSettings['whatsapp_url'] }}" target="_blank" rel="noopener" class="mt-3 inline-flex w-full justify-center rounded-full border border-emerald-200 bg-white px-6 py-3 text-sm font-semibold text-emerald-800">Chat WhatsApp</a>
                    @endif
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6">
                    <div class="text-sm font-semibold text-slate-900">Spesifikasi</div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-[0.25em] text-slate-500">Seats</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $car->seats ?? '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-[0.25em] text-slate-500">Luggage</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $car->luggage ?? '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-[0.25em] text-slate-500">Transmission</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $car->transmission ?? '—' }}</dd>
                        </div>
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <dt class="text-xs uppercase tracking-[0.25em] text-slate-500">Fuel</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $car->fuel ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>
    </section>
@endsection
