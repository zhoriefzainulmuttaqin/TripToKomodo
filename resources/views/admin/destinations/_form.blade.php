@php
    $isEdit = isset($destination);
    $fallbackLocale = (string) config('app.fallback_locale', 'en');
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Terjemahan</p>
        <h2 class="mt-2 text-lg font-semibold text-slate-900">Konten Destinasi per Bahasa</h2>
        <p class="mt-2 text-xs text-slate-500">Bahasa utama wajib diisi untuk nama destinasi.</p>

        <div class="mt-6 space-y-6">
            @foreach ($languages as $language)
                @php
                    $code = $language->code;
                    $translation = $translations[$code] ?? [];
                    $isRequired = $code === $fallbackLocale;
                    $nameValue = old("translations.{$code}.name", $translation['name'] ?? ($isRequired ? ($destination->name ?? '') : ''));
                    $descriptionValue = old("translations.{$code}.description", $translation['description'] ?? ($isRequired ? ($destination->description ?? '') : ''));
                    $categoryValue = old("translations.{$code}.category", $translation['category'] ?? ($isRequired ? ($destination->category ?? '') : ''));
                    $distanceValue = old("translations.{$code}.distance", $translation['distance'] ?? ($isRequired ? ($destination->distance ?? '') : ''));
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="text-sm font-semibold text-slate-900">{{ $language->name ?? strtoupper($code) }}</p>
                        <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700">{{ strtoupper($code) }}</span>
                        @if (!empty($language->native_name))
                            <span class="text-xs text-slate-500">{{ $language->native_name }}</span>
                        @endif
                        @if ($isRequired)
                            <span class="ml-auto text-xs font-semibold text-emerald-700">Wajib</span>
                        @endif
                    </div>

                    <div class="mt-4 grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Nama Destinasi</label>
                            <input name="translations[{{ $code }}][name]" value="{{ $nameValue }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" @if($isRequired) required @endif />
                            @error("translations.{$code}.name")
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="text-sm font-semibold text-slate-900">Deskripsi</label>
                            <textarea name="translations[{{ $code }}][description]" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="Deskripsi destinasi wisata...">{{ $descriptionValue }}</textarea>
                            @error("translations.{$code}.description")
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Kategori</label>
                            <input name="translations[{{ $code }}][category]" value="{{ $categoryValue }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                            @error("translations.{$code}.category")
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm font-semibold text-slate-900">Jarak / Keterangan</label>
                            <input name="translations[{{ $code }}][distance]" value="{{ $distanceValue }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                            @error("translations.{$code}.distance")
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Gambar Destinasi</label>
        <div class="mt-2">
            <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100" />
            <p class="mt-1 text-xs text-slate-500">Format: JPEG, PNG, JPG, WebP. Maksimal 2MB.</p>
        </div>
        @error('image')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror

        @if ($isEdit && !empty($destination->image))
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-medium text-slate-600 mb-2">Gambar saat ini:</p>
                <div class="flex items-start gap-4">
                    <img src="{{ asset('storage/' . $destination->image) }}" alt="{{ $destination->name }}" class="h-32 w-32 rounded-xl object-cover">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="remove_image" name="remove_image" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                        <label for="remove_image" class="text-sm text-slate-700">Hapus gambar</label>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Latitude</label>
        <input name="lat" value="{{ old('lat', $destination->lat ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
        @error('lat')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Longitude</label>
        <input name="lng" value="{{ old('lng', $destination->lng ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
        @error('lng')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2 flex items-center gap-3">
        <input id="is_active" name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" @checked(old('is_active', $destination->is_active ?? true)) />
        <label for="is_active" class="text-sm text-slate-700">Aktifkan destinasi</label>
    </div>
</div>
