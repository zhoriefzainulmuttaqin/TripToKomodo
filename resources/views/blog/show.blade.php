@extends('layouts.app')

@php
    $title = $post->meta_title ?: $post->title;

    $desc = $post->meta_description ?: ($post->excerpt ?? '');
    if (trim((string) $desc) === '') {
        $desc = \Illuminate\Support\Str::limit(trim(strip_tags((string) $contentHtml)), 160, '');
    }

    $ogTitle = !empty($post->og_title) ? $post->og_title : $title;
    $ogDesc = !empty($post->og_description) ? $post->og_description : $desc;
    $ogImage = $post->ogImageUrl();
@endphp

@section('title', $title)
@section('meta_description', $desc)
@section('meta_keywords', $post->meta_keywords ?? '')
@section('canonical', $canonical)
@section('og_type', 'article')
@section('og_title', $ogTitle)
@section('og_description', $ogDesc)
@section('og_image', !empty($ogImage) ? url($ogImage) : asset('favicon.ico'))


@if (!empty($post->meta_robots))
    @section('meta_robots', $post->meta_robots)
@endif

@section('hreflang')
    @foreach ($activeLanguages as $language)
        @php
            $href = null;
            $t = $translations->firstWhere('language_code', $language->code);
            if ($t) {
                $href = route('blog.show', ['lang' => $language->code, 'slug' => $t->slug]);
            } else {
                $href = route('blog.index', ['lang' => $language->code]);
            }
        @endphp
        <link rel="alternate" hreflang="{{ $language->code }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ route('blog.index', ['lang' => 'en']) }}">
@endsection

@push('schema')
    <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($articleSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @if (!empty($post->schema_json_ld))
        <script type="application/ld+json">{!! $post->schema_json_ld !!}</script>
    @endif
@endpush

@section('content')
    <article class="mx-auto max-w-3xl px-6 py-14">
        <a href="{{ route('blog.index', ['lang' => app()->getLocale()]) }}" class="text-sm text-emerald-700 hover:underline">← Komodo Insider</a>

        <h1 class="mt-4 text-4xl font-semibold text-slate-900">{{ $post->title }}</h1>
        <p class="mt-3 text-sm text-slate-500">
            {{ optional($post->published_at)->format('d M Y') }}
            @php
                $reading = $post->readingTimeMinutesComputed();
                $views = (int) ($post->view_count ?? 0);
            @endphp
            @if (!empty($reading))
                <span class="px-2">•</span>{{ $reading }} min read
            @endif
            <span class="px-2">•</span>{{ number_format($views) }} views
        </p>

        @if (!empty($post->featuredImageUrl()))
            <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">
                <img src="{{ $post->featuredImageUrl() }}" alt="{{ $post->title }}" class="h-full w-full object-cover" loading="eager" decoding="async" />
            </div>
        @endif

        @if (!empty($post->excerpt))
            <p class="mt-8 text-lg text-slate-700">{{ $post->excerpt }}</p>
        @endif

        <div class="prose prose-slate mt-10 max-w-none">
            {!! $contentHtml !!}
        </div>
    </article>
@endsection
