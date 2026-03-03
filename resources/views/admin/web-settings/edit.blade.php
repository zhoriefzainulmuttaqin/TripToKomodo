@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@php
    $section = (string) request()->query('section', 'all');
    $section = $section !== '' ? $section : 'all';

    $tabs = [
        'all' => 'Semua',
        'identity' => 'Identitas',
        'footer' => 'Footer',
        'about' => 'About Us',
        'rental' => 'Car Rental',
        'contact' => 'Kontak',
        'home-hero' => 'Home Hero',
    ];

@endphp

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Pengaturan</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Website Settings</h1>
            <p class="mt-2 text-sm text-slate-600">Kelola konten website lewat CMS.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Kembali</a>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        @foreach ($tabs as $key => $label)
            <a
                href="{{ route('admin.web-settings.edit', ['section' => $key]) }}"
                class="rounded-full px-4 py-2 text-xs font-semibold {{ $section === $key ? 'bg-emerald-600 text-white' : 'border border-slate-200 bg-white text-slate-700 hover:text-emerald-700' }}"
            >
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-6">
        <div>
            @if ($section === 'contact')
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="contact" />

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-900">Kontak Website</p>
                        <p class="mt-1 text-xs text-slate-500">Gunakan format internasional untuk WhatsApp (contoh: +62812xxxx).</p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Email</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email', $contactEmail ?? '') }}" placeholder="hello@triptokomodo.com" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('contact_email')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Telepon</label>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone', $contactPhone ?? '') }}" placeholder="+62 812 0000 0000" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('contact_phone')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">WhatsApp</label>
                            <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $contactWhatsapp ?? '') }}" placeholder="+62 812 0000 0000" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            @error('contact_whatsapp')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>

            @elseif ($section === 'identity')
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="identity" />

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-900">Identitas Website</p>
                        <p class="mt-1 text-xs text-slate-500">Atur nama website dan logo yang tampil di navbar, halaman login, dan header admin.</p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Nama Website</label>
                                <input type="text" name="site_name" value="{{ old('site_name', $siteName ?? '') }}" placeholder="Trip to Komodo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('site_name')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tagline (opsional)</label>
                                <input type="text" name="site_tagline" value="{{ old('site_tagline', $siteTagline ?? '') }}" placeholder="" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                <p class="mt-2 text-xs text-slate-500">Kosongkan jika tidak ingin tampil di navbar.</p>
                                @error('site_tagline')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">Logo Website</p>
                            <p class="mt-1 text-xs text-slate-500">Disarankan PNG transparan. Maks 5MB.</p>

                            @if (!empty($siteLogoUrl))
                                <div class="mt-3 flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4">
                                    <img src="{{ $siteLogoUrl }}" alt="Logo" class="h-12 w-12 rounded-xl object-contain bg-slate-50 border border-slate-200" />
                                    <div class="min-w-0">
                                        <p class="text-xs text-slate-500">Path: <span class="font-mono">{{ $siteLogoPath }}</span></p>
                                        <label class="mt-2 flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="remove_site_logo" value="1" class="rounded border-slate-300" />
                                            Hapus logo saat ini
                                        </label>
                                    </div>
                                </div>
                            @else
                                <div class="mt-3 rounded-2xl border border-dashed border-slate-200 bg-white p-4 text-sm text-slate-600">
                                    Belum ada logo yang diatur.
                                </div>
                            @endif

                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Upload logo baru</label>
                                <input type="file" name="site_logo" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('site_logo')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>

            @elseif ($section === 'footer')
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="footer" />
                    <input type="hidden" name="lang" value="{{ $editLang }}" />

                    <div class="mb-6 flex items-center justify-between rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
                        <div>
                            <p class="text-sm font-semibold text-indigo-900">Bahasa Konten</p>
                            <p class="text-xs text-indigo-700">Pilih bahasa untuk menerjemahkan Footer.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['id' => 'Indonesian', 'en' => 'English', 'zh' => 'Chinese', 'es' => 'Spanish', 'de' => 'German', 'ru' => 'Russian'] as $langCode => $langName)
                                <a
                                    href="{{ route('admin.web-settings.edit', ['section' => 'footer', 'lang' => $langCode]) }}"
                                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $editLang === $langCode ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600 hover:bg-indigo-100' }}"
                                >
                                    {{ strtoupper($langCode) }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-slate-900">Footer ({{ strtoupper($editLang) }})</p>
                            @if ($editLang !== 'en')
                                <span class="rounded-md bg-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-600">Terjemahan</span>
                            @else
                                <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Default</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Atur konten footer, metode pembayaran, dan tautan sosial media.</p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Judul Footer</label>
                                <input type="text" name="footer_title" value="{{ old('footer_title', $footerTitle ?? '') }}" placeholder="{{ $siteName ?? 'Trip to Komodo' }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('footer_title')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Copyright (opsional)</label>
                                <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $footerCopyright ?? '') }}" placeholder="© {year} {siteName}. All rights reserved." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                <p class="mt-2 text-xs text-slate-500">Boleh pakai placeholder: <span class="font-mono">{year}</span>, <span class="font-mono">{siteName}</span>.</p>
                                @error('footer_copyright')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Deskripsi Footer</label>
                            <textarea name="footer_description" rows="4" placeholder="Deskripsi singkat di footer" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('footer_description', $footerDescription ?? '') }}</textarea>
                            @error('footer_description')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Metode Pembayaran</label>
                                <textarea name="footer_payment_methods" rows="5" placeholder="Satu baris = satu metode (contoh: Transfer Bank)" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('footer_payment_methods', $footerPaymentMethods ?? '') }}</textarea>
                                <p class="mt-2 text-xs text-slate-500">Contoh: Transfer Bank, QRIS, Visa, Mastercard, PayPal.</p>
                                @error('footer_payment_methods')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Sosial Media</p>
                                <p class="mt-1 text-[10px] text-slate-500">Berlaku global (tidak terjemahan)</p>
                                <div class="mt-2 space-y-3">
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600">Instagram URL</label>
                                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $socialInstagram ?? '') }}" placeholder="https://instagram.com/username" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                        @error('social_instagram')
                                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600">Facebook URL</label>
                                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $socialFacebook ?? '') }}" placeholder="https://facebook.com/page" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                        @error('social_facebook')
                                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600">TikTok URL</label>
                                        <input type="url" name="social_tiktok" value="{{ old('social_tiktok', $socialTiktok ?? '') }}" placeholder="https://tiktok.com/@username" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                        @error('social_tiktok')
                                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-600">YouTube URL</label>
                                        <input type="url" name="social_youtube" value="{{ old('social_youtube', $socialYoutube ?? '') }}" placeholder="https://youtube.com/@channel" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                        @error('social_youtube')
                                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>

            @elseif ($section === 'rental')
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="rental" />
                    <input type="hidden" name="lang" value="{{ $editLang }}" />

                    <div class="mb-6 flex items-center justify-between rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
                        <div>
                            <p class="text-sm font-semibold text-indigo-900">Bahasa Konten</p>
                            <p class="text-xs text-indigo-700">Pilih bahasa untuk menerjemahkan halaman Car Rental.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['id' => 'Indonesian', 'en' => 'English', 'zh' => 'Chinese', 'es' => 'Spanish', 'de' => 'German', 'ru' => 'Russian'] as $langCode => $langName)
                                <a
                                    href="{{ route('admin.web-settings.edit', ['section' => 'rental', 'lang' => $langCode]) }}"
                                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $editLang === $langCode ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600 hover:bg-indigo-100' }}"
                                >
                                    {{ strtoupper($langCode) }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-slate-900">Car Rental (CMS) - {{ strtoupper($editLang) }}</p>
                        @if ($editLang !== 'en')
                            <span class="rounded-md bg-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-600">Terjemahan</span>
                        @else
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Default</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Jika kosong, halaman akan fallback ke file terjemahan bawaan <span class="font-mono">resources/lang/*/pages.php</span>.</p>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-900">SEO / Meta</p>
                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Page Title</label>
                                <input type="text" name="rental_page_title" value="{{ old('rental_page_title', $rentalPageTitle ?? '') }}" placeholder="Rental Mobil Labuan Bajo | TriptoKomodo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('rental_page_title')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Keywords (opsional)</label>
                                <input type="text" name="rental_page_keywords" value="{{ old('rental_page_keywords', $rentalPageKeywords ?? '') }}" placeholder="rental mobil labuan bajo, sewa mobil flores" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('rental_page_keywords')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Meta Description</label>
                            <textarea name="rental_page_meta" rows="3" placeholder="Rental mobil Labuan Bajo: driver profesional, unit nyaman, dan itinerary fleksibel..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('rental_page_meta', $rentalPageMeta ?? '') }}</textarea>
                            @error('rental_page_meta')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8">
                            <p class="text-sm font-semibold text-slate-900">Hero</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                                    <input type="text" name="rental_hero_tag" value="{{ old('rental_hero_tag', $rentalHeroTag ?? '') }}" placeholder="Rental Mobil" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('rental_hero_tag')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Title</label>
                                    <input type="text" name="rental_hero_title" value="{{ old('rental_hero_title', $rentalHeroTitle ?? '') }}" placeholder="Rental Mobil Labuan Bajo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('rental_hero_title')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Description</label>
                                <textarea name="rental_hero_desc" rows="4" placeholder="Jelaskan layanan rental: unit, driver, area, dll." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('rental_hero_desc', $rentalHeroDesc ?? '') }}</textarea>
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
                                    <input type="text" name="rental_cta_title" value="{{ old('rental_cta_title', $rentalCtaTitle ?? '') }}" placeholder="Butuh rekomendasi cepat?" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('rental_cta_title')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Button Label</label>
                                    <input type="text" name="rental_cta_button" value="{{ old('rental_cta_button', $rentalCtaButton ?? '') }}" placeholder="Konsultasi" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('rental_cta_button')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">CTA Description</label>
                                <textarea name="rental_cta_desc" rows="3" placeholder="Teks ajakan singkat sebelum tombol." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('rental_cta_desc', $rentalCtaDesc ?? '') }}</textarea>
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

            @elseif ($section === 'home-hero')
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">

                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="home-hero" />

                    <p class="text-sm font-semibold text-slate-900">Home Hero Background</p>
                    <p class="mt-1 text-xs text-slate-500">Disarankan gambar landscape (misal 1600×900). Maks 5MB.</p>

                    @if (!empty($heroBackgroundUrl))
                        <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            <img src="{{ $heroBackgroundUrl }}" alt="Preview hero" class="h-56 w-full object-cover" />
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Path: <span class="font-mono">{{ $heroBackgroundPath }}</span></p>

                        <label class="mt-4 flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="remove_hero_background" value="1" class="rounded border-slate-300" />
                            Hapus gambar hero saat ini
                        </label>
                    @else
                        <div class="mt-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-sm text-slate-600">
                            Belum ada gambar hero yang diatur.
                        </div>
                    @endif

                    <div class="mt-6">
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Upload gambar baru</label>
                        <input type="file" name="hero_background_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        @error('hero_background_image')
                            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>

            @elseif ($section === 'about')
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="about" />
                    <input type="hidden" name="lang" value="{{ $editLang }}" />

                    <div class="mb-6 flex items-center justify-between rounded-2xl border border-indigo-100 bg-indigo-50/50 p-4">
                        <div>
                            <p class="text-sm font-semibold text-indigo-900">Bahasa Konten</p>
                            <p class="text-xs text-indigo-700">Pilih bahasa untuk menerjemahkan About Us.</p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach (['id' => 'Indonesian', 'en' => 'English', 'zh' => 'Chinese', 'es' => 'Spanish', 'de' => 'German', 'ru' => 'Russian'] as $langCode => $langName)
                                <a
                                    href="{{ route('admin.web-settings.edit', ['section' => 'about', 'lang' => $langCode]) }}"
                                    class="rounded-full px-3 py-1.5 text-xs font-semibold transition-colors {{ $editLang === $langCode ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-600 hover:bg-indigo-100' }}"
                                >
                                    {{ strtoupper($langCode) }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-slate-900">About Us (CMS) - {{ strtoupper($editLang) }}</p>
                        @if ($editLang !== 'en')
                            <span class="rounded-md bg-slate-200 px-2 py-0.5 text-[10px] font-medium text-slate-600">Terjemahan</span>
                        @else
                            <span class="rounded-md bg-emerald-100 px-2 py-0.5 text-[10px] font-medium text-emerald-700">Default</span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Konten ini dipakai untuk halaman About Us dan potongan About di Home. Jika kosong, akan fallback ke file terjemahan bawaan.</p>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                                <input type="text" name="about_tag" value="{{ old('about_tag', $aboutTag ?? '') }}" placeholder="ABOUT US" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('about_tag')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Image Alt</label>
                                <input type="text" name="about_image_alt" value="{{ old('about_image_alt', $aboutImageAlt ?? '') }}" placeholder="About TriptoKomodo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('about_image_alt')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Headline</label>
                            <input type="text" name="about_headline" value="{{ old('about_headline', $aboutHeadline ?? '') }}" placeholder="TriptoKomodo — ..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            @error('about_headline')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Subheadline</label>
                            <input type="text" name="about_subheadline" value="{{ old('about_subheadline', $aboutSubheadline ?? '') }}" placeholder="We are a locally based operator..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            @error('about_subheadline')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Lead (paragraf)</label>
                            <textarea name="about_lead" rows="5" placeholder="Satu baris = satu paragraf" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('about_lead', $aboutLead ?? '') }}</textarea>
                            <p class="mt-2 text-xs text-slate-500">Pisahkan paragraf dengan baris baru.</p>
                            @error('about_lead')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Badge</label>
                                <input type="text" name="about_badge" value="{{ old('about_badge', $aboutBadge ?? '') }}" placeholder="Labuan Bajo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('about_badge')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Badge Title</label>
                                <input type="text" name="about_badge_title" value="{{ old('about_badge_title', $aboutBadgeTitle ?? '') }}" placeholder="Sunrise sailing..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('about_badge_title')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Badge Desc</label>
                                <input type="text" name="about_badge_desc" value="{{ old('about_badge_desc', $aboutBadgeDesc ?? '') }}" placeholder="Small caption..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('about_badge_desc')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">Stats</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Stat 1</p>
                                    <input type="text" name="about_stat_1_value" value="{{ old('about_stat_1_value', $aboutStat1Value ?? '') }}" placeholder="2015" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <input type="text" name="about_stat_1_label" value="{{ old('about_stat_1_label', $aboutStat1Label ?? '') }}" placeholder="Established" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Stat 2</p>
                                    <input type="text" name="about_stat_2_value" value="{{ old('about_stat_2_value', $aboutStat2Value ?? '') }}" placeholder="150+" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <input type="text" name="about_stat_2_label" value="{{ old('about_stat_2_label', $aboutStat2Label ?? '') }}" placeholder="Trips completed" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Stat 3</p>
                                    <input type="text" name="about_stat_3_value" value="{{ old('about_stat_3_value', $aboutStat3Value ?? '') }}" placeholder="4.9/5" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <input type="text" name="about_stat_3_label" value="{{ old('about_stat_3_label', $aboutStat3Label ?? '') }}" placeholder="Guest rating" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <p class="text-sm font-semibold text-slate-900">Vision</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                                    <input type="text" name="about_vision_tag" value="{{ old('about_vision_tag', $aboutVisionTag ?? '') }}" placeholder="Visi" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('about_vision_tag')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Title</label>
                                    <input type="text" name="about_vision_title" value="{{ old('about_vision_title', $aboutVisionTitle ?? '') }}" placeholder="Menjadi operator Komodo..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('about_vision_title')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Body</label>
                                <textarea name="about_vision_body" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('about_vision_body', $aboutVisionBody ?? '') }}</textarea>
                                @error('about_vision_body')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8">
                            <p class="text-sm font-semibold text-slate-900">Mission</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                                    <input type="text" name="about_mission_tag" value="{{ old('about_mission_tag', $aboutMissionTag ?? '') }}" placeholder="Misi" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('about_mission_tag')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Title</label>
                                    <input type="text" name="about_mission_title" value="{{ old('about_mission_title', $aboutMissionTitle ?? '') }}" placeholder="Memberikan perjalanan..." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('about_mission_title')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Body</label>
                                <textarea name="about_mission_body" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('about_mission_body', $aboutMissionBody ?? '') }}</textarea>
                                @error('about_mission_body')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8">
                            <p class="text-sm font-semibold text-slate-900">Values</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                                    <input type="text" name="about_values_tag" value="{{ old('about_values_tag', $aboutValuesTag ?? '') }}" placeholder="Komitmen Kami" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('about_values_tag')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Title</label>
                                    <input type="text" name="about_values_title" value="{{ old('about_values_title', $aboutValuesTitle ?? '') }}" placeholder="Kenapa memilih TriptoKomodo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    @error('about_values_title')
                                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Desc</label>
                                <textarea name="about_values_desc" rows="2" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('about_values_desc', $aboutValuesDesc ?? '') }}</textarea>
                                @error('about_values_desc')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-semibold text-slate-900">Items</p>
                                <div class="mt-3 grid gap-4 md:grid-cols-3">
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Item 1</p>
                                        <input type="text" name="about_values_item_1_title" value="{{ old('about_values_item_1_title', $aboutValuesItem1Title ?? '') }}" placeholder="Keamanan & kenyamanan" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                        <textarea name="about_values_item_1_desc" rows="3" placeholder="Deskripsi" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ old('about_values_item_1_desc', $aboutValuesItem1Desc ?? '') }}</textarea>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Item 2</p>
                                        <input type="text" name="about_values_item_2_title" value="{{ old('about_values_item_2_title', $aboutValuesItem2Title ?? '') }}" placeholder="Harga transparan" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                        <textarea name="about_values_item_2_desc" rows="3" placeholder="Deskripsi" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ old('about_values_item_2_desc', $aboutValuesItem2Desc ?? '') }}</textarea>
                                    </div>
                                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Item 3</p>
                                        <input type="text" name="about_values_item_3_title" value="{{ old('about_values_item_3_title', $aboutValuesItem3Title ?? '') }}" placeholder="Itinerary lokal" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                        <textarea name="about_values_item_3_desc" rows="3" placeholder="Deskripsi" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ old('about_values_item_3_desc', $aboutValuesItem3Desc ?? '') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <p class="text-sm font-semibold text-slate-900">Highlights</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Highlight 1</p>
                                    <input type="text" name="about_highlights_1_title" value="{{ old('about_highlights_1_title', $aboutHighlights1Title ?? '') }}" placeholder="Best Destination" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <textarea name="about_highlights_1_desc" rows="3" placeholder="Deskripsi" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ old('about_highlights_1_desc', $aboutHighlights1Desc ?? '') }}</textarea>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Highlight 2</p>
                                    <input type="text" name="about_highlights_2_title" value="{{ old('about_highlights_2_title', $aboutHighlights2Title ?? '') }}" placeholder="Affordable Price" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <textarea name="about_highlights_2_desc" rows="3" placeholder="Deskripsi" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ old('about_highlights_2_desc', $aboutHighlights2Desc ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">About Image</p>
                            @if (!empty($aboutImageUrl))
                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                    <img src="{{ $aboutImageUrl }}" alt="Preview about" class="h-56 w-full object-cover" />
                                </div>
                                <p class="mt-2 text-xs text-slate-500">Path: <span class="font-mono">{{ $aboutImagePath }}</span></p>

                                <label class="mt-4 flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="remove_about_image" value="1" class="rounded border-slate-300" />
                                    Hapus gambar About saat ini
                                </label>
                            @else
                                <div class="mt-3 rounded-2xl border border-dashed border-slate-200 bg-white p-4 text-sm text-slate-600">
                                    Belum ada gambar About yang diatur.
                                </div>
                            @endif

                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Upload gambar About</label>
                                <input type="file" name="about_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('about_image')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>

            @else
                {{-- ALL (default) --}}
                <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="section" value="all" />

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-900">Identitas Website</p>
                        <p class="mt-1 text-xs text-slate-500">Nama website & logo yang tampil di navbar, halaman login, dan header admin.</p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Nama Website</label>
                                <input type="text" name="site_name" value="{{ old('site_name', $siteName ?? '') }}" placeholder="Trip to Komodo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tagline (opsional)</label>
                                <input type="text" name="site_tagline" value="{{ old('site_tagline', $siteTagline ?? '') }}" placeholder="" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                <p class="mt-2 text-xs text-slate-500">Kosongkan jika tidak ingin tampil di navbar.</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">Logo Website</p>
                            @if (!empty($siteLogoUrl))
                                <div class="mt-3 flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-4">
                                    <img src="{{ $siteLogoUrl }}" alt="Logo" class="h-12 w-12 rounded-xl object-contain bg-slate-50 border border-slate-200" />
                                    <div>
                                        <label class="flex items-center gap-2 text-sm text-slate-700">
                                            <input type="checkbox" name="remove_site_logo" value="1" class="rounded border-slate-300" />
                                            Hapus logo saat ini
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Upload logo baru</label>
                                <input type="file" name="site_logo" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-900">Footer</p>
                        <p class="mt-1 text-xs text-slate-500">Konten footer, metode pembayaran, dan sosial media.</p>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Judul Footer</label>
                                <input type="text" name="footer_title" value="{{ old('footer_title', $footerTitle ?? '') }}" placeholder="{{ $siteName ?? 'Trip to Komodo' }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Copyright (opsional)</label>
                                <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $footerCopyright ?? '') }}" placeholder="© {year} {siteName}. All rights reserved." class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                <p class="mt-2 text-xs text-slate-500">Placeholder: <span class="font-mono">{year}</span>, <span class="font-mono">{siteName}</span>.</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Deskripsi Footer</label>
                            <textarea name="footer_description" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('footer_description', $footerDescription ?? '') }}</textarea>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Metode Pembayaran</label>
                                <textarea name="footer_payment_methods" rows="4" placeholder="Satu baris = satu metode" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('footer_payment_methods', $footerPaymentMethods ?? '') }}</textarea>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Sosial Media</p>
                                <div class="mt-2 grid gap-3 md:grid-cols-2">
                                    <input type="url" name="social_instagram" value="{{ old('social_instagram', $socialInstagram ?? '') }}" placeholder="Instagram URL" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    <input type="url" name="social_facebook" value="{{ old('social_facebook', $socialFacebook ?? '') }}" placeholder="Facebook URL" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    <input type="url" name="social_tiktok" value="{{ old('social_tiktok', $socialTiktok ?? '') }}" placeholder="TikTok URL" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                    <input type="url" name="social_youtube" value="{{ old('social_youtube', $socialYoutube ?? '') }}" placeholder="YouTube URL" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-900">Kontak Website</p>
                        <p class="mt-1 text-xs text-slate-500">Gunakan format internasional untuk WhatsApp (contoh: +62812xxxx).</p>


                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Email</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email', $contactEmail ?? '') }}" placeholder="hello@triptokomodo.com" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Telepon</label>
                                <input type="text" name="contact_phone" value="{{ old('contact_phone', $contactPhone ?? '') }}" placeholder="+62 812 0000 0000" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">WhatsApp</label>
                            <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $contactWhatsapp ?? '') }}" placeholder="+62 812 0000 0000" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        </div>
                    </div>

                    <p class="mt-8 text-sm font-semibold text-slate-900">About Us (CMS)</p>
                    <p class="mt-1 text-xs text-slate-500">Konten ini dipakai untuk halaman About Us dan potongan About di Home. Jika kosong, akan fallback ke file terjemahan.</p>

                    <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Tag</label>
                                <input type="text" name="about_tag" value="{{ old('about_tag', $aboutTag ?? '') }}" placeholder="ABOUT US" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Image Alt</label>
                                <input type="text" name="about_image_alt" value="{{ old('about_image_alt', $aboutImageAlt ?? '') }}" placeholder="About TriptoKomodo" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Headline</label>
                            <input type="text" name="about_headline" value="{{ old('about_headline', $aboutHeadline ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Subheadline</label>
                            <input type="text" name="about_subheadline" value="{{ old('about_subheadline', $aboutSubheadline ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Lead (paragraf)</label>
                            <textarea name="about_lead" rows="5" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">{{ old('about_lead', $aboutLead ?? '') }}</textarea>
                        </div>

                        <div class="mt-6 grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Badge</label>
                                <input type="text" name="about_badge" value="{{ old('about_badge', $aboutBadge ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Badge Title</label>
                                <input type="text" name="about_badge_title" value="{{ old('about_badge_title', $aboutBadgeTitle ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Badge Desc</label>
                                <input type="text" name="about_badge_desc" value="{{ old('about_badge_desc', $aboutBadgeDesc ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">Stats</p>
                            <div class="mt-3 grid gap-4 md:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <input type="text" name="about_stat_1_value" value="{{ old('about_stat_1_value', $aboutStat1Value ?? '') }}" placeholder="2015" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <input type="text" name="about_stat_1_label" value="{{ old('about_stat_1_label', $aboutStat1Label ?? '') }}" placeholder="Established" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <input type="text" name="about_stat_2_value" value="{{ old('about_stat_2_value', $aboutStat2Value ?? '') }}" placeholder="150+" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <input type="text" name="about_stat_2_label" value="{{ old('about_stat_2_label', $aboutStat2Label ?? '') }}" placeholder="Trips completed" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white p-4">
                                    <input type="text" name="about_stat_3_value" value="{{ old('about_stat_3_value', $aboutStat3Value ?? '') }}" placeholder="4.9/5" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                    <input type="text" name="about_stat_3_label" value="{{ old('about_stat_3_label', $aboutStat3Label ?? '') }}" placeholder="Guest rating" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <p class="text-sm font-semibold text-slate-900">About Image</p>
                            @if (!empty($aboutImageUrl))
                                <div class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                                    <img src="{{ $aboutImageUrl }}" alt="Preview about" class="h-56 w-full object-cover" />
                                </div>
                                <label class="mt-4 flex items-center gap-2 text-sm text-slate-700">
                                    <input type="checkbox" name="remove_about_image" value="1" class="rounded border-slate-300" />
                                    Hapus gambar About saat ini
                                </label>
                            @endif

                            <div class="mt-4">
                                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Upload gambar About</label>
                                <input type="file" name="about_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                            </div>
                        </div>
                    </div>

                    <p class="mt-8 text-sm font-semibold text-slate-900">Home Hero Background</p>
                    @if (!empty($heroBackgroundUrl))
                        <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            <img src="{{ $heroBackgroundUrl }}" alt="Preview hero" class="h-56 w-full object-cover" />
                        </div>
                        <label class="mt-4 flex items-center gap-2 text-sm text-slate-700">
                            <input type="checkbox" name="remove_hero_background" value="1" class="rounded border-slate-300" />
                            Hapus gambar hero saat ini
                        </label>
                    @endif

                    <div class="mt-6">
                        <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Upload gambar baru</label>
                        <input type="file" name="hero_background_image" accept="image/*" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
