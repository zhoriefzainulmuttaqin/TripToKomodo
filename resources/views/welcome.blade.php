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
        $forecast = [
            ['day' => 'Hari 1', 'date' => 'Sen', 'temp' => '29° / 25°', 'status' => 'Cerah'],
            ['day' => 'Hari 2', 'date' => 'Sel', 'temp' => '30° / 25°', 'status' => 'Cerah Berawan'],
            ['day' => 'Hari 3', 'date' => 'Rab', 'temp' => '28° / 24°', 'status' => 'Berawan'],
            ['day' => 'Hari 4', 'date' => 'Kam', 'temp' => '29° / 25°', 'status' => 'Cerah'],
            ['day' => 'Hari 5', 'date' => 'Jum', 'temp' => '30° / 24°', 'status' => 'Cerah'],
            ['day' => 'Hari 6', 'date' => 'Sab', 'temp' => '29° / 25°', 'status' => 'Hujan Ringan'],
            ['day' => 'Hari 7', 'date' => 'Min', 'temp' => '28° / 24°', 'status' => 'Hujan Ringan'],
            ['day' => 'Hari 8', 'date' => 'Sen', 'temp' => '29° / 24°', 'status' => 'Cerah Berawan'],
            ['day' => 'Hari 9', 'date' => 'Sel', 'temp' => '30° / 25°', 'status' => 'Cerah'],
            ['day' => 'Hari 10', 'date' => 'Rab', 'temp' => '29° / 24°', 'status' => 'Berawan'],
            ['day' => 'Hari 11', 'date' => 'Kam', 'temp' => '30° / 25°', 'status' => 'Cerah'],
            ['day' => 'Hari 12', 'date' => 'Jum', 'temp' => '29° / 24°', 'status' => 'Cerah Berawan'],
            ['day' => 'Hari 13', 'date' => 'Sab', 'temp' => '28° / 24°', 'status' => 'Hujan Ringan'],
            ['day' => 'Hari 14', 'date' => 'Min', 'temp' => '29° / 24°', 'status' => 'Cerah'],
        ];
    @endphp

    <section class="relative overflow-hidden bg-white">
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
                    $translation = $package->translations->first();
                    $image = $package->images->sortBy('sort_order')->first();
                @endphp
                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="h-48 rounded-2xl bg-slate-100" style="background-image:url('{{ $image?->url }}'); background-size:cover; background-position:center;"></div>
                    <div class="mt-4">
                        <p class="text-xs text-emerald-600">{{ $package->duration_days }} hari</p>
                        <h3 class="mt-2 text-lg font-semibold text-slate-900">{{ $translation?->title ?? $package->code }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $translation?->summary ?? 'Paket trip eksklusif Labuan Bajo dengan itinerary terbaik.' }}</p>
                        <a href="{{ route('tours.show', ['lang' => app()->getLocale(), 'slug' => $translation?->slug ?? $package->code]) }}" class="mt-4 inline-flex text-sm text-emerald-700 hover:text-emerald-800">Detail Paket →</a>
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

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="rounded-3xl border border-emerald-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">2 Minggu Kedepan</p>
                    <h2 class="mt-3 text-2xl font-semibold text-slate-900">Perkiraan cuaca Labuan Bajo</h2>
                    <p class="mt-2 text-sm text-slate-600">Estimasi cuaca untuk membantu pemilihan itinerary terbaik.</p>
                </div>
                <span class="rounded-full border border-emerald-100 bg-emerald-50 px-4 py-2 text-xs text-emerald-700">Update harian</span>
            </div>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($forecast as $day)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <p class="text-xs text-emerald-600">{{ $day['day'] }} • {{ $day['date'] }}</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900">{{ $day['temp'] }}</p>
                        <p class="mt-1 text-sm text-slate-600">{{ $day['status'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
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
