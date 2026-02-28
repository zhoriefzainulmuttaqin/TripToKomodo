@extends('layouts.admin')

@section('title', 'Pengaturan Website')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Pengaturan</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Website Settings</h1>
            <p class="mt-2 text-sm text-slate-600">Atur gambar hero untuk halaman home.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.web-settings.update') }}" enctype="multipart/form-data" class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.7fr]">
            <div>
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

                <div class="mt-8 flex justify-end gap-3">
                    <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
                </div>
            </div>

            <div class="rounded-3xl border border-emerald-100 bg-emerald-50 p-5">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-emerald-700">Catatan</p>
                <ul class="mt-3 space-y-2 text-sm text-emerald-900/90">
                    <li>Gambar disimpan di <span class="font-mono">storage/app/public/web-settings</span>.</li>
                    <li>Pastikan <span class="font-mono">storage:link</span> sudah aktif supaya gambar bisa tampil.</li>
                    <li>Kalau gambar terasa terlalu gelap/terang, kita bisa atur overlay di hero.</li>
                </ul>
            </div>
        </div>
    </form>
@endsection
