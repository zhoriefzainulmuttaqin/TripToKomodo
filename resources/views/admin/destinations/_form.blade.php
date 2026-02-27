@php
    $isEdit = isset($destination);
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Nama</label>
        <input name="name" value="{{ old('name', $destination->name ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
        @error('name')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Kategori</label>
        <input name="category" value="{{ old('category', $destination->category ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
        @error('category')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Jarak / Keterangan</label>
        <input name="distance" value="{{ old('distance', $destination->distance ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
        @error('distance')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
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
