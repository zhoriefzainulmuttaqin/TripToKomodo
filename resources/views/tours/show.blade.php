@extends('layouts.app')

@section('title', ($translation->meta_title ?? $translation->title) . ' | Labuan Bajo')
@section('meta_description', $translation->meta_description ?? $translation->summary ?? 'Paket trip Labuan Bajo premium.')
@section('canonical', url()->current())

@section('hreflang')
    @php
        $byLang = $package->translations->keyBy('language_code');
    @endphp
    @foreach ($activeLanguages as $language)
        @php
            $t = $byLang->get($language->code);
        @endphp
        @if ($t && ($t->is_active ?? true))
            <link rel="alternate" hreflang="{{ $language->code }}" href="{{ route('tours.show', ['lang' => $language->code, 'slug' => $t->slug]) }}">
        @endif
    @endforeach
@endsection

@section('content')
    @php
        $currencySymbol = $activeCurrencies->firstWhere('code', $pricing['currency_code'])?->symbol ?? $pricing['currency_code'];
        $priceFormatted = number_format($pricing['selling_price_converted'], 0, ',', '.');

        $imagesSorted = $package->images->sortBy(fn ($img) => [($img->is_primary ? 0 : 1), $img->sort_order]);
        $primaryImage = $imagesSorted->first();

        $contactUrl = route('home', ['lang' => app()->getLocale()]) . '#contact';

        $faqSchema = $seo->faqSchema($package->faqs->map(fn ($faq) => ['question' => $faq->question, 'answer' => $faq->answer])->toArray());
        $reviewSchema = $seo->reviewSchema([
            'value' => number_format($package->reviews->avg('rating') ?? 4.8, 1),
            'count' => $package->reviews->count() ?: 12,
        ]);
        $tourSchema = $seo->tourStructuredData($package, [
            'title' => $translation->title,
            'description' => $translation->summary,
            'price' => $pricing['selling_price_converted'],
            'currency_code' => $pricing['currency_code'],
            'url' => url()->current(),
        ]);

        $breadcrumbSchema = $seo->breadcrumbSchema([
            ['name' => 'Home', 'url' => route('home', ['lang' => app()->getLocale()])],
            ['name' => 'Tours', 'url' => route('tours.index', ['lang' => app()->getLocale()])],
            ['name' => $translation->title, 'url' => url()->current()],
        ]);

        $hasAvailability = ($package->availabilities ?? collect())->isNotEmpty();
        $availableCount = $hasAvailability ? $package->availabilities->where('is_available', true)->count() : 0;

        $formatLines = function (?string $text): array {
            if (!$text) return [];
            $lines = preg_split('/\r\n|\r|\n/', $text);
            $lines = array_map(fn ($s) => trim((string) $s), $lines);
            $lines = array_values(array_filter($lines, fn ($s) => $s !== ''));
            return $lines;
        };

        $includesLines = $formatLines($translation->includes);
        $excludesLines = $formatLines($translation->excludes);
    @endphp

    @push('schema')
        <script type="application/ld+json">{!! json_encode($tourSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @if ($package->faqs->isNotEmpty())
            <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        @endif
        <script type="application/ld+json">{!! json_encode($reviewSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="relative overflow-hidden">
        @if (!empty($primaryImage?->url))
            <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ $primaryImage->url }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/55 to-white"></div>
        @else
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.22),transparent_65%)]"></div>
        @endif

        <div class="relative mx-auto max-w-6xl px-6 py-14">
            <nav class="text-xs text-white/80">
                <a href="{{ route('home', ['lang' => app()->getLocale()]) }}" class="hover:text-white">Home</a>
                <span class="px-2">/</span>
                <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="hover:text-white">Tours</a>
                <span class="px-2">/</span>
                <span class="text-white">{{ $translation->title }}</span>
            </nav>

            <div class="mt-6 grid gap-10 lg:grid-cols-[1.5fr_0.7fr] lg:items-start">
                <div>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        @if ($package->category)
                            <span class="rounded-full bg-white/10 px-4 py-2 text-white">{{ $package->category->name }}</span>
                        @endif
                        <span class="rounded-full bg-white/10 px-4 py-2 text-white">{{ $package->duration_days }}D/{{ $package->duration_nights }}N</span>
                        <span class="rounded-full bg-white/10 px-4 py-2 text-white">Max {{ $package->max_people ?? '-' }}</span>
                        @if (!empty($package->difficulty))
                            <span class="rounded-full bg-white/10 px-4 py-2 text-white">{{ ucfirst($package->difficulty) }}</span>
                        @endif
                    </div>

                    <h1 class="mt-5 text-4xl font-semibold leading-tight text-white md:text-5xl">{{ $translation->title }}</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-relaxed text-white/80">{{ $translation->summary ?? '' }}</p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        <a href="#overview" class="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-white">Lihat Detail</a>
                        <a href="{{ $contactUrl }}" class="rounded-full border border-white/25 bg-white/10 px-6 py-3 text-sm font-semibold text-white">Konsultasi Trip</a>
                    </div>

                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-white">
                            <p class="text-xs text-white/70">Harga mulai</p>
                            <p class="mt-2 text-xl font-semibold">{{ $currencySymbol }} {{ $priceFormatted }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-white">
                            <p class="text-xs text-white/70">Operator</p>
                            <p class="mt-2 text-sm font-semibold">{{ $package->operator?->name ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/15 bg-white/10 p-4 text-white">
                            <p class="text-xs text-white/70">Availability</p>
                            <p class="mt-2 text-sm font-semibold">{{ $hasAvailability ? $availableCount . ' tanggal tersedia' : 'Belum diatur' }}</p>
                        </div>
                    </div>
                </div>

                <aside class="rounded-3xl border border-white/15 bg-white/10 p-6 text-white backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.3em] text-white/70">Quick booking</p>
                    <p class="mt-3 text-3xl font-semibold">{{ $currencySymbol }} {{ $priceFormatted }}</p>
                    <p class="mt-2 text-sm text-white/80">Harga ditampilkan otomatis menyesuaikan mata uang yang dipilih.</p>

                    <div class="mt-6 space-y-3 text-sm text-white/85">
                        <div class="flex items-center justify-between">
                            <span>Min</span>
                            <span class="font-semibold">{{ $package->min_people }} pax</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Max</span>
                            <span class="font-semibold">{{ $package->max_people ?? '-' }} pax</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Status</span>
                            <span class="font-semibold">{{ ucfirst($package->status) }}</span>
                        </div>
                    </div>

                    <a href="{{ $contactUrl }}" class="mt-6 block rounded-full bg-emerald-500 px-6 py-3 text-center text-sm font-semibold text-white">Konsultasi & Booking</a>
                    <a href="#availability" class="mt-3 block rounded-full border border-white/25 bg-white/10 px-6 py-3 text-center text-sm font-semibold text-white">Cek Availability</a>

                    <p class="mt-4 text-xs text-white/70">Tip: isi availability dari admin untuk menampilkan tanggal tersedia.</p>
                </aside>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-6 py-14" id="overview">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_0.6fr]">
            <div>
                @if ($imagesSorted->isNotEmpty())
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($imagesSorted->take(4) as $img)
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-100">
                                <img src="{{ $img->url }}" alt="{{ $img->alt_text ?? $translation->title }}" class="h-56 w-full object-cover" loading="lazy" />
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-10 space-y-10">
                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">Deskripsi</h2>
                        <div class="prose mt-4 max-w-none text-slate-700">{!! $descriptionHtml !!}</div>
                    </div>

                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">Itinerary</h2>
                        <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-700">
                            {!! nl2br(e($translation->itinerary ?? 'Itinerary lengkap akan diinformasikan oleh concierge.')) !!}
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="rounded-3xl border border-emerald-100 bg-emerald-50/60 p-6">
                            <h3 class="text-lg font-semibold text-emerald-900">Included</h3>
                            @if (!empty($includesLines))
                                <ul class="mt-4 space-y-2 text-sm text-emerald-950/80">
                                    @foreach ($includesLines as $line)
                                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span><span>{{ $line }}</span></li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-4 text-sm text-emerald-950/80">Akomodasi kapal, makan, crew, dan dokumentasi.</p>
                            @endif
                        </div>

                        <div class="rounded-3xl border border-rose-100 bg-rose-50/60 p-6">
                            <h3 class="text-lg font-semibold text-rose-900">Excluded</h3>
                            @if (!empty($excludesLines))
                                <ul class="mt-4 space-y-2 text-sm text-rose-950/80">
                                    @foreach ($excludesLines as $line)
                                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-rose-600"></span><span>{{ $line }}</span></li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-4 text-sm text-rose-950/80">Tiket pesawat, asuransi pribadi, dan pengeluaran pribadi.</p>
                            @endif
                        </div>
                    </div>

                    @if (!empty($translation->transportation))
                        <div>
                            <h2 class="text-2xl font-semibold text-slate-900">Transportasi</h2>
                            <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-700">{!! nl2br(e($translation->transportation)) !!}</div>
                        </div>
                    @endif

                    @if (($package->destinations ?? collect())->isNotEmpty())
                        <div id="destinations">
                            <h2 class="text-2xl font-semibold text-slate-900">Destinasi</h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach ($package->destinations as $d)
                                    <span class="rounded-full border border-emerald-100 bg-emerald-50 px-4 py-2 text-xs text-emerald-800">{{ $d->name }}</span>
                                @endforeach
                            </div>

                            <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-600">
                                        <tr>
                                            <th class="px-5 py-4">Nama</th>
                                            <th class="px-5 py-4">Kategori</th>
                                            <th class="px-5 py-4">Koordinat</th>
                                            <th class="px-5 py-4">Maps</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($package->destinations as $d)
                                            <tr class="border-t border-slate-100">
                                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $d->name }}</td>
                                                <td class="px-5 py-4 text-slate-700">{{ $d->category ?? '-' }}</td>
                                                <td class="px-5 py-4 text-slate-700">{{ $d->lat ?? '-' }}, {{ $d->lng ?? '-' }}</td>
                                                <td class="px-5 py-4">
                                                    @if (!empty($d->lat) && !empty($d->lng))
                                                        <a class="text-emerald-700 hover:text-emerald-800" target="_blank" rel="noopener" href="{{ 'https://www.google.com/maps?q=' . $d->lat . ',' . $d->lng }}">Buka</a>
                                                    @else
                                                        <span class="text-slate-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div id="availability">
                        <h2 class="text-2xl font-semibold text-slate-900">Ketersediaan</h2>

                        @if (!$hasAvailability)
                            <div class="mt-4 rounded-3xl border border-dashed border-emerald-200 bg-emerald-50 p-6 text-sm text-emerald-900">
                                Kalender ketersediaan belum diatur untuk paket ini. Hubungi concierge untuk cek jadwal.
                            </div>
                        @else
                            <div class="mt-4 overflow-hidden rounded-3xl border border-slate-200 bg-white">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-600">
                                        <tr>
                                            <th class="px-5 py-4">Tanggal</th>
                                            <th class="px-5 py-4">Status</th>
                                            <th class="px-5 py-4">Slots</th>
                                            <th class="px-5 py-4">Override IDR</th>
                                            <th class="px-5 py-4">Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($package->availabilities as $a)
                                            <tr class="border-t border-slate-100">
                                                <td class="px-5 py-4 font-semibold text-slate-900">{{ $a->date?->format('d M Y') }}</td>
                                                <td class="px-5 py-4">
                                                    @if ($a->is_available)
                                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Available</span>
                                                    @else
                                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Closed</span>
                                                    @endif
                                                </td>
                                                <td class="px-5 py-4 text-slate-700">{{ $a->available_slots ?? '-' }}</td>
                                                <td class="px-5 py-4 text-slate-700">{{ $a->price_idr_override ? number_format($a->price_idr_override, 0, ',', '.') : '-' }}</td>
                                                <td class="px-5 py-4 text-slate-600">{{ $a->note ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap items-center gap-3">
                            <a href="{{ $contactUrl }}" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Tanya Jadwal & Booking</a>
                            <a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm text-slate-700 hover:text-emerald-700">Lihat Paket Lain</a>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="rounded-3xl border border-slate-200 bg-white p-6">
                            <h2 class="text-xl font-semibold text-slate-900">FAQ</h2>
                            <div class="mt-4 space-y-4 text-sm text-slate-700">
                                @forelse ($package->faqs as $faq)
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="font-semibold text-slate-900">{{ $faq->question }}</p>
                                        <p class="mt-2">{{ $faq->answer }}</p>
                                    </div>
                                @empty
                                    <p class="text-slate-600">FAQ akan segera tersedia.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="rounded-3xl border border-slate-200 bg-white p-6">
                            <h2 class="text-xl font-semibold text-slate-900">Ulasan</h2>
                            <p class="mt-2 text-sm text-slate-600">Rating {{ number_format($package->reviews->avg('rating') ?? 4.8, 1) }}/5 • {{ $package->reviews->count() ?: 12 }} review</p>

                            <div class="mt-4 space-y-4 text-sm text-slate-700">
                                @forelse ($package->reviews->take(4) as $review)
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                        <p class="font-semibold text-slate-900">{{ $review->author_name ?? 'Traveler' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">Rating: {{ $review->rating }}/5</p>
                                        @if (!empty($review->review))
                                            <p class="mt-2">{{ $review->review }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-slate-600">Belum ada ulasan.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="sticky top-24 space-y-6">
                    <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-6">
                        <p class="text-xs uppercase tracking-[0.25em] text-emerald-700">Ringkasan</p>
                        <div class="mt-4 space-y-3 text-sm text-emerald-950/90">
                            <div class="flex items-center justify-between">
                                <span>Durasi</span>
                                <span class="font-semibold">{{ $package->duration_days }}D/{{ $package->duration_nights }}N</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Kapasitas</span>
                                <span class="font-semibold">{{ $package->min_people }} - {{ $package->max_people ?? '-' }} pax</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Kategori</span>
                                <span class="font-semibold">{{ $package->category?->name ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>Operator</span>
                                <span class="font-semibold">{{ $package->operator?->name ?? '-' }}</span>
                            </div>
                        </div>

                        <a href="{{ $contactUrl }}" class="mt-6 block rounded-full bg-emerald-600 px-6 py-3 text-center text-sm font-semibold text-white">Konsultasi Sekarang</a>
                        <a href="#availability" class="mt-3 block rounded-full border border-emerald-200 bg-white px-6 py-3 text-center text-sm font-semibold text-emerald-800">Lihat Availability</a>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6">
                        <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Tips</p>
                        <ul class="mt-4 space-y-2 text-sm text-slate-700">
                            <li>Isi <span class="font-mono">meta_title</span> & <span class="font-mono">meta_description</span> per bahasa untuk SEO.</li>
                            <li>Upload gambar primary yang tajam (landscape) agar hero lebih meyakinkan.</li>
                            <li>Tambahkan availability untuk meningkatkan konversi booking.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
