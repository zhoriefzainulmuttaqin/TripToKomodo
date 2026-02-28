@extends('layouts.app')

@section('title', 'Komodo Insider')
@section('meta_description', 'Komodo Insider: artikel, itinerary, dan insight terbaik untuk trip Labuan Bajo, Komodo, dan Flores.')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Komodo Insider</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">Blog & Insight</h1>
        <p class="mt-4 text-sm text-slate-600">Bagian ini akan berisi artikel (tips trip, itinerary, spot terbaik). Untuk sekarang masih tahap setup.</p>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @for ($i = 0; $i < 3; $i++)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Coming Soon</p>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900">Artikel Komodo Insider</h3>
                    <p class="mt-2 text-sm text-slate-600">Konten blog akan muncul di sini setelah admin menambahkan artikel.</p>
                </div>
            @endfor
        </div>
    </section>
@endsection
