@extends('layouts.app')

@section('title', 'Trip Labuan Bajo | Travel Platform Eksklusif')
@section('meta_description', 'Paket trip Labuan Bajo premium dengan kapal terbaik, itinerary personal, dan layanan concierge. Booking mudah dengan multi-currency dan multi-language.')

@section('hreflang')
    @foreach ($activeLanguages as $language)
        <link rel="alternate" hreflang="{{ $language->code }}" href="{{ url($language->code) }}">
    @endforeach
@endsection

@section('content')
    @push('schema')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin="" defer></script>
    @endpush
    @php
        $weatherData = $weather ?? null;
        $weatherToday = is_array($weatherData) ? ($weatherData['today'] ?? null) : null;
        $weatherCurrent = is_array($weatherData) ? ($weatherData['current'] ?? null) : null;
        $weatherDaily = is_array($weatherData) ? ($weatherData['daily'] ?? []) : [];
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
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Labuan Bajo Exclusive Trips</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight text-slate-900 md:text-5xl">Nikmati curated trip Labuan Bajo dengan gaya premium dan layanan personal.</h1>
                    <p class="mt-5 text-base text-slate-600">Konsep seperti trip.com: semuanya terkurasi, cepat ditemukan, dan siap dipesan untuk segala usia.</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Cari Paket Trip</a>
                        <a href="#contact" class="rounded-full border border-emerald-200 px-6 py-3 text-sm text-emerald-700">Konsultasi Personal</a>
                    </div>
                    <div class="mt-10 grid gap-4 text-xs text-slate-600 md:grid-cols-3">
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                            <p class="text-lg font-semibold text-emerald-700">100+</p>
                            <p>Trip premium selesai</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                            <p class="text-lg font-semibold text-emerald-700">4.9/5</p>
                            <p>Rata-rata kepuasan</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-100 bg-emerald-50/70 p-4">
                            <p class="text-lg font-semibold text-emerald-700">24/7</p>
                            <p>Concierge support</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Trip Finder</p>
                    <h2 class="mt-3 text-xl font-semibold text-slate-900">Rencanakan trip Anda</h2>
                    <div class="mt-5 space-y-4 text-sm">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs text-slate-500">Durasi</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">3D2N • 4D3N • 5D4N</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs text-slate-500">Tipe kapal</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">Phinisi Luxury, Speedboat</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <p class="text-xs text-slate-500">Destinasi utama</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">Padar, Pink Beach, Komodo, Manta Point</p>
                        </div>
                    </div>
                    <a href="#contact" class="mt-6 block rounded-full bg-emerald-600 px-5 py-3 text-center text-sm font-semibold text-white">Diskusi itinerary custom</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_0.6fr]">
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Peta Komodo & Flores</p>
                        <h2 class="mt-3 text-2xl font-semibold text-slate-900">Peta destinasi utama</h2>
                        <p class="mt-2 text-sm text-slate-600">Titik destinasi diambil otomatis dari tabel destinasi dan ditampilkan pada peta Komodo & Flores.</p>
                    </div>
                    <span class="rounded-full border border-emerald-100 bg-emerald-50 px-4 py-2 text-xs text-emerald-700">Peta interaktif</span>
                </div>
                <div class="mt-6 overflow-hidden rounded-2xl border border-emerald-100 bg-emerald-50">
                    <div id="komodo-map" class="h-[340px] w-full"></div>
                </div>
                <p class="mt-3 text-xs text-slate-500">Peta menggunakan OpenStreetMap via Leaflet dan difokuskan ke wilayah Komodo & Flores.</p>
            </div>
            <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Destinasi populer</h3>
                <div class="mt-4 space-y-4 text-sm text-slate-600">
                    @forelse ($destinations as $destination)
                        <div class="rounded-2xl border border-slate-200 bg-white p-4">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-semibold text-slate-900">{{ $destination->name }}</p>
                                @if (!empty($destination->category))
                                    <span class="text-xs text-emerald-700">{{ $destination->category }}</span>
                                @endif
                            </div>
                            @if (!empty($destination->distance))
                                <p class="mt-2 text-xs text-slate-500">{{ $destination->distance }}</p>
                            @endif
                            <p class="mt-2 text-xs text-slate-400">Koordinat: {{ $destination->lat }}, {{ $destination->lng }}</p>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 p-4 text-xs text-emerald-700">
                            Belum ada data destinasi. Tambahkan data di tabel destinasi untuk menampilkan titik peta.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const mapContainer = document.getElementById('komodo-map');
                if (!mapContainer || typeof L === 'undefined') return;

                const bounds = [
                    [-9.4, 119.3],
                    [-8.1, 122.9],
                ];

                const map = L.map(mapContainer, {
                    scrollWheelZoom: false,
                    zoomControl: true,
                }).fitBounds(bounds, { padding: [20, 20] });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 18,
                }).addTo(map);

                const destinations = @json($destinations);
                if (!Array.isArray(destinations) || destinations.length === 0) return;

                destinations.forEach((destination) => {
                    const lat = parseFloat(destination.lat);
                    const lng = parseFloat(destination.lng);
                    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

                    const marker = L.circleMarker([lat, lng], {
                        radius: 6,
                        color: '#065f46',
                        fillColor: '#10b981',
                        fillOpacity: 0.8,
                        weight: 2,
                    }).addTo(map);

                    if (destination.name) {
                        marker.bindPopup(`<strong>${destination.name}</strong>`);
                    }
                });
            });
        </script>
    </section>

    <section id="experiences" class="mx-auto max-w-6xl px-6 py-16">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Curated Trips</p>
                <h2 class="mt-3 text-3xl font-semibold text-slate-900">Paket trip paling diminati</h2>
                <p class="mt-3 text-sm text-slate-600">Pilihan itinerary terfavorit dengan fasilitas premium dan crew berpengalaman.</p>
            </div>
            <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="text-sm text-emerald-700 hover:text-emerald-800">Lihat semua paket →</a>
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
                        <p class="text-xs text-emerald-600">{{ $package->duration_days }} hari</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $translation?->title ?? $package->code }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $translation?->summary ?? 'Paket trip eksklusif Labuan Bajo dengan itinerary terbaik.' }}</p>
                        @if (!empty($translation?->slug))
                            <a href="{{ route('tours.show', ['lang' => $linkLang, 'slug' => $translation->slug]) }}" class="mt-4 inline-flex text-sm text-emerald-700 hover:text-emerald-800">Detail Paket →</a>
                        @else
                            <span class="mt-4 inline-flex text-sm text-slate-400">Detail belum tersedia</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="rounded-3xl border border-slate-200 bg-white p-8 text-sm text-slate-600">
                    Paket trip akan segera tersedia. Tambahkan data paket melalui admin untuk tampil di sini.
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
                $todayStatus = is_array($weatherCurrent) ? ($weatherCurrent['status'] ?? null) : null;
                if (!$todayStatus && is_array($weatherToday)) {
                    $todayStatus = $weatherToday['status'] ?? null;
                }

                $todayRangeText = ($todayMax !== null || $todayMin !== null)
                    ? 'Max ' . ($todayMax ?? '--') . '° • Min ' . ($todayMin ?? '--') . '°'
                    : null;
            @endphp

            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Realtime • 2 Minggu Kedepan</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Perkiraan cuaca Labuan Bajo</h2>
                    <p class="mt-2 text-sm text-slate-600">Data diambil dari API cuaca publik dan di-cache sebentar agar cepat.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <p id="weather-updated-at" class="text-xs text-slate-500">{{ $weatherUpdatedAt ? 'Terakhir update: ' . $weatherUpdatedAt : 'Terakhir update: -' }}</p>
                    <button
                        type="button"
                        id="weather-refresh"
                        data-endpoint="{{ route('weather.labuanbajo', ['lang' => app()->getLocale()]) }}"
                        class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-700 hover:bg-emerald-100"
                    >
                        Update sekarang
                    </button>
                </div>
            </div>

            @if (empty($weatherDaily))
                <div class="mt-6 rounded-2xl border border-dashed border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-800">
                    Cuaca realtime belum tersedia. Pastikan server bisa akses internet, lalu coba klik "Update sekarang".
                </div>
            @else
                <div class="mt-6 rounded-3xl border border-emerald-100 bg-emerald-50/60 p-6">
                    <p class="text-xs font-semibold text-emerald-700">Hari ini{{ $todayDow ? ' • ' . $todayDow : '' }}</p>
                    <div class="mt-4 grid gap-6 md:grid-cols-[1fr_320px] md:items-center">
                        <div>
                            <p id="weather-current-temp" class="text-5xl font-semibold tracking-tight text-slate-900">{{ $todayTemp !== null ? $todayTemp . '°' : '--' }}</p>
                            <p id="weather-current-range" class="mt-2 text-sm text-slate-600">{{ $todayRangeText ?? 'Max --° • Min --°' }}</p>
                            <p id="weather-current-status" class="mt-2 text-base font-semibold text-slate-900">{{ $todayStatus ?? 'Tidak diketahui' }}</p>
                            <p id="weather-current-pop" class="mt-2 text-sm text-slate-600">{{ $todayPop !== null ? 'Peluang hujan: ' . $todayPop . '%' : 'Peluang hujan: -' }}</p>
                            <p class="mt-4 text-xs text-slate-500">Ilustrasi menyesuaikan kondisi: hujan / cerah / berawan.</p>
                        </div>

                        <div
                            id="weather-scene"
                            class="weather-scene relative w-full overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm"
                            data-scene="{{ $scene }}"
                            aria-label="Ilustrasi cuaca"
                        >
                            <svg viewBox="0 0 400 240" class="h-[220px] w-full" role="img" aria-hidden="true">
                                <defs>
                                    <linearGradient id="sky" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0" stop-color="#ecfeff" />
                                        <stop offset="1" stop-color="#d1fae5" />
                                    </linearGradient>
                                    <linearGradient id="sea" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0" stop-color="#a7f3d0" />
                                        <stop offset="1" stop-color="#34d399" />
                                    </linearGradient>
                                </defs>

                                <rect x="0" y="0" width="400" height="240" fill="url(#sky)" />

                                <g class="scene scene-sunny" opacity="0.95">
                                    <circle cx="310" cy="60" r="26" fill="#fbbf24" />
                                    <g class="sun-rays" stroke="#f59e0b" stroke-width="4" stroke-linecap="round">
                                        <line x1="310" y1="18" x2="310" y2="6" />
                                        <line x1="310" y1="114" x2="310" y2="126" />
                                        <line x1="268" y1="60" x2="256" y2="60" />
                                        <line x1="352" y1="60" x2="364" y2="60" />
                                        <line x1="282" y1="32" x2="273" y2="23" />
                                        <line x1="338" y1="88" x2="347" y2="97" />
                                        <line x1="338" y1="32" x2="347" y2="23" />
                                        <line x1="282" y1="88" x2="273" y2="97" />
                                    </g>
                                </g>

                                <g class="scene scene-cloudy" opacity="0.95">
                                    <g class="cloud cloud-1" fill="#ffffff">
                                        <ellipse cx="270" cy="70" rx="42" ry="22" />
                                        <ellipse cx="240" cy="74" rx="26" ry="18" />
                                        <ellipse cx="300" cy="78" rx="30" ry="18" />
                                    </g>
                                    <g class="cloud cloud-2" fill="#ffffff" opacity="0.9">
                                        <ellipse cx="120" cy="55" rx="36" ry="18" />
                                        <ellipse cx="95" cy="60" rx="22" ry="14" />
                                        <ellipse cx="145" cy="64" rx="26" ry="14" />
                                    </g>
                                </g>

                                <g class="scene scene-rainy" opacity="0.95">
                                    <g class="cloud cloud-1" fill="#ffffff">
                                        <ellipse cx="255" cy="70" rx="46" ry="24" />
                                        <ellipse cx="222" cy="76" rx="28" ry="18" />
                                        <ellipse cx="292" cy="80" rx="32" ry="18" />
                                    </g>
                                    <g class="rain" stroke="#60a5fa" stroke-width="3" stroke-linecap="round" opacity="0.9">
                                        <line class="rain-drop" x1="228" y1="98" x2="218" y2="118" />
                                        <line class="rain-drop" x1="255" y1="102" x2="245" y2="122" />
                                        <line class="rain-drop" x1="282" y1="98" x2="272" y2="118" />
                                        <line class="rain-drop" x1="310" y1="104" x2="300" y2="124" />
                                    </g>
                                    <g class="umbrella" transform="translate(62, 78)">
                                        <path d="M120 78 C138 58 170 58 188 78" fill="#ef4444" opacity="0.9" />
                                        <path d="M120 78 C138 96 170 96 188 78" fill="#ef4444" opacity="0.55" />
                                        <path d="M154 78 L154 120" stroke="#7c2d12" stroke-width="5" stroke-linecap="round" />
                                    </g>
                                </g>

                                <path d="M0 165 C70 140 120 150 170 165 C220 182 260 182 400 165 L400 240 L0 240 Z" fill="url(#sea)" opacity="0.75" />
                                <path d="M0 170 C90 150 155 165 210 178 C270 192 330 190 400 170" fill="none" stroke="#10b981" stroke-width="6" opacity="0.35" />

                                <g class="komodo" transform="translate(50, 130)">
                                    <path d="M30 55 C45 30 90 24 130 30 C170 36 210 54 230 72 C245 85 244 102 232 112 C214 127 178 127 150 120 C120 112 95 102 70 98 C45 94 24 78 30 55 Z" fill="#334155" opacity="0.95" />
                                    <path class="komodo-tail" d="M28 62 C8 52 -2 34 10 22 C24 8 44 18 54 30" fill="none" stroke="#334155" stroke-width="14" stroke-linecap="round" />
                                    <circle cx="202" cy="62" r="4" fill="#0f172a" />
                                    <path d="M214 70 C224 74 232 76 242 72" stroke="#0f172a" stroke-width="3" stroke-linecap="round" fill="none" opacity="0.9" />
                                    <path d="M108 118 C106 138 112 152 128 160" stroke="#1f2937" stroke-width="10" stroke-linecap="round" fill="none" />
                                    <path d="M164 120 C162 140 168 154 184 162" stroke="#1f2937" stroke-width="10" stroke-linecap="round" fill="none" />
                                </g>
                            </svg>

                            <div class="pointer-events-none absolute inset-x-0 bottom-0 flex items-center justify-between px-4 py-3 text-xs">
                                <span class="rounded-full bg-white/80 px-3 py-1 text-slate-700">Ilustrasi dinamis</span>
                                <span class="rounded-full bg-emerald-600/90 px-3 py-1 font-semibold text-white">{{ $todayStatus ?? 'Cuaca' }}</span>
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
                            <p class="mt-1 text-sm text-slate-600" data-role="status">{{ $day['status'] ?? '-' }}</p>
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

                const renderSmallCards = (days) => {
                    const container = refreshBtn.closest('section')?.querySelector('[data-weather-cards]');
                    if (!container) return;

                    const html = days.map((d) => {
                        const label = fmtDayLabel(d.date);
                        const temp = `${(d.temp_max ?? '--')}° / ${(d.temp_min ?? '--')}°`;
                        const status = d.status ?? '-';
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
                        ? `Max ${today?.temp_max ?? '--'}° • Min ${today?.temp_min ?? '--'}°`
                        : 'Max --° • Min --°';
                    setText('weather-current-range', rangeText);

                    setText('weather-current-status', current?.status ?? today?.status ?? 'Tidak diketahui');

                    const popText = (today?.precipitation_probability_max !== null && today?.precipitation_probability_max !== undefined)
                        ? `Peluang hujan: ${today.precipitation_probability_max}%`
                        : 'Peluang hujan: -';
                    setText('weather-current-pop', popText);

                    setText('weather-updated-at', `Terakhir update: ${fmtUpdatedAt(data.fetched_at)}`);

                    if (sceneBtn) {
                        sceneBtn.dataset.scene = current?.scene ?? today?.scene ?? 'cloudy';

                        const labelLeft = sceneBtn.querySelector('span:first-child');
                        const labelRight = sceneBtn.querySelector('span:last-child');
                        if (labelRight) {
                            labelRight.textContent = current?.status ?? today?.status ?? 'Cuaca';
                        }
                    }

                    const daily = Array.isArray(data.daily) ? data.daily : [];
                    renderSmallCards(daily.slice(1));
                };

                refreshBtn.addEventListener('click', async () => {
                    refreshBtn.disabled = true;
                    refreshBtn.textContent = 'Memuat...';

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
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Smart Pricing</p>
                <h3 class="mt-3 text-xl font-semibold text-slate-900">Harga optimal otomatis</h3>
                <p class="mt-3 text-sm text-slate-600">Sistem margin & psikologi harga per mata uang untuk menjaga profit tanpa mengorbankan kompetitif.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">SEO Ready</p>
                <h3 class="mt-3 text-xl font-semibold text-slate-900">SEO multi-bahasa</h3>
                <p class="mt-3 text-sm text-slate-600">Sitemap otomatis, schema JSON-LD, dan hreflang untuk menjangkau traveler global.</p>
            </div>
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Growth Engine</p>
                <h3 class="mt-3 text-xl font-semibold text-slate-900">Affiliate & retargeting</h3>
                <p class="mt-3 text-sm text-slate-600">Tracking referral, kupon, dan automasi follow-up untuk konversi maksimal.</p>
            </div>
        </div>
    </section>

    <section id="faq" class="mx-auto max-w-6xl px-6 py-16">
        <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-semibold text-slate-900">Pertanyaan yang sering ditanyakan</h2>
            <div class="mt-6 space-y-4 text-sm text-slate-600">
                <div>
                    <p class="font-semibold text-slate-900">Apakah paket bisa disesuaikan?</p>
                    <p>Ya, itinerary dapat disesuaikan sesuai kebutuhan, budget, dan minat traveler.</p>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Apakah tersedia pembayaran mata uang asing?</p>
                    <p>Tersedia multi-currency otomatis dengan kurs terbaru dan opsi manual di navbar.</p>
                </div>
                <div>
                    <p class="font-semibold text-slate-900">Berapa lama proses booking?</p>
                    <p>Booking dapat selesai dalam 10-15 menit setelah konfirmasi itinerary.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="contact" class="mx-auto max-w-6xl px-6 py-16">
        <div class="rounded-3xl border border-emerald-200 bg-emerald-50 p-10">
            <h2 class="text-3xl font-semibold text-slate-900">Siap berangkat ke Labuan Bajo?</h2>
            <p class="mt-3 text-sm text-emerald-800">Tim concierge kami akan bantu itinerary, kapal, dan kebutuhan pribadi Anda.</p>
            <div class="mt-6 flex flex-wrap gap-4">
                <a href="https://wa.me/6281200000000" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">WhatsApp Concierge</a>
                <a href="mailto:hello@triptokomodo.com" class="rounded-full border border-emerald-200 px-6 py-3 text-sm text-emerald-700">Email Kami</a>
            </div>
        </div>
    </section>
@endsection
