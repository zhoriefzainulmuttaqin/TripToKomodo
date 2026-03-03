@extends('layouts.app')

@php
    $hasAnyFilter = !empty($selectedCategory) || !empty($selectedDuration) || !empty($selectedDestinations);

    $baseTitle = __('pages.tours.page.title');
    $titleSuffixParts = [];

    if (!empty($selectedCategoryName ?? null)) {
        $titleSuffixParts[] = (string) $selectedCategoryName;
    } elseif (!empty($selectedCategory)) {
        $titleSuffixParts[] = (string) $selectedCategory;
    }

    if (!empty($selectedDuration)) {
        $titleSuffixParts[] = (string) $selectedDuration;
    }

    if (!empty($selectedDestinationNames ?? [])) {
        $titleSuffixParts[] = implode(', ', (array) $selectedDestinationNames);
    }

    $seoTitle = $baseTitle;
    if (!empty($titleSuffixParts)) {
        $seoTitle .= ' - ' . implode(' • ', $titleSuffixParts);
    }
@endphp

@section('title', $seoTitle)
@section('meta_description', __('pages.tours.page.meta'))
@section('meta_keywords', __('pages.tours.page.keywords'))

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-600">{{ __('pages.tours.hero.tag') }}</p>
                <h1 class="mt-3 text-3xl font-semibold text-slate-900">{{ __('pages.tours.hero.headline') }}</h1>
                <p class="mt-3 text-sm text-slate-600">{{ __('pages.tours.hero.sub') }}</p>

                @if ($hasAnyFilter)
                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if (!empty($selectedCategory))
                            <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">
                                {{ __('pages.tours.filters.chip_category', ['value' => ($selectedCategoryName ?? $selectedCategory)]) }}
                            </span>
                        @endif

                        @if (!empty($selectedDuration))
                            @php
                                $durParts = explode('-', (string) $selectedDuration);
                                $durLabel = (int) ($durParts[0] ?? 0) . ' ' . __('pages.tours.filters.duration_day') . ' • ' . (int) ($durParts[1] ?? 0) . ' ' . __('pages.tours.filters.duration_night');
                            @endphp
                            <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">
                                {{ __('pages.tours.filters.chip_duration', ['value' => $durLabel]) }}
                            </span>
                        @endif

                        @if (!empty($selectedDestinations))
                            <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">
                                {{ __('pages.tours.filters.chip_destination', ['value' => (!empty($selectedDestinationNames) ? implode(', ', $selectedDestinationNames) : (count($selectedDestinations) . ' dipilih'))]) }}
                            </span>
                        @endif

                        <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="text-xs text-slate-600 hover:text-emerald-700">Reset</a>
                    </div>
                @endif
            </div>
            <div class="rounded-full border border-slate-200 bg-white px-5 py-2 text-sm text-slate-600">{{ __('pages.tours.filters.total', ['count' => $packages->total()]) }}</div>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @foreach ($packages as $package)
                <x-tour-package-card :package="$package" />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $packages->links() }}
        </div>
    </section>
@endsection
