@extends('layouts.app')

@section('title', 'Paket Trip Labuan Bajo')
@section('meta_description', 'Pilih paket trip Labuan Bajo terbaik dengan kapal premium dan itinerary eksklusif.')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">Paket Trip</p>
                <h1 class="mt-3 text-3xl font-semibold">Paket Labuan Bajo untuk semua gaya travel</h1>
                <p class="mt-3 text-sm text-slate-300">Pilih durasi, tipe kapal, dan pengalaman terbaik untuk trip Anda.</p>
            </div>
            <div class="rounded-full border border-white/10 bg-white/5 px-5 py-2 text-sm text-slate-300">Total paket: {{ $packages->total() }}</div>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($packages as $package)
                @php
                    $translation = $package->translations->first();
                    $image = $package->images->sortBy('sort_order')->first();
                @endphp
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5">
                    <div class="h-48 rounded-2xl bg-slate-800" style="background-image:url('{{ $image?->url }}'); background-size:cover; background-position:center;"></div>
                    <div class="mt-4">
                        <p class="text-xs text-emerald-300">{{ $package->duration_days }} hari • {{ $package->duration_nights }} malam</p>
                        <h3 class="mt-2 text-lg font-semibold">{{ $translation?->title ?? $package->code }}</h3>
                        <p class="mt-2 text-sm text-slate-300">{{ $translation?->summary ?? 'Paket trip eksklusif Labuan Bajo.' }}</p>
                        <a href="{{ route('tours.show', ['lang' => app()->getLocale(), 'slug' => $translation?->slug ?? $package->code]) }}" class="mt-4 inline-flex text-sm text-emerald-300 hover:text-emerald-200">Detail Paket →</a>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $packages->links() }}
        </div>
    </section>
@endsection
