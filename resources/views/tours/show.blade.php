@extends('layouts.app')

@section('title', ($translation->meta_title ?? $translation->title) . ' | Labuan Bajo')
@section('meta_description', $translation->meta_description ?? $translation->summary ?? 'Paket trip Labuan Bajo premium.')
@section('canonical', url()->current())

@section('content')
    @php
        $currencySymbol = $activeCurrencies->firstWhere('code', $pricing['currency_code'])?->symbol ?? $pricing['currency_code'];
        $priceFormatted = number_format($pricing['selling_price_converted'], 0, ',', '.');
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
    @endphp

    @push('schema')
        <script type="application/ld+json">{!! json_encode($tourSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($reviewSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="mx-auto max-w-6xl px-6 py-16">
        <div class="grid gap-10 lg:grid-cols-[1.4fr_0.8fr]">
            <div>
                <p class="text-sm uppercase tracking-[0.3em] text-emerald-300">{{ $package->duration_days }} Hari • {{ $package->duration_nights }} Malam</p>
                <h1 class="mt-3 text-3xl font-semibold">{{ $translation->title }}</h1>
                <p class="mt-4 text-sm text-slate-300">{{ $translation->summary }}</p>

                <div class="mt-8 grid gap-4 md:grid-cols-2">
                    @foreach ($package->images as $image)
                        <div class="h-48 rounded-2xl bg-slate-800" style="background-image:url('{{ $image->url }}'); background-size:cover; background-position:center;"></div>
                    @endforeach
                </div>

                <div class="mt-10 space-y-8 text-sm text-slate-300">
                    <div>
                        <h2 class="text-xl font-semibold text-white">Deskripsi Trip</h2>
                        <div class="prose prose-invert mt-3 max-w-none">{!! $descriptionHtml !!}</div>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-white">Itinerary</h2>
                        <div class="prose prose-invert mt-3 max-w-none">{!! nl2br(e($translation->itinerary ?? 'Itinerary lengkap akan diinformasikan oleh concierge.')) !!}</div>
                    </div>
                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <h3 class="text-lg font-semibold text-white">Termasuk</h3>
                            <div class="mt-3 text-sm text-slate-300">{!! nl2br(e($translation->includes ?? 'Akomodasi kapal, makan, crew, dan dokumentasi.')) !!}</div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-white">Tidak Termasuk</h3>
                            <div class="mt-3 text-sm text-slate-300">{!! nl2br(e($translation->excludes ?? 'Tiket pesawat, asuransi pribadi, dan pengeluaran pribadi.')) !!}</div>
                        </div>
                    </div>
                </div>

                <div class="mt-10 rounded-3xl border border-white/10 bg-white/5 p-6">
                    <h2 class="text-xl font-semibold">FAQ</h2>
                    <div class="mt-4 space-y-4 text-sm text-slate-300">
                        @forelse ($package->faqs as $faq)
                            <div>
                                <p class="font-semibold text-white">{{ $faq->question }}</p>
                                <p>{{ $faq->answer }}</p>
                            </div>
                        @empty
                            <p>FAQ akan segera tersedia.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <aside class="rounded-3xl border border-white/10 bg-white/5 p-6 h-fit">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-300">Harga Mulai</p>
                <p class="mt-3 text-3xl font-semibold">{{ $currencySymbol }} {{ $priceFormatted }}</p>
                <p class="mt-2 text-sm text-slate-300">Sudah termasuk penyesuaian kurs & margin terbaik.</p>

                <div class="mt-6 space-y-3 text-sm text-slate-300">
                    <div class="flex items-center justify-between">
                        <span>Operator</span>
                        <span class="text-white">{{ $package->operator->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Min. peserta</span>
                        <span class="text-white">{{ $package->min_people }} orang</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Status</span>
                        <span class="text-white">{{ ucfirst($package->status) }}</span>
                    </div>
                </div>

                <a href="#contact" class="mt-6 block rounded-full bg-emerald-400 px-6 py-3 text-center text-sm font-semibold text-slate-950">Konsultasi Trip</a>
            </aside>
        </div>
    </section>
@endsection
