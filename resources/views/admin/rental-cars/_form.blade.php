@php
    $car = $car ?? null;
    $translations = $car?->translations?->keyBy('language_code') ?? collect();
    $fallbackLocale = (string) config('app.fallback_locale', 'en');
@endphp

<div class="grid gap-6">
    <div class="rounded-3xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-semibold">Data Mobil</h2>

        <div class="mt-4 grid gap-4 md:grid-cols-2">
            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Aktif</span>
                <select name="is_active" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    @php $activeVal = old('is_active', $car?->is_active ?? true); @endphp
                    <option value="1" {{ (string) $activeVal === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ (string) $activeVal === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Harga / Hari (IDR)</span>
                <input type="number" min="0" name="price_per_day_idr" value="{{ old('price_per_day_idr', $car?->price_per_day_idr ?? 0) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required />
                @error('price_per_day_idr')
                    <div class="mt-2 text-xs text-rose-600">{{ $message }}</div>
                @enderror
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Seats</span>
                <input type="number" min="1" name="seats" value="{{ old('seats', $car?->seats) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Luggage</span>
                <input type="number" min="0" name="luggage" value="{{ old('luggage', $car?->luggage) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Transmission</span>
                <input type="text" name="transmission" value="{{ old('transmission', $car?->transmission) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Automatic / Manual" />
            </label>

            <label class="block">
                <span class="text-sm font-semibold text-slate-700">Fuel</span>
                <input type="text" name="fuel" value="{{ old('fuel', $car?->fuel) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Petrol / Diesel" />
            </label>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div>
                <div class="text-sm font-semibold text-slate-700">Gambar Utama</div>

                @if (!empty($car?->image))
                    <div class="mt-3 flex items-center gap-4">
                        <img src="{{ $car->image }}" alt="" class="h-20 w-28 rounded-2xl border border-slate-200 object-cover" />
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="checkbox" name="remove_image" value="1" class="rounded border-slate-300" />
                            <span>Hapus gambar</span>
                        </label>
                    </div>
                @endif

                <input type="file" name="image" accept="image/*" class="mt-3 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                @error('image')
                    <div class="mt-2 text-xs text-rose-600">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-semibold">Konten Multi Bahasa</h2>
        <p class="mt-1 text-sm text-slate-600">Minimal isi <span class="font-semibold">Nama</span> untuk bahasa utama: <span class="font-semibold">{{ strtoupper($fallbackLocale) }}</span>.</p>

        <div class="mt-4 space-y-4">
            @foreach ($languages as $language)
                @php
                    $code = (string) ($language->code ?? 'en');
                    $t = $translations->get($code);
                @endphp

                <details class="rounded-2xl border border-slate-200 bg-white" {{ $code === $fallbackLocale ? 'open' : '' }}>
                    <summary class="flex cursor-pointer list-none items-center justify-between px-4 py-3">
                        <div class="font-semibold text-slate-900">{{ strtoupper($code) }} <span class="ml-2 text-xs font-normal text-slate-500">{{ $language->name ?? '' }}</span></div>
                        <span class="material-symbols-outlined text-[18px] leading-none text-slate-500" aria-hidden="true">expand_more</span>
                    </summary>

                    <div class="border-t border-slate-100 p-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Nama</span>
                                <input type="text" name="translations[{{ $code }}][name]" value="{{ old('translations.' . $code . '.name', $t?->name) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
                                @error('translations.' . $code . '.name')
                                    <div class="mt-2 text-xs text-rose-600">{{ $message }}</div>
                                @enderror
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Slug (opsional)</span>
                                <input type="text" name="translations[{{ $code }}][slug]" value="{{ old('translations.' . $code . '.slug', $t?->slug) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="auto dari nama jika kosong" />
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Excerpt</span>
                                <textarea name="translations[{{ $code }}][excerpt]" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('translations.' . $code . '.excerpt', $t?->excerpt) }}</textarea>
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Description</span>
                                <textarea name="translations[{{ $code }}][description]" rows="6" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('translations.' . $code . '.description', $t?->description) }}</textarea>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Meta Title</span>
                                <input type="text" name="translations[{{ $code }}][meta_title]" value="{{ old('translations.' . $code . '.meta_title', $t?->meta_title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Meta Keywords</span>
                                <input type="text" name="translations[{{ $code }}][meta_keywords]" value="{{ old('translations.' . $code . '.meta_keywords', $t?->meta_keywords) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />
                            </label>

                            <label class="block md:col-span-2">
                                <span class="text-sm font-semibold text-slate-700">Meta Description</span>
                                <textarea name="translations[{{ $code }}][meta_description]" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('translations.' . $code . '.meta_description', $t?->meta_description) }}</textarea>
                            </label>

                            <label class="block">
                                <span class="text-sm font-semibold text-slate-700">Aktif (bahasa ini)</span>
                                @php $tActive = old('translations.' . $code . '.is_active', $t?->is_active ?? true); @endphp
                                <select name="translations[{{ $code }}][is_active]" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                                    <option value="1" {{ (string) $tActive === '1' ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ (string) $tActive === '0' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </label>
                        </div>
                    </div>
                </details>
            @endforeach
        </div>
    </div>
</div>
