@php
    $isEdit = isset($customer);
    $otherContactsValue = old('other_contacts', $isEdit ? implode("\n", (array) ($customer->other_contacts ?? [])) : '');
@endphp

<div class="grid gap-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Nama Lengkap</label>
        <input name="full_name" value="{{ old('full_name', $customer->full_name ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
        @error('full_name')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">No HP / WhatsApp</label>
        <input name="phone" value="{{ old('phone', $customer->phone ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="08xxxxxxxxxx" />
        @error('phone')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Email</label>
        <input name="email" value="{{ old('email', $customer->email ?? '') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="email@contoh.com" />
        @error('email')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="text-sm font-semibold text-slate-900">Asal Negara</label>
        @php
            $countryValue = (string) old('country', $customer->country ?? '');
        @endphp
        <select
            name="country"
            id="countrySelect"
            data-current="{{ $countryValue }}"
            data-source="{{ route('admin.customers.countries') }}"
            class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3"
        >
            <option value="">Pilih negara</option>
            @if ($countryValue !== '')
                <option value="{{ $countryValue }}" selected>{{ $countryValue }}</option>
            @endif
        </select>
        <p class="mt-2 text-xs text-slate-500" id="countryHelp">Memuat daftar negara...</p>
        @error('country')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror

        <script>
            (function () {
                var select = document.getElementById('countrySelect');
                if (!select) return;

                var help = document.getElementById('countryHelp');
                var current = (select.getAttribute('data-current') || '').trim();
                var source = select.getAttribute('data-source');

                function setHelp(text) {
                    if (help) help.textContent = text;
                }

                function addOption(value, selected) {
                    var opt = document.createElement('option');
                    opt.value = value;
                    opt.textContent = value;
                    if (selected) opt.selected = true;
                    select.appendChild(opt);
                }

                function hasOption(value) {
                    var opts = select.options;
                    for (var i = 0; i < opts.length; i++) {
                        if ((opts[i].value || '').trim() === value) return true;
                    }
                    return false;
                }

                if (!source) {
                    setHelp('');
                    return;
                }

                fetch(source, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) {
                        if (!r.ok) throw new Error('HTTP ' + r.status);
                        return r.json();
                    })
                    .then(function (data) {
                        var countries = (data && data.countries) ? data.countries : [];
                        if (!Array.isArray(countries) || countries.length === 0) {
                            setHelp('Daftar negara tidak tersedia. Anda tetap bisa menyimpan tanpa memilih negara.');
                            return;
                        }

                        // Reset options (keep placeholder)
                        select.options.length = 1;

                        // If current value is not in list, keep it.
                        if (current && !countries.includes(current)) {
                            addOption(current, true);
                        }

                        countries.forEach(function (name) {
                            if (!name || typeof name !== 'string') return;
                            var v = name.trim();
                            if (!v) return;
                            if (hasOption(v)) return;
                            addOption(v, current === v);
                        });

                        setHelp('');
                    })
                    .catch(function () {
                        setHelp('Gagal memuat daftar negara. Anda tetap bisa menyimpan tanpa memilih negara.');
                    });
            })();
        </script>
    </div>


    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Kontak lain (opsional)</label>
        <textarea name="other_contacts" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="Satu per baris. Contoh:\nInstagram: @triptokomodo\nWeChat: abc123\nTelegram: @abc">{{ $otherContactsValue }}</textarea>
        @error('other_contacts')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Dokumen (opsional)</label>
        <div class="mt-2">
            <input type="file" name="document" accept="application/pdf,image/jpeg,image/png,image/jpg,image/webp" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-full file:border-0 file:bg-emerald-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-emerald-700 hover:file:bg-emerald-100" />
            <p class="mt-1 text-xs text-slate-500">Format: PDF/JPEG/PNG/JPG/WebP. Maksimal 5MB.</p>
        </div>
        @error('document')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror

        @if ($isEdit && !empty($customer->document_path))
            <div class="mt-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-medium text-slate-600 mb-2">Dokumen saat ini:</p>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ asset('storage/' . $customer->document_path) }}" target="_blank" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700 hover:text-emerald-700">Lihat/Download</a>
                    <span class="text-xs text-slate-500">{{ $customer->document_original_name ?? '' }}</span>
                    <span class="text-xs text-slate-500">{{ $customer->document_size ? number_format($customer->document_size / 1024, 0) . ' KB' : '' }}</span>
                    <label class="ml-auto inline-flex items-center gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="remove_document" value="1" class="h-4 w-4 rounded border-slate-300" />
                        Hapus dokumen
                    </label>
                </div>
            </div>
        @endif
    </div>

    <div class="md:col-span-2">
        <label class="text-sm font-semibold text-slate-900">Catatan / Data lainnya (opsional)</label>
        <textarea name="notes" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" placeholder="Catatan internal, preferensi, history chat, dll.">{{ old('notes', $customer->notes ?? '') }}</textarea>
        @error('notes')
            <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</div>
