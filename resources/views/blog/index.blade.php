@extends('layouts.app')

@php
    $p = trans('pages.blog');
@endphp

@section('title', $p['page']['title'] ?? 'Komodo Insider')
@section('meta_description', $p['page']['meta'] ?? 'Komodo Insider: artikel, itinerary, dan insight terbaik untuk trip Labuan Bajo, Komodo, dan Flores.')
@section('meta_keywords', $p['page']['keywords'] ?? '')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $p['hero']['tag'] ?? 'Komodo Insider' }}</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">{{ $p['hero']['title'] ?? 'Blog & Insight' }}</h1>
        <p class="mt-4 text-sm text-slate-600">{{ $p['hero']['desc'] ?? '' }}</p>

        @if ($posts->isEmpty())
            <div class="mt-10 rounded-3xl border border-slate-200 bg-white p-8 text-sm text-slate-600 shadow-sm">
                Belum ada artikel untuk bahasa ini.
            </div>
        @else
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    @php
                        $img = $post->featuredImageUrl();
                    @endphp
                    <a href="{{ route('blog.show', ['lang' => app()->getLocale(), 'slug' => $post->slug]) }}" class="group overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5">
                        <div class="aspect-[16/10] w-full bg-slate-100">
                            @if (!empty($img))
                                <img src="{{ $img }}" alt="{{ $post->title }}" class="h-full w-full object-cover" loading="lazy" decoding="async" />
                            @endif
                        </div>
                        <div class="p-6">
                            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">{{ $p['card']['tag'] ?? 'Komodo Insider' }}</p>
                            <h3 class="mt-3 text-lg font-semibold text-slate-900 group-hover:text-emerald-700">{{ $post->title }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $post->excerpt ?? '' }}</p>
                            <p class="mt-4 text-xs text-slate-500">
                                {{ optional($post->published_at)->format('d M Y') }}
                                @php
                                    $reading = $post->readingTimeMinutesComputed();
                                    $views = (int) ($post->view_count ?? 0);
                                @endphp
                                @if (!empty($reading))
                                    <span class="px-1.5">•</span>{{ $reading }} min read
                                @endif
                                <span class="px-1.5">•</span>{{ number_format($views) }} views
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
@endsection
