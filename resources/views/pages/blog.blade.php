@extends('layouts.app')

@php $p = trans('pages.blog'); @endphp

@section('title', $p['page']['title'] ?? 'Komodo Insider')
@section('meta_description', $p['page']['meta'] ?? 'Komodo Insider: artikel, itinerary, dan insight terbaik untuk trip Labuan Bajo, Komodo, dan Flores.')
@section('meta_keywords', $p['page']['keywords'] ?? '')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $p['hero']['tag'] ?? 'Komodo Insider' }}</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">{{ $p['hero']['title'] ?? 'Blog & Insight' }}</h1>
        <p class="mt-4 text-sm text-slate-600">{{ $p['hero']['desc'] ?? '' }}</p>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @for ($i = 0; $i < 3; $i++)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">{{ $p['card']['tag'] ?? 'Coming Soon' }}</p>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $p['card']['title'] ?? '' }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $p['card']['desc'] ?? '' }}</p>
                </div>
            @endfor
        </div>
    </section>
@endsection
