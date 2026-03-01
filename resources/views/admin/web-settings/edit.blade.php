@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@php
    $section = (string) request()->query('section', 'all');
    $section = $section !== '' ? $section : 'all';

    $tabs = [
        'all' => 'Semua',
        'about' => 'About Us',
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

                    <p class="text-sm font-semibold text-slate-900">About Us (CMS)</p>
                    <p class="mt-1 text-xs text-slate-500">Konten ini dipakai untuk halaman About Us dan potongan About di Home. Jika kosong, akan fallback ke file terjemahan.</p>

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
