@extends('layouts.app')

@php
    $t = trans('home');

    // About snippet (merge translation + CMS override)
    $about = trans('about');
    $cms = (isset($cmsAbout) && is_array($cmsAbout)) ? $cmsAbout : [];
    if (!empty($cms)) {
        $about = array_replace_recursive($about, $cms);
    }
@endphp

@section('title', $t['page']['title'])
@section('meta_description', $t['page']['meta'])

@section('hreflang')
    @foreach ($activeLanguages as $language)
        <link rel="alternate" hreflang="{{ $language->code }}" href="{{ url($language->code) }}">
    @endforeach
@endsection

@section('content')
    @push('schema')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @endpush
    @push('styles')
        <style>
            /* Alpine.js cloak */
            [x-cloak] { display: none !important; }
            /* Trip Finder Smooth Styling */
            .trip-finder-card {
                animation: tripFinderFadeIn 0.6s ease-out;
            }
            @keyframes tripFinderFadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .trip-finder-field {
                animation: fieldSlideIn 0.5s ease-out backwards;
            }
            .trip-finder-field:nth-child(1) { animation-delay: 0.1s; }
            .trip-finder-field:nth-child(2) { animation-delay: 0.2s; }
            .trip-finder-field:nth-child(3) { animation-delay: 0.3s; }
            @keyframes fieldSlideIn {
                from { opacity: 0; transform: translateX(-10px); }
                to { opacity: 1; transform: translateX(0); }
            }
            /* Custom Scrollbar */
            .scrollbar-thin {
                scrollbar-width: thin;
                scrollbar-color: #a7f3d0 transparent;
            }
            .scrollbar-thin::-webkit-scrollbar {
                width: 6px;
            }
            .scrollbar-thin::-webkit-scrollbar-track {
                background: transparent;
                border-radius: 3px;
            }
            .scrollbar-thin::-webkit-scrollbar-thumb {
                background: #a7f3d0;
                border-radius: 3px;
                transition: background 0.3s ease;
            }
            .scrollbar-thin::-webkit-scrollbar-thumb:hover {
                background: #34d399;
            }
            /* Select dropdown animation */
            .trip-finder-select {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .trip-finder-select option {
                padding: 8px 12px;
            }
            /* Map popup styling */
            .destination-popup .leaflet-popup-content-wrapper {
                border-radius: 16px;
                box-shadow: 0 10px 40px -10px rgba(0,0,0,0.2);
                padding: 0;
                overflow: hidden;
            }
            .destination-popup .leaflet-popup-content {
                margin: 12px;
                line-height: 1.5;
            }
            .destination-popup .leaflet-popup-tip {
                background: white;
            }
            .destination-popup img {
                border-radius: 8px;
                width: 100%;
                height: 120px;
                object-fit: cover;
            }
        </style>
    @endpush
    @php
        $weatherData = $weather ?? null;
        $weatherToday = is_array($weatherData) ? ($weatherData['today'] ?? null) : null;
        $weatherCurrent = is_array($weatherData) ? ($weatherData['current'] ?? null) : null;
        $weatherDaily = is_array($weatherData) ? ($weatherData['daily'] ?? []) : [];
        $mapDestinations = $destinations ?? collect();
    @endphp

    <section class="relative overflow-hidden bg-white">
        @if (!empty($heroBackgroundUrl))
            <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ $heroBackgroundUrl }}');"></div>
            <div class="absolute inset-0 bg-slate-900/35"></div>
        @endif
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.18),transparent_65%)]"></div>
        <div class="relative mx-auto max-w-6xl px-6 py-20">
            <div class="grid gap-12 lg:grid-cols-[1.3fr_0.7fr]">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $t['hero']['tag'] }}</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">{{ $t['hero']['headline'] }}</h1>
                    <p class="mt-5 text-base text-slate-600">{{ $t['hero']['sub'] }}</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">{{ $t['hero']['cta_primary'] }}</a>
                        <a href="#contact" class="rounded-full border border-emerald-200 px-6 py-3 text-sm text-emerald-700">{{ $t['hero']['cta_secondary'] }}</a>
                    </div>
                    <div class="mt-10 grid gap-4 text-xs text-slate-600 md:grid-cols-3">
                        @foreach ($t['hero']['stats'] as $stat)
                            <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                                <p class="text-lg font-semibold text-emerald-700">{{ $stat['value'] }}</p>
                                <p>{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="trip-finder-card rounded-3xl border border-emerald-100/80 bg-white/95 p-6 shadow-lg shadow-emerald-900/5 backdrop-blur-sm transition-all duration-500 hover:shadow-xl hover:shadow-emerald-900/10">
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">{{ $t['trip_finder']['tag'] }}</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-900">{{ $t['trip_finder']['title'] }}</h2>

                    @php
                        $durations = collect($filterDurations ?? [])->map(fn($d) => [
                            'value' => (int) $d->duration_days . '-' . (int) $d->duration_nights,
                            'label' => (int) $d->duration_days . ' ' . $t['trip_finder']['duration_day'] . ' • ' . (int) $d->duration_nights . ' ' . $t['trip_finder']['duration_night']
                        ])->values();
                        $categories = collect($filterCategories ?? [])->map(fn($c) => [
                            'value' => $c->slug,
                            'label' => $c->name
                        ])->values();
                        $tripFinderDestinations = collect($filterDestinations ?? [])->map(fn($d) => [
                            'value' => $d->id,
                            'label' => $d->name
                        ])->values();
                    @endphp

                    <form x-data="{
                        durOpen: false,
                        catOpen: false,
                        destOpen: false,
                        duration: '{{ request('duration', '') }}',
                        category: '{{ request('category', '') }}',
                        destination: '{{ request('destination', '') }}',
                        durationLabel: '{{ request('duration') ? collect($filterDurations ?? [])->first(fn($d) => ((int)$d->duration_days.'-'.(int)$d->duration_nights) == request('duration'))? ((int)collect($filterDurations ?? [])->first(fn($d) => ((int)$d->duration_days.'-'.(int)$d->duration_nights) == request('duration'))->duration_days.' '.$t['trip_finder']['duration_day'].' • '.(int)collect($filterDurations ?? [])->first(fn($d) => ((int)$d->duration_days.'-'.(int)$d->duration_nights) == request('duration'))->duration_nights.' '.$t['trip_finder']['duration_night']) : $t['trip_finder']['duration_all'] : $t['trip_finder']['duration_all'] }}',
                        categoryLabel: '{{ request('category') ? ($filterCategories->firstWhere('slug', request('category'))->name ?? $t['trip_finder']['category_all']) : $t['trip_finder']['category_all'] }}',
                        destinationLabel: '{{ request('destination') ? ($filterDestinations->firstWhere('id', request('destination'))->name ?? $t['trip_finder']['destination_all']) : $t['trip_finder']['destination_all'] }}',
                        setDuration(val, label) { this.duration = val; this.durationLabel = label; this.durOpen = false; },
                        setCategory(val, label) { this.category = val; this.categoryLabel = label; this.catOpen = false; },
                        setDestination(val, label) { this.destination = val; this.destinationLabel = label; this.destOpen = false; },
                    }" method="GET" action="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="mt-5 space-y-3 text-sm">
                        {{-- Durasi --}}
                        <div class="trip-finder-field rounded-2xl border border-slate-200/80 bg-white p-4 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/5">
                            <label class="block text-xs font-medium text-slate-500" for="tripfinder-duration">{{ $t['trip_finder']['duration'] }}</label>
                            <div class="relative mt-2" @click.outside="durOpen = false">
                                <button type="button" @click="durOpen = !durOpen" class="flex w-full items-center justify-between rounded-xl border-0 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none ring-1 ring-slate-200 transition-all duration-300 hover:bg-white hover:ring-emerald-300 focus:ring-2 focus:ring-emerald-500">
                                    <span x-text="durationLabel"></span>
                                    <svg :class="{ 'rotate-180 text-emerald-500': durOpen }" class="h-4 w-4 text-slate-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="duration" :value="duration">
                                <div x-cloak x-show="durOpen" x-transition class="absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                                    <a @click.prevent="setDuration('', '{{ $t['trip_finder']['duration_all'] }}')" href="#" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800" :class="{ 'bg-emerald-50 text-emerald-800': duration === '' }">{{ $t['trip_finder']['duration_all'] }}</a>
                                    @foreach ($durations as $d)
                                        <a @click.prevent="setDuration('{{ $d['value'] }}', '{{ $d['label'] }}')" href="#" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800" :class="{ 'bg-emerald-50 text-emerald-800': duration === '{{ $d['value'] }}' }">{{ $d['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Tipe Kapal --}}
                        <div class="trip-finder-field rounded-2xl border border-slate-200/80 bg-white p-4 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/5">
                            <label class="block text-xs font-medium text-slate-500" for="tripfinder-category">{{ $t['trip_finder']['category'] }}</label>
                            <div class="relative mt-2" @click.outside="catOpen = false">
                                <button type="button" @click="catOpen = !catOpen" class="flex w-full items-center justify-between rounded-xl border-0 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none ring-1 ring-slate-200 transition-all duration-300 hover:bg-white hover:ring-emerald-300 focus:ring-2 focus:ring-emerald-500">
                                    <span x-text="categoryLabel"></span>
                                    <svg :class="{ 'rotate-180 text-emerald-500': catOpen }" class="h-4 w-4 text-slate-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="category" :value="category">
                                <div x-cloak x-show="catOpen" x-transition class="absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                                    <a @click.prevent="setCategory('', '{{ $t['trip_finder']['category_all'] }}')" href="#" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800" :class="{ 'bg-emerald-50 text-emerald-800': category === '' }">{{ $t['trip_finder']['category_all'] }}</a>
                                    @foreach ($categories as $c)
                                        <a @click.prevent="setCategory('{{ $c['value'] }}', '{{ $c['label'] }}')" href="#" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800" :class="{ 'bg-emerald-50 text-emerald-800': category === '{{ $c['value'] }}' }">{{ $c['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Destinasi --}}
                        <div class="trip-finder-field rounded-2xl border border-slate-200/80 bg-white p-4 transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/5">
                            <label class="block text-xs font-medium text-slate-500" for="tripfinder-destination">{{ $t['trip_finder']['destination'] }}</label>
                            <div class="relative mt-2" @click.outside="destOpen = false">
                                <button type="button" @click="destOpen = !destOpen" class="flex w-full items-center justify-between rounded-xl border-0 bg-slate-50 px-4 py-2.5 text-sm text-slate-700 outline-none ring-1 ring-slate-200 transition-all duration-300 hover:bg-white hover:ring-emerald-300 focus:ring-2 focus:ring-emerald-500">
                                    <span x-text="destinationLabel"></span>
                                    <svg :class="{ 'rotate-180 text-emerald-500': destOpen }" class="h-4 w-4 text-slate-400 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <input type="hidden" name="destination" :value="destination">
                                <div x-cloak x-show="destOpen" x-transition class="absolute z-20 mt-2 w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                                    <a @click.prevent="setDestination('', '{{ $t['trip_finder']['destination_all'] }}')" href="#" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800" :class="{ 'bg-emerald-50 text-emerald-800': destination === '' }">{{ $t['trip_finder']['destination_all'] }}</a>
                                    @foreach ($tripFinderDestinations as $d)
                                        <a @click.prevent="setDestination('{{ $d['value'] }}', '{{ $d['label'] }}')" href="#" class="block px-4 py-3 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-800" :class="{ 'bg-emerald-50 text-emerald-800': destination === '{{ $d['value'] }}' }">{{ $d['label'] }}</a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Buttons --}}
                        <div class="space-y-3 pt-2">
                            <button type="submit" class="group relative w-full overflow-hidden rounded-full bg-emerald-600 px-5 py-3 text-center text-sm font-semibold text-white shadow-lg shadow-emerald-600/30 transition-all duration-300 hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-600/40 hover:-translate-y-0.5 active:translate-y-0">
                                <span class="relative z-10 flex items-center justify-center gap-2">
                                    {{ $t['trip_finder']['submit'] }}
                                    <svg class="h-4 w-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </span>
                            </button>

                            <a href="#contact" class="group relative block w-full overflow-hidden rounded-full border border-emerald-200 bg-white px-5 py-3 text-center text-sm font-semibold text-emerald-700 shadow-sm transition-all duration-300 hover:border-emerald-300 hover:bg-emerald-50/80 hover:shadow-md hover:-translate-y-0.5 active:translate-y-0">
                                <span class="flex items-center justify-center gap-2">
                                    {{ $t['trip_finder']['consult'] }}
                                    <svg class="h-4 w-4 transition-all duration-300 group-hover:translate-x-1 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $about['hero']['tag'] ?? 'About Us' }}</p>
                <h2 class="mt-4 text-3xl font-semibold leading-tight text-slate-900 md:text-4xl">{{ $about['hero']['headline'] ?? '' }}</h2>
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

                <div class="mt-6 flex">
                    <a href="{{ route('about', ['lang' => app()->getLocale()]) }}" class="inline-flex items-center justify-center rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        {{ trans('nav.menu_about') }}
                    </a>
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
                            loading="lazy"
                            decoding="async"
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

    <section class="mx-auto max-w-7xl px-6 py-16">
        <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $t['map']['tag'] }}</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $t['map']['title'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $t['map']['desc'] }}</p>
                </div>
                <span class="rounded-full border border-emerald-100 bg-emerald-50 px-4 py-2 text-xs text-emerald-700">{{ $t['map']['badge'] }}</span>
            </div>
            <div class="mt-6 overflow-hidden rounded-2xl border border-emerald-100 bg-emerald-50">
                <div id="komodo-map" class="h-[500px] w-full lg:h-[600px]"></div>
            </div>
            <p class="mt-3 text-xs text-slate-500">{{ $t['map']['note'] }}</p>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const mapContainer = document.getElementById('komodo-map');
                if (!mapContainer) return;

                // Debug: Check if Leaflet is loaded
                if (typeof L === 'undefined') {
                    console.error('Leaflet library is not loaded');
                    mapContainer.innerHTML = `<div class="flex h-full items-center justify-center text-red-500">{{ $t['map']['error'] }}</div>`;
                    return;
                }

                // Default bounds for Komodo & Flores
                const defaultBounds = [
                    [-9.0, 119.3],
                    [-8.2, 120.5],
                ];

                const map = L.map(mapContainer, {
                    scrollWheelZoom: false,
                    zoomControl: false,
                    attributionControl: false,
                }).fitBounds(defaultBounds);

                L.control.zoom({ position: 'topleft' }).addTo(map);
                L.control.attribution({ prefix: false }).addAttribution('&copy; OpenStreetMap contributors').addTo(map);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                }).addTo(map);

                // Fix map sizing after layout render
                setTimeout(() => {
                    map.invalidateSize();
                }, 300);

                // Safe parsing of destinations
                let rawDestinations = @json($mapDestinations);

                // Ensure it's an array (handle if it comes as object/collection)
                const destinations = Array.isArray(rawDestinations)
                    ? rawDestinations
                    : Object.values(rawDestinations || {});

                console.log('Destinations loaded:', destinations.length);

                if (destinations.length > 0) {
                    const markers = L.featureGroup();

                    destinations.forEach((destination) => {
                        const rawLat = (destination.lat ?? '').toString().replace(',', '.');
                        const rawLng = (destination.lng ?? '').toString().replace(',', '.');
                        const lat = parseFloat(rawLat);
                        const lng = parseFloat(rawLng);

                        // Validate coordinates
                        if (!isNaN(lat) && !isNaN(lng)) {
                            // Create point marker
                            const marker = L.circleMarker([lat, lng], {
                                radius: 8,
                                color: '#ffffff',
                                weight: 2,
                                fillColor: '#10b981', // Emerald-500
                                fillOpacity: 1,
                            });

                            // Popup Content
                            let popupHtml = `<div class="min-w-[200px] max-w-[240px]">`;

                            if (destination.image) {
                                popupHtml += `<div class="mb-3 h-32 w-full overflow-hidden rounded-lg bg-slate-100">
                                    <img src="/storage/${destination.image}" alt="${destination.name}" class="h-full w-full object-cover">
                                </div>`;
                            }

                            popupHtml += `<div class="flex items-start justify-between gap-2">
                                <h3 class="font-bold text-slate-900 text-sm leading-tight">${destination.name}</h3>
                                ${destination.category ? `<span class="shrink-0 rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-medium text-emerald-700 border border-emerald-100">${destination.category}</span>` : ''}
                            </div>`;

                            if (destination.description) {
                                const desc = destination.description.length > 100
                                    ? destination.description.substring(0, 100) + '...'
                                    : destination.description;
                                popupHtml += `<p class="mt-2 text-xs text-slate-600 leading-relaxed">${desc}</p>`;
                            }

                            if (destination.distance) {
                                popupHtml += `<div class="mt-2 flex items-center gap-1 text-[10px] text-slate-400">
                                    <span>📍</span> ${destination.distance}
                                </div>`;
                            }

                            popupHtml += `</div>`;

                            marker.bindPopup(popupHtml, {
                                closeButton: false,
                                className: 'destination-popup',
                                minWidth: 220
                            });

                            // Hover effects
                            marker.on('mouseover', function() {
                                this.setStyle({ radius: 10, fillColor: '#059669' }); // Darker on hover
                                this.openPopup();
                            });

                            // Optional: Close popup on mouseout? User requested "klik muncul", so click is better.
                            // But maybe we keep it persistent on click.

                            marker.addTo(markers);
                        }
                    });

                    // Add markers to map
                    if (markers.getLayers().length > 0) {
                        markers.addTo(map);
                        map.fitBounds(markers.getBounds().pad(0.1));
                    }
                }
            });
        </script>
    </section>

    <section id="experiences" class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $t['experiences']['tag'] }}</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900">{{ $t['experiences']['title'] }}</h2>
                <p class="mt-3 text-sm text-slate-600">{{ $t['experiences']['desc'] }}</p>
            </div>
            <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="text-sm text-emerald-700 hover:text-emerald-800">{{ $t['experiences']['link'] }}</a>
        </div>

        <div class="mt-10 grid gap-6 md:grid-cols-3">
            @forelse ($packages as $package)
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
                        <p class="text-xs text-emerald-600">{{ $package->duration_days }} {{ $t['experiences']['duration_unit'] }}</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $translation?->title ?? $package->code }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $translation?->summary ?? $t['experiences']['summary_fallback'] }}</p>
                        @if (!empty($translation?->slug))
                            <a href="{{ route('tours.show', ['lang' => $linkLang, 'slug' => $translation->slug]) }}" class="mt-4 inline-flex text-sm text-emerald-700 hover:text-emerald-800">{{ $t['experiences']['detail'] }}</a>
                        @else
                            <span class="mt-4 inline-flex text-sm text-slate-400">{{ $t['experiences']['detail_empty'] }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-200 bg-white p-8 text-sm text-slate-600">
                    {{ $t['experiences']['empty'] }}
                </div>
            @endforelse
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            @php
                $weatherUpdatedAt = null;
                try {
                    $weatherUpdatedAt = is_array($weatherData) && !empty($weatherData['fetched_at'])
                        ? \Carbon\CarbonImmutable::parse($weatherData['fetched_at'])->setTimezone('Asia/Makassar')->format('d M Y H:i') . ' WITA'
                        : null;
                } catch (\Throwable) {
                    $weatherUpdatedAt = null;
                }

                $scene = 'cloudy';
                if (is_array($weatherCurrent) && !empty($weatherCurrent['scene'])) {
                    $scene = $weatherCurrent['scene'];
                } elseif (is_array($weatherToday) && !empty($weatherToday['scene'])) {
                    $scene = $weatherToday['scene'];
                }

                $todayTemp = is_array($weatherCurrent) && isset($weatherCurrent['temperature'])
                    ? $weatherCurrent['temperature']
                    : (is_array($weatherToday) ? ($weatherToday['temp_max'] ?? null) : null);
                $todayMax = is_array($weatherToday) ? ($weatherToday['temp_max'] ?? null) : null;
                $todayMin = is_array($weatherToday) ? ($weatherToday['temp_min'] ?? null) : null;
                $todayPop = is_array($weatherToday) ? ($weatherToday['precipitation_probability_max'] ?? null) : null;
                $todayDow = is_array($weatherToday) ? ($weatherToday['dow'] ?? null) : null;
                $todayStatusKey = is_array($weatherCurrent) ? ($weatherCurrent['status_key'] ?? null) : null;
                if (!$todayStatusKey && is_array($weatherToday)) {
                    $todayStatusKey = $weatherToday['status_key'] ?? null;
                }
                $todayStatusText = $todayStatusKey
                    ? ($t['weather']['status'][$todayStatusKey] ?? $t['weather']['status_unknown'])
                    : $t['weather']['status_unknown'];

                $todayRangeText = ($todayMax !== null || $todayMin !== null)
                    ? $t['weather']['max'] . ' ' . ($todayMax ?? '--') . '° • ' . $t['weather']['min'] . ' ' . ($todayMin ?? '--') . '°'
                    : null;
            @endphp

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $t['weather']['tag'] }}</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $t['weather']['title'] }}</h2>
                    <p class="mt-2 text-sm text-slate-600">{{ $t['weather']['desc'] }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <p id="weather-updated-at" class="text-xs text-slate-500">{{ $weatherUpdatedAt ? $t['weather']['updated'] . $weatherUpdatedAt : $t['weather']['updated'] . '-' }}</p>
                    <button
                        type="button"
                        id="weather-refresh"
                        data-endpoint="{{ route('weather.labuanbajo', ['lang' => app()->getLocale()]) }}"
                        class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                    >
                        {{ $t['weather']['update_btn'] }}
                    </button>
                </div>
            </div>

            @if (empty($weatherDaily))
                <div class="mt-6 rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-800">
                    {{ $t['weather']['empty'] }}
                </div>
            @else
                <div class="mt-6 rounded-3xl border border-emerald-100 bg-emerald-50/60 p-6">
                    <p class="text-xs font-semibold text-emerald-700">{{ $t['weather']['today'] }}{{ $todayDow ? ' • ' . $todayDow : '' }}</p>
                    <div class="mt-4 grid gap-6 md:grid-cols-[1fr_320px] md:items-center">
                        <div>
                            <p id="weather-current-temp" class="text-5xl font-semibold tracking-tight text-slate-900">{{ $todayTemp !== null ? $todayTemp . '°' : '--' }}</p>
                            <p id="weather-current-range" class="mt-2 text-sm text-slate-600">{{ $todayRangeText ?? $t['weather']['range_fallback'] }}</p>
                            <p id="weather-current-status" class="mt-2 text-base font-semibold text-slate-900">{{ $todayStatusText }}</p>
                            <p id="weather-current-pop" class="mt-2 text-sm text-slate-600">{{ $todayPop !== null ? $t['weather']['pop_label'] . ': ' . $todayPop . '%' : $t['weather']['pop_label'] . ': -' }}</p>
                            <p class="mt-4 text-xs text-slate-500">{{ $t['weather']['illustration_note'] }}</p>
                        </div>

                        <div
                            id="weather-scene"
                            class="weather-scene relative w-full overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm"
                            data-scene="{{ $scene }}"
                            aria-label="Ilustrasi cuaca"
                        >
                            <div class="relative h-[220px] w-full">
                                <div class="scene scene-sunny weather-scene-layer">
                                    <img src="{{ asset('storage/weather/cerah.png') }}" alt="Ilustrasi cuaca cerah" class="weather-scene-image" loading="lazy" decoding="async">
                                </div>
                                <div class="scene scene-cloudy weather-scene-layer">
                                    <img src="{{ asset('storage/weather/berawan.png') }}" alt="Ilustrasi cuaca berawan" class="weather-scene-image" loading="lazy" decoding="async">
                                </div>
                                <div class="scene scene-rainy weather-scene-layer">
                                    <img src="{{ asset('storage/weather/hujan.png') }}" alt="Ilustrasi cuaca hujan" class="weather-scene-image" loading="lazy" decoding="async">
                                </div>
                            </div>

                            <div class="pointer-events-none absolute inset-x-0 bottom-0 flex items-center justify-between px-4 py-3 text-xs">
                                <span class="rounded-full bg-white/80 px-3 py-1 text-slate-700">{{ $t['weather']['badge_left'] }}</span>
                                <span class="rounded-full bg-emerald-600/90 px-3 py-1 font-semibold text-white">{{ $todayStatusText }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-weather-cards>
                    @foreach (collect($weatherDaily)->slice(1) as $day)
                        @php
                            $dateLabel = null;
                            try {
                                $dateLabel = !empty($day['date'])
                                    ? \Carbon\CarbonImmutable::parse($day['date'], 'Asia/Makassar')->locale(app()->getLocale())->isoFormat('D MMM')
                                    : null;
                            } catch (\Throwable) {
                                $dateLabel = null;
                            }
                        @endphp
                        <div class="rounded-2xl border border-slate-200 bg-white p-4" data-weather-day="{{ $day['index'] }}">
                            <p class="text-xs text-emerald-600" data-role="label">{{ $dateLabel ?? ($day['date'] ?? '-') }}</p>
                            <p class="mt-2 text-lg font-semibold text-slate-900" data-role="temp">{{ ($day['temp_max'] ?? '--') . '° / ' . ($day['temp_min'] ?? '--') . '°' }}</p>
                            <p class="mt-1 text-sm text-slate-600" data-role="status">{{ $t['weather']['status'][$day['status_key'] ?? 'unknown'] ?? $t['weather']['status_unknown'] }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const refreshBtn = document.getElementById('weather-refresh');
                const sceneBtn = document.getElementById('weather-scene');

                if (!refreshBtn) return;

                const endpoint = refreshBtn.dataset.endpoint;
                const originalText = refreshBtn.textContent;

                const fmtUpdatedAt = (iso) => {
                    if (!iso) return '-';
                    try {
                        const date = new Date(iso);
                        const str = new Intl.DateTimeFormat('id-ID', {
                            timeZone: 'Asia/Makassar',
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                        }).format(date);
                        return `${str} WITA`;
                    } catch (_) {
                        return '-';
                    }
                };

                const setText = (id, text) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.textContent = text;
                };

                const fmtDayLabel = (dateString) => {
                    if (!dateString) return '-';
                    try {
                        // keep WITA date stable
                        const date = new Date(`${dateString}T00:00:00+08:00`);
                        return new Intl.DateTimeFormat('id-ID', {
                            timeZone: 'Asia/Makassar',
                            day: 'numeric',
                            month: 'short',
                        }).format(date);
                    } catch (_) {
                        return dateString;
                    }
                };

                const statusMap = @json($t['weather']['status']);

                const renderSmallCards = (days) => {
                    const container = refreshBtn.closest('section')?.querySelector('[data-weather-cards]');
                    if (!container) return;

                    const html = days.map((d) => {
                        const label = fmtDayLabel(d.date);
                        const temp = `${(d.temp_max ?? '--')}° / ${(d.temp_min ?? '--')}°`;
                        const statusKey = d.status_key ?? 'unknown';
                        const status = statusMap?.[statusKey] ?? '{{ $t['weather']['status_unknown'] }}';
                        return `
                            <div class="rounded-2xl border border-slate-200 bg-white p-4" data-weather-day="${d.index}">
                                <p class="text-xs text-emerald-600" data-role="label">${label}</p>
                                <p class="mt-2 text-lg font-semibold text-slate-900" data-role="temp">${temp}</p>
                                <p class="mt-1 text-sm text-slate-600" data-role="status">${status}</p>
                            </div>
                        `;
                    }).join('');

                    container.innerHTML = html;
                };

                const applyWeather = (payload) => {
                    const data = payload?.data;
                    if (!data) return;

                    const today = data.today;
                    const current = data.current;

                    const temp = current?.temperature ?? today?.temp_max ?? null;
                    setText('weather-current-temp', temp !== null ? `${temp}°` : '--');

                    const rangeText = (today?.temp_max !== undefined || today?.temp_min !== undefined)
                        ? `{{ $t['weather']['max'] }} ${today?.temp_max ?? '--'}° • {{ $t['weather']['min'] }} ${today?.temp_min ?? '--'}°`
                        : '{{ $t['weather']['max'] }} --° • {{ $t['weather']['min'] }} --°';
                    setText('weather-current-range', rangeText);

                    const statusKey = current?.status_key ?? today?.status_key ?? 'unknown';
                    const statusText = statusMap?.[statusKey] ?? '{{ $t['weather']['status_unknown'] }}';
                    setText('weather-current-status', statusText);

                    const popText = (today?.precipitation_probability_max !== null && today?.precipitation_probability_max !== undefined)
                        ? `{{ $t['weather']['pop_label'] }}: ${today.precipitation_probability_max}%`
                        : '{{ $t['weather']['pop_label'] }}: -';
                    setText('weather-current-pop', popText);

                    setText('weather-updated-at', `{{ $t['weather']['updated'] }}${fmtUpdatedAt(data.fetched_at)}`);

                    if (sceneBtn) {
                        sceneBtn.dataset.scene = current?.scene ?? today?.scene ?? 'cloudy';

                        const labelLeft = sceneBtn.querySelector('span:first-child');
                        const labelRight = sceneBtn.querySelector('span:last-child');
                        if (labelRight) {
                            labelRight.textContent = statusText;
                        }
                    }

                    const daily = Array.isArray(data.daily) ? data.daily : [];
                    renderSmallCards(daily.slice(1));
                };

                refreshBtn.addEventListener('click', async () => {
                    refreshBtn.disabled = true;
                    refreshBtn.textContent = '{{ $t['weather']['loading'] }}';

                    try {
                        const url = new URL(endpoint, window.location.origin);
                        url.searchParams.set('force', '1');
                        const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
                        const json = await res.json();
                        if (json?.ok) {
                            applyWeather(json);
                        }
                    } catch (_) {
                        // ignore
                    } finally {
                        refreshBtn.disabled = false;
                        refreshBtn.textContent = originalText;
                    }
                });
            });
        </script>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid gap-8 md:grid-cols-3">
            @foreach ($t['features'] as $feature)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $feature['tag'] }}</p>
                    <h3 class="mt-3 text-xl font-semibold text-slate-900">{{ $feature['title'] }}</h3>
                    <p class="mt-3 text-sm text-slate-600">{{ $feature['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <section id="reviews" class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $t['reviews']['tag'] }}</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $t['reviews']['title'] }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $t['reviews']['desc'] }}</p>
            </div>
        </div>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach ($t['reviews']['items'] as $item)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between text-xs">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">{{ $item['trip'] }}</span>
                        <span class="text-amber-400">{{ str_repeat('★', (int) ($item['rating'] ?? 5)) }}</span>
                    </div>
                    <p class="mt-4 text-sm text-slate-600">“{{ $item['quote'] }}”</p>
                    <div class="mt-4 text-sm font-semibold text-slate-900">{{ $item['name'] }}</div>
                    <div class="text-xs text-slate-500">{{ $item['origin'] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="blog" class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $t['blog']['tag'] }}</p>
                <h2 class="mt-3 text-2xl font-semibold text-slate-900">{{ $t['blog']['title'] }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $t['blog']['desc'] }}</p>
            </div>
            <a href="{{ route('blog.index', ['lang' => app()->getLocale()]) }}" class="text-sm text-emerald-700 hover:text-emerald-800">{{ $t['blog']['link'] }}</a>
        </div>
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach ($t['blog']['items'] as $item)
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-emerald-700">{{ $item['category'] }}</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500">{{ $item['date'] }}</span>
                    </div>
                    <h3 class="mt-3 text-lg font-semibold text-slate-900">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['excerpt'] }}</p>
                    <a href="{{ route('blog.index', ['lang' => app()->getLocale()]) }}" class="mt-4 inline-flex text-sm text-emerald-700 hover:text-emerald-800">{{ $t['blog']['read_more'] }} →</a>
                </div>
            @endforeach
        </div>
    </section>

    <section id="faq" class="mx-auto max-w-6xl px-6 py-16">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-slate-900">{{ $t['faq']['title'] }}</h2>
            <div class="mt-6 space-y-3 text-sm text-slate-600">
                @php
                    $faqList = ($faqItems ?? collect())->isNotEmpty() ? $faqItems : collect($t['faq']['items']);
                @endphp
                @foreach ($faqList as $index => $item)
                    <details class="group rounded-2xl border border-slate-200 bg-white px-5 py-4" @if($index === 0) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 text-sm font-semibold text-slate-900">
                            <span>{{ $item['q'] }}</span>
                            <span class="flex h-7 w-7 items-center justify-center rounded-full border border-emerald-200 text-emerald-700 transition group-open:rotate-180">⌄</span>
                        </summary>
                        <div class="mt-3 text-sm text-slate-600">
                            {{ $item['a'] }}
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <section id="contact" class="mx-auto max-w-6xl px-6 py-16">
        @php
            $contactEmail = $contactSettings['email'] ?? 'hello@triptokomodo.com';
            $contactPhone = $contactSettings['phone'] ?? '+62 812 0000 0000';
            $contactWhatsapp = $contactSettings['whatsapp'] ?? $contactPhone;
            $contactWhatsappUrl = $contactSettings['whatsapp_url'] ?? 'https://wa.me/6281200000000';
        @endphp
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-10">
            <h2 class="text-3xl font-semibold text-slate-900">{{ $t['contact']['title'] }}</h2>
            <p class="mt-3 text-sm text-emerald-800">{{ $t['contact']['desc'] }}</p>
            <div class="mt-5 text-sm text-emerald-900">
                <p>Telepon: {{ $contactPhone }}</p>
                <p>Email: {{ $contactEmail }}</p>
            </div>
            <div class="mt-6 flex flex-wrap gap-4">
                <a href="{{ $contactWhatsappUrl }}" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">{{ $t['contact']['cta_whatsapp'] }}</a>
                <a href="mailto:{{ $contactEmail }}" class="rounded-full border border-emerald-200 px-6 py-3 text-sm text-emerald-700">{{ $t['contact']['cta_email'] }}</a>
            </div>
        </div>
    </section>
@endsection
