@extends('layouts.app')

@php
    $about = trans('about');
    $cms = (isset($cmsAbout) && is_array($cmsAbout)) ? $cmsAbout : [];
    if (!empty($cms)) {
        $about = array_replace_recursive($about, $cms);
    }
@endphp

@section('title', $about['page']['title'] ?? 'Tentang Kami')
@section('meta_description', $about['page']['meta'] ?? 'Kenali tim dan pengalaman TriptoKomodo sebagai operator tur Labuan Bajo.')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $about['hero']['tag'] ?? 'About Us' }}</p>
                <h1 class="mt-3 text-4xl font-semibold text-slate-900 lg:text-5xl">{{ $about['hero']['headline'] ?? '' }}</h1>
                <p class="mt-4 text-base text-slate-600">{{ $about['hero']['subheadline'] ?? '' }}</p>

                <div class="mt-6 space-y-4 text-sm text-slate-600">
                    @foreach (($about['hero']['lead'] ?? []) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-3">
                    @foreach (($about['stats'] ?? []) as $stat)
                        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-4 text-center shadow-sm">
                            <p class="text-2xl font-semibold text-emerald-700">{{ $stat['value'] ?? '' }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $stat['label'] ?? '' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-6 top-10 h-28 w-28 rounded-full bg-emerald-100 blur-2xl"></div>
                <div class="absolute -right-6 bottom-10 h-28 w-28 rounded-full bg-emerald-200/60 blur-2xl"></div>
                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="relative aspect-[4/5] w-full bg-gradient-to-br from-emerald-50 via-slate-50 to-emerald-100">
                        <img
                            src="{{ $about['hero']['image'] ?? '' }}"
                            alt="{{ $about['hero']['image_alt'] ?? 'About TriptoKomodo' }}"
                            class="absolute inset-0 h-full w-full object-cover"
                            onerror="this.style.display='none'"
                        >
                        <div class="absolute inset-0 flex flex-col items-start justify-end p-6">
                            <div class="rounded-2xl bg-white/90 p-4 text-sm text-slate-700 shadow-sm">
                                <p class="text-xs uppercase tracking-[0.2em] text-emerald-600">{{ $about['hero']['badge'] ?? 'Labuan Bajo' }}</p>
                                <p class="mt-2 text-base font-semibold text-slate-900">{{ $about['hero']['badge_title'] ?? '' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $about['hero']['badge_desc'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-16">
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $about['vision']['tag'] ?? 'Vision' }}</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $about['vision']['title'] ?? '' }}</h2>
                <p class="mt-3 text-sm text-slate-600">{{ $about['vision']['body'] ?? '' }}</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $about['mission']['tag'] ?? 'Mission' }}</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $about['mission']['title'] ?? '' }}</h2>
                <p class="mt-3 text-sm text-slate-600">{{ $about['mission']['body'] ?? '' }}</p>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-16">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $about['values']['tag'] ?? 'Our Promise' }}</p>
                    <h2 class="mt-2 text-2xl font-semibold text-slate-900">{{ $about['values']['title'] ?? '' }}</h2>
                </div>
                <p class="text-sm text-slate-500">{{ $about['values']['desc'] ?? '' }}</p>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-3">
                @foreach (($about['values']['items'] ?? []) as $item)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-sm font-semibold text-slate-900">{{ $item['title'] ?? '' }}</p>
                        <p class="mt-2 text-xs text-slate-600">{{ $item['desc'] ?? '' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 pb-20">
        <div class="grid gap-6 md:grid-cols-2">
            @foreach (($about['highlights'] ?? []) as $item)
                <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-6">
                    <p class="text-sm font-semibold text-emerald-800">{{ $item['title'] ?? '' }}</p>
                    <p class="mt-2 text-xs text-emerald-900/80">{{ $item['desc'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </section>
@endsection
