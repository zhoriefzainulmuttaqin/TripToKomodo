@extends('layouts.admin')

@section('title', 'Destinasi')

@section('content')
    @php
        $totalData = $destinations->total();
        $shownData = $destinations->count();
        $activeData = $destinations->where('is_active', true)->count();
        $inactiveData = $shownData - $activeData;
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Destinasi</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Destinasi</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4">
            <p class="text-sm text-slate-600">Total</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $totalData }}</p>
        </div>
        <div class="rounded-2xl bg-lime-100/50 p-4">
            <p class="text-sm text-slate-600">Ditampilkan</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $shownData }}</p>
        </div>
        <div class="rounded-2xl bg-emerald-100/50 p-4">
            <p class="text-sm text-slate-600">Aktif (halaman ini)</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $activeData }}</p>
        </div>
        <div class="rounded-2xl bg-amber-100/50 p-4">
            <p class="text-sm text-slate-600">Nonaktif (halaman ini)</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $inactiveData }}</p>
        </div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" action="{{ route('admin.destinations.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full flex-col gap-3 md:flex-row">
                <div class="relative w-full md:max-w-md">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama destinasi..." class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-sm" />
                </div>
                <select name="status" class="h-11 rounded-xl border border-slate-200 bg-white px-4 text-sm">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <button type="submit" class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
            </div>
            <a href="{{ route('admin.destinations.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tambah Data</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-5 py-4">Gambar</th>
                    <th class="px-5 py-4">Nama</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4">Koordinat</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($destinations as $destination)
                    <tr>
                        <td class="px-5 py-4">
                            @if (!empty($destination->image))
                                <img src="{{ asset('storage/' . $destination->image) }}" alt="{{ $destination->display_name ?? $destination->name }}" class="h-14 w-14 rounded-xl object-cover">
                            @else
                                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-slate-100">
                                    <span class="material-symbols-outlined text-[22px] text-slate-400">image</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $destination->display_name ?? $destination->name }}</p>
                            @if (!empty($destination->display_description ?? $destination->description))
                                <p class="mt-1 max-w-xs truncate text-xs text-slate-500">{{ $destination->display_description ?? $destination->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">{{ $destination->display_category ?? $destination->category ?? '-' }}</td>
                        <td class="px-5 py-4">{{ $destination->lat ?? '-' }}, {{ $destination->lng ?? '-' }}</td>
                        <td class="px-5 py-4">
                            @if (property_exists($destination, 'is_active') && $destination->is_active)
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                            @elseif (property_exists($destination, 'is_active'))
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Nonaktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.destinations.edit', $destination->id) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.destinations.destroy', $destination->id) }}" onsubmit="return confirm('Hapus destinasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-600">Belum ada data destinasi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $destinations->links() }}</div>
@endsection
