@extends('layouts.app')

@section('title', 'Paket Trip Labuan Bajo')
@section('meta_description', 'Pilih paket trip Labuan Bajo terbaik dengan kapal premium dan itinerary eksklusif.')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-600">Paket Trip</p>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">Paket Labuan Bajo untuk semua gaya travel</h1>
                <p class="mt-3 text-sm text-slate-600">Pilih durasi, tipe kapal, dan pengalaman terbaik untuk trip Anda.</p>

                @if (!empty($selectedCategory))
                    <div class="mt-3">
                        <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">
                            Filter kategori: {{ $selectedCategory }}
                        </span>
                        <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="ml-2 text-xs text-slate-600 hover:text-emerald-700">Hapus filter</a>
                    </div>
                @endif
            </div>
            <div class="rounded-full border border-slate-200 bg-white px-5 py-2 text-sm text-slate-600">Total paket: {{ $packages->total() }}</div>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($packages as $package)
                @php
                    $locale = app()->getLocale();
                    $fallbackLocale = config('app.fallback_locale', 'en');

                    $translation = $package->translations->firstWhere('language_code', $locale)
                        ?? $package->translations->firstWhere('language_code', $fallbackLocale)
                        ?? $package->translations->first();

                    $linkLang = $translation?->language_code ?? $locale;

                    $primaryImage = $package->images->firstWhere('is_primary', true);
                    $image = $primaryImage ?? $package->images->sortBy('sort_order')->first();
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="h-48 rounded-2xl bg-slate-100" style="background-image:url('{{ $image?->url }}'); background-size:cover; background-position:center;"></div>
                    <div class="mt-4">
                        <p class="text-xs text-emerald-600">{{ $package->duration_days }} hari • {{ $package->duration_nights }} malam</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $translation?->title ?? $package->code }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $translation?->summary ?? 'Paket trip eksklusif Labuan Bajo.' }}</p>
                        @if (!empty($translation?->slug))
                            <a href="{{ route('tours.show', ['lang' => $linkLang, 'slug' => $translation->slug]) }}" class="mt-4 inline-flex text-sm text-emerald-700 hover:text-emerald-800">Detail Paket →</a>
                        @else
                            <span class="mt-4 inline-flex text-sm text-slate-400">Detail belum tersedia</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $packages->links() }}
        </div>
    </section>
@endsection
