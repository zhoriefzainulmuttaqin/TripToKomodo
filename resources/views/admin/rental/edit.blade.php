@extends('layouts.admin')

@section('title', 'Rental Mobil')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Rental Mobil</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">CMS Rental Mobil</h1>
            <p class="mt-2 text-sm text-slate-600">Kelola konten halaman Rental Mobil per bahasa.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.rental.update') }}" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <input type="hidden" name="lang" value="{{ $editLang }}" />

        <div class="mb-6 flex items-center justify-between rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
            <div>
                <p class="text-sm font-semibold text-indigo-900">Bahasa Konten</p>
                <p class="text-xs text-indigo-700">Pilih bahasa untuk menerjemahkan halaman Rental Mobil.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach (['id' => 'Indonesian', 'en' => 'English', 'zh' => 'Chinese', 'es' => 'Spanish', 'de' => 'German', 'ru' => 'Russian'] as $langCode => $langName)
                    <a
                        href="{{ route('admin.rental.edit', ['lang' => $langCode]) }}"
                        class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $editLang === $langCode ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600 hover:bg-indigo-100' }}"
                    >
                        {{ strtoupper($langCode) }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
            <p class="text-sm font-semibold text-slate-900">SEO / Meta ({{ strtoupper($editLang) }})</p>
            <p class="mt-1 text-xs text-slate-500">Jika kosong, halaman akan fallback ke terjemahan bawaan.</p>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Page Title</label>
                    <input type="text" name="rental_page_title" value="{{ old('rental_page_title', $rentalPageTitle ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    @error('rental_page_title')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Keywords (opsional)</label>
                    <input type="text" name="rental_page_keywords" value="{{ old('rental_page_keywords', $rentalPageKeywords ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    @error('rental_page_keywords')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Description</label>
                <textarea name="rental_page_meta" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('rental_page_meta', $rentalPageMeta ?? '') }}</textarea>
                @error('rental_page_meta')
                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-8">
                <p class="text-sm font-semibold text-slate-900">Hero</p>
                <div class="mt-3 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                        <input type="text" name="rental_hero_tag" value="{{ old('rental_hero_tag', $rentalHeroTag ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        @error('rental_hero_tag')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Title</label>
                        <input type="text" name="rental_hero_title" value="{{ old('rental_hero_title', $rentalHeroTitle ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        @error('rental_hero_title')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Description</label>
                    <textarea name="rental_hero_desc" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('rental_hero_desc', $rentalHeroDesc ?? '') }}</textarea>
                    @error('rental_hero_desc')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8">
                <p class="text-sm font-semibold text-slate-900">CTA Box</p>
                <div class="mt-3 grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">CTA Title</label>
                        <input type="text" name="rental_cta_title" value="{{ old('rental_cta_title', $rentalCtaTitle ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        @error('rental_cta_title')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Button Label</label>
                        <input type="text" name="rental_cta_button" value="{{ old('rental_cta_button', $rentalCtaButton ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        @error('rental_cta_button')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">CTA Description</label>
                    <textarea name="rental_cta_desc" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('rental_cta_desc', $rentalCtaDesc ?? '') }}</textarea>
                    @error('rental_cta_desc')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
@endsection
