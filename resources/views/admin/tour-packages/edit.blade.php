@extends('layouts.admin')

@section('title', 'Edit Paket Trip')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Paket Trip</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Edit Paket</h1>
            <p class="mt-2 text-sm text-slate-600">Perbarui detail paket, galeri, destinasi, dan ketersediaan.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.tour-packages.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Kembali</a>
            <form method="POST" action="{{ route('admin.tour-packages.destroy', $package->id) }}" onsubmit="return confirm('Pindahkan paket ini ke trash?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">Trash</button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tour-packages.update', $package->id) }}" enctype="multipart/form-data" class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')

        @include('admin.tour-packages._form', ['package' => $package])

        <div class="mt-8 flex justify-end gap-3">
            <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>

    <div class="mt-10 grid gap-6 lg:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Galeri</p>
                    <h2 class="mt-2 text-xl font-semibold text-slate-900">Gambar Paket</h2>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                @forelse($package->images as $image)
                    <div class="overflow-hidden rounded-2xl border border-slate-200">
                        <div class="h-36 bg-slate-100" style="background-image:url('{{ $image->url }}'); background-size:cover; background-position:center;"></div>
                        <div class="flex items-center justify-between gap-3 p-3 text-xs">
                            <div class="text-slate-600">
                                @if($image->is_primary)
                                    <span class="rounded-full bg-emerald-50 px-2 py-1 font-semibold text-emerald-700">Primary</span>
                                @else
                                    <span class="rounded-full bg-slate-100 px-2 py-1 font-semibold text-slate-700">Gallery</span>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('admin.tour-packages.images.destroy', [$package->id, $image->id]) }}" onsubmit="return confirm('Hapus gambar ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:text-rose-700">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-sm text-slate-600 sm:col-span-2">
                        Belum ada gambar.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Kalender</p>
            <h2 class="mt-2 text-xl font-semibold text-slate-900">Ketersediaan</h2>

            <form method="POST" action="{{ route('admin.tour-packages.availabilities.store', $package->id) }}" class="mt-6 grid gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <label class="text-sm font-semibold text-slate-900">Tanggal</label>
                    <input type="date" name="date" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" required />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-900">Available slots (opsional)</label>
                    <input type="number" name="available_slots" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-900">Price override IDR (opsional)</label>
                    <input type="number" step="0.01" name="price_idr_override" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                </div>
                <div>
                    <label class="text-sm font-semibold text-slate-900">Catatan (opsional)</label>
                    <input type="text" name="note" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3" />
                </div>
                <div class="md:col-span-2 flex items-center gap-3">
                    <input id="is_available" name="is_available" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300" checked />
                    <label for="is_available" class="text-sm text-slate-700">Tanggal tersedia</label>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Tambah/Update</button>
                </div>
            </form>

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-600">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Slots</th>
                            <th class="px-4 py-3">Override</th>
                            <th class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($package->availabilities as $a)
                            <tr class="border-t border-slate-100">
                                <td class="px-4 py-3">{{ $a->date?->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @if($a->is_available)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Available</span>
                                    @else
                                        <span class="rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">Closed</span>
                                    @endif
                                    @if(!empty($a->note))
                                        <p class="mt-1 text-xs text-slate-500">{{ $a->note }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-700">{{ $a->available_slots ?? '-' }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $a->price_idr_override ? number_format($a->price_idr_override, 0, ',', '.') : '-' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end">
                                        <form method="POST" action="{{ route('admin.tour-packages.availabilities.destroy', [$package->id, $a->id]) }}" onsubmit="return confirm('Hapus tanggal ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-700">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-slate-600">Belum ada data availability.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
