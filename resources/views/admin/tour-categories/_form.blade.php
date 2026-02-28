<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Nama</label>
        <input name="name" value="{{ old('name', $category->name ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
        @error('name')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Slug (opsional)</label>
        <input name="slug" value="{{ old('slug', $category->slug ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="contoh: luxury-phinisi" />
        <p class="mt-2 text-xs text-slate-500">Jika kosong, slug dibuat otomatis dari nama.</p>
        @error('slug')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Urutan</label>
        <input name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
        @error('sort_order')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2 flex items-center gap-3">
        <input id="is_active" name="is_active" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" @checked(old('is_active', $category->is_active ?? true)) />
        <label for="is_active" class="text-sm text-slate-700">Aktifkan kategori</label>
    </div>
</div>
