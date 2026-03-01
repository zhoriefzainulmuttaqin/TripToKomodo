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

@push('styles')
    <style>[x-cloak] { display: none !important; }</style>
@endpush

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

        $isHtml = function (?string $text): bool {
            if (!$text) {
                return false;
            }

            return $text !== strip_tags($text);
        };

        $formatLines = function (?string $text) use ($isHtml): array {
            if (!$text || $isHtml($text)) {
                return [];
            }
            $lines = preg_split('/\r\n|\r|\n/', $text);
            $lines = array_map(fn ($s) => trim((string) $s), $lines);
            $lines = array_values(array_filter($lines, fn ($s) => $s !== ''));
            return $lines;
        };

        $includesIsHtml = $isHtml($translation->includes);
        $excludesIsHtml = $isHtml($translation->excludes);
        $itineraryIsHtml = $isHtml($translation->itinerary);

        $includesLines = $formatLines($translation->includes);
        $excludesLines = $formatLines($translation->excludes);

        $priceSuffixes = [
            'id' => '/ orang',
            'en' => '/ person',
            'ru' => '/ чел.',
            'zh' => '/ 人',
            'de' => '/ Person',
        ];
        $priceSuffix = $priceSuffixes[app()->getLocale()] ?? '/ person';
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
                    <p class="mt-3 text-3xl font-semibold">{{ $currencySymbol }} {{ $priceFormatted }} <span class="text-lg font-normal text-white/80">{{ $priceSuffix }}</span></p>
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
            <div class="min-w-0">
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
                        <div class="prose mt-4 max-w-none break-words text-slate-700">{!! $descriptionHtml !!}</div>
                    </div>

                    <div>
                        <h2 class="text-2xl font-semibold text-slate-900">Itinerary</h2>
                        <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-700 break-words">
                            @if ($itineraryIsHtml)
                                <div class="prose max-w-none break-words">{!! $translation->itinerary !!}</div>
                            @else
                                <div class="break-words">{!! nl2br(e($translation->itinerary ?? 'Itinerary lengkap akan diinformasikan oleh concierge.')) !!}</div>
                            @endif
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="rounded-3xl border border-emerald-100 bg-emerald-50/60 p-6">
                            <h3 class="text-lg font-semibold text-emerald-900">Included</h3>
                            @if ($includesIsHtml)
                                <div class="prose mt-4 max-w-none text-emerald-950/80 break-words">{!! $translation->includes !!}</div>
                            @elseif (!empty($includesLines))
                                <ul class="mt-4 space-y-2 text-sm text-emerald-950/80">
                                    @foreach ($includesLines as $line)
                                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-emerald-600"></span><span class="break-words">{{ $line }}</span></li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="mt-4 text-sm text-emerald-950/80">Akomodasi kapal, makan, crew, dan dokumentasi.</p>
                            @endif
                        </div>

                        <div class="rounded-3xl border border-rose-100 bg-rose-50/60 p-6">
                            <h3 class="text-lg font-semibold text-rose-900">Excluded</h3>
                            @if ($excludesIsHtml)
                                <div class="prose mt-4 max-w-none text-rose-950/80 break-words">{!! $translation->excludes !!}</div>
                            @elseif (!empty($excludesLines))
                                <ul class="mt-4 space-y-2 text-sm text-rose-950/80">
                                    @foreach ($excludesLines as $line)
                                        <li class="flex gap-2"><span class="mt-1 h-1.5 w-1.5 rounded-full bg-rose-600"></span><span class="break-words">{{ $line }}</span></li>
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
                            <div class="mt-4 rounded-3xl border border-slate-200 bg-white p-6 text-sm text-slate-700 break-words">{!! nl2br(e($translation->transportation)) !!}</div>
                        </div>
                    @endif

                    @if (($package->destinations ?? collect())->isNotEmpty())
                        @php
                            $locale = app()->getLocale();
                            $fallbackLocale = (string) config('app.fallback_locale', 'en');
                            $destinationData = $package->destinations->map(function ($d) use ($locale, $fallbackLocale) {
                                $translation = $d->translationFor($locale, $fallbackLocale);

                                return [
                                    'id' => $d->id,
                                    'name' => $translation?->name ?? $d->name,
                                    'image' => $d->image,
                                    'description' => $translation?->description ?? $d->description,
                                    'category' => $translation?->category ?? $d->category,
                                    'distance' => $translation?->distance ?? $d->distance,
                                    'lat' => $d->lat,
                                    'lng' => $d->lng,
                                ];
                            })->values();
                            $destinationChunks = $destinationData->chunk(2);
                        @endphp

                        <div id="destinations" x-data="{
                            current: 0,
                            total: {{ $destinationChunks->count() }},
                            next() { this.current = (this.current + 1) % this.total; },
                            prev() { this.current = (this.current - 1 + this.total) % this.total; },
                        }">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-semibold text-slate-900">Destinasi</h2>
                                <div class="flex items-center gap-2">
                                    <button @click="prev()" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                    </button>
                                    <button @click="next()" class="flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition-all hover:border-emerald-300 hover:bg-emerald-50 hover:text-emerald-700">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Card Slider --}}
                            <div class="mt-6 overflow-hidden">
                                <div class="flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${current * 100}%)`">
                                    @foreach ($destinationChunks as $chunkIndex => $chunk)
                                        <div class="w-full flex-shrink-0 px-1">
                                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                                @foreach ($chunk as $dest)
                                                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                                        {{-- Image --}}
                                                        <div class="relative h-64 w-full bg-slate-100">
                                                            @if (!empty($dest['image']))
                                                                <img src="{{ asset('storage/' . $dest['image']) }}" alt="{{ $dest['name'] }}" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                                <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-emerald-100 to-slate-200">
                                                                    <svg class="h-16 w-16 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                </div>
                                                            @else
                                                                <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-emerald-100 to-slate-200">
                                                                    <svg class="h-16 w-16 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                            {{-- Category Badge --}}
                                                            @if (!empty($dest['category']))
                                                                <span class="absolute left-4 top-4 rounded-full border border-white/50 bg-white/90 px-3 py-1 text-xs font-medium text-emerald-700 backdrop-blur-sm">{{ $dest['category'] }}</span>
                                                            @endif
                                                        </div>
                                                        {{-- Content --}}
                                                        <div class="p-6 text-center">
                                                            <h3 class="text-xl font-semibold text-slate-900">{{ $dest['name'] }}</h3>
                                                            @if (!empty($dest['description']))
                                                                <p class="mt-3 text-sm leading-relaxed text-slate-600 line-clamp-3">{{ $dest['description'] }}</p>
                                                            @else
                                                                <p class="mt-3 text-sm text-slate-500">Destinasi menarik yang akan dikunjungi selama trip.</p>
                                                            @endif
                                                            {{-- Maps Link --}}
                                                            @if (!empty($dest['lat']) && !empty($dest['lng']))
                                                                <a href="https://www.google.com/maps?q={{ $dest['lat'] }},{{ $dest['lng'] }}" target="_blank" rel="noopener" class="mt-4 inline-flex items-center gap-1 text-sm text-emerald-600 hover:text-emerald-700">
                                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                    </svg>
                                                                    Lihat di Maps
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Dots Indicator --}}
                            <div class="mt-4 flex justify-center gap-2">
                                @foreach ($destinationChunks as $index => $chunk)
                                    <button @click="current = {{ $index }}" :class="{ 'bg-emerald-600 w-6': current === {{ $index }}, 'bg-slate-300 w-2': current !== {{ $index }} }" class="h-2 rounded-full transition-all duration-300"></button>
                                @endforeach
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
                            @php
                                $availabilitiesByMonth = $package->availabilities->groupBy(fn($a) => $a->date->format('Y-m'));
                                $locale = app()->getLocale();
                                $monthNames = [
                                    'id' => ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                    'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                                ];
                                $jsMonthNames = [];
                                foreach($availabilitiesByMonth as $ym => $ma) {
                                    [$y, $m] = explode('-', $ym);
                                    $mi = (int)$m - 1;
                                    $mn = $monthNames[$locale][$mi] ?? $monthNames['en'][$mi];
                                    $jsMonthNames[] = "$mn $y";
                                }
                            @endphp

                            <div class="mt-4" x-data="{ 
                                currentIdx: 0, 
                                total: {{ $availabilitiesByMonth->count() }},
                                monthNames: {{ json_encode($jsMonthNames) }},
                                next() { this.currentIdx = (this.currentIdx + 1) % this.total; },
                                prev() { this.currentIdx = (this.currentIdx - 1 + this.total) % this.total; }
                            }">
                                {{-- Month Navigation --}}
                                <div class="mb-4 flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3">
                                    <button @click="prev()" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-emerald-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    
                                    <span class="font-semibold text-slate-900" x-text="monthNames[currentIdx]"></span>
                                    
                                    <button @click="next()" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 hover:bg-slate-100 hover:text-emerald-600 transition-colors">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>

                                {{-- Calendar Slides --}}
                                <div class="relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-6">
                                    @foreach ($availabilitiesByMonth as $yearMonth => $monthAvailabilities)
                                        @php
                                            [$year, $month] = explode('-', $yearMonth);
                                            $firstDayOfMonth = \Carbon\Carbon::create($year, $month, 1);
                                            $daysInMonth = $firstDayOfMonth->daysInMonth;
                                            $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
                                            $dayNames = $locale === 'id' 
                                                ? ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
                                                : ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                        @endphp
                                        <div x-show="currentIdx === {{ $loop->index }}" 
                                             x-transition:enter="transition ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             class="w-full"
                                             {{ $loop->index === 0 ? '' : 'x-cloak' }}>
                                            
                                            <div class="grid grid-cols-7 gap-1 text-center">
                                                @foreach ($dayNames as $dayName)
                                                    <div class="py-2 text-xs font-medium text-slate-500">{{ $dayName }}</div>
                                                @endforeach
                                                
                                                @for ($i = 0; $i < $startDayOfWeek; $i++)
                                                    <div class="aspect-square"></div>
                                                @endfor
                                                
                                                @for ($day = 1; $day <= $daysInMonth; $day++)
                                                    @php
                                                        $availability = $monthAvailabilities->first(fn($a) => $a->date->day === $day);
                                                    @endphp
                                                    @if ($availability)
                                                        <div class="group relative aspect-square">
                                                            <div class="flex h-full w-full flex-col items-center justify-center rounded-xl border transition-all {{ $availability->is_available ? 'border-emerald-200 bg-emerald-50 hover:bg-emerald-100' : 'border-rose-200 bg-rose-50 hover:bg-rose-100' }}">
                                                                <span class="text-sm font-semibold {{ $availability->is_available ? 'text-emerald-700' : 'text-rose-700' }}">{{ $day }}</span>
                                                                @if ($availability->is_available && $availability->available_slots)
                                                                    <span class="mt-0.5 text-[10px] {{ $availability->is_available ? 'text-emerald-600' : 'text-rose-600' }}">{{ $availability->available_slots }} slot</span>
                                                                @endif
                                                            </div>
                                                            @if ($availability->note || $availability->price_idr_override)
                                                                <div class="absolute bottom-full left-1/2 z-10 mb-2 hidden w-40 -translate-x-1/2 rounded-xl border border-slate-200 bg-white p-3 shadow-lg group-hover:block">
                                                                    @if ($availability->price_idr_override)
                                                                        <p class="text-xs font-semibold text-emerald-700">Rp {{ number_format($availability->price_idr_override, 0, ',', '.') }}</p>
                                                                    @endif
                                                                    @if ($availability->note)
                                                                        <p class="mt-1 text-xs text-slate-600">{{ $availability->note }}</p>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="aspect-square flex items-center justify-center">
                                                            <span class="text-sm text-slate-300">{{ $day }}</span>
                                                        </div>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    @endforeach
                                    
                                    <div class="mt-4 flex items-center gap-4 text-xs border-t border-slate-100 pt-4">
                                        <div class="flex items-center gap-2">
                                            <div class="h-4 w-4 rounded border border-emerald-200 bg-emerald-50"></div>
                                            <span class="text-slate-600">Tersedia</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="h-4 w-4 rounded border border-rose-200 bg-rose-50"></div>
                                            <span class="text-slate-600">Penuh/Tutup</span>
                                        </div>
                                    </div>
                                </div>
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
                                        <p class="font-semibold text-slate-900 break-words">{{ $faq->question }}</p>
                                        <p class="mt-2 break-words">{{ $faq->answer }}</p>
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
                                            <p class="mt-2 break-words">{{ $review->review }}</p>
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
