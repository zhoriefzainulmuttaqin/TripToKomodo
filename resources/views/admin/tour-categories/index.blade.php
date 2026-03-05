@extends('layouts.admin')

@section('title', 'Kategori Trip')

@section('content')
    @php
        $totalData = $categories->total();
        $shownData = $categories->count();
        $activeData = $categories->where('is_active', true)->count();
        $inactiveData = $shownData - $activeData;
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Kategori Trip</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Kategori Trip</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }}</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }}</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Aktif (halaman ini)</p><p class="mt-1 text-2xl font-bold">{{ $activeData }}</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Nonaktif (halaman ini)</p><p class="mt-1 text-2xl font-bold">{{ $inactiveData }}</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" action="{{ route('admin.tour-categories.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full flex-col gap-3 md:flex-row">
                <div class="relative w-full md:max-w-md">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kategori..." class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-4 text-sm" />
                </div>
                <select name="status" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <button type="submit" class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
            </div>
            <a href="{{ route('admin.tour-categories.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tambah Data</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-5 py-4">Nama</th>
                    <th class="px-5 py-4">Slug</th>
                    <th class="px-5 py-4">Urutan</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($categories as $category)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $category->name }}</td>
                        <td class="px-5 py-4">{{ $category->slug }}</td>
                        <td class="px-5 py-4">{{ $category->sort_order }}</td>
                        <td class="px-5 py-4">
                            @if ($category->is_active)
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.tour-categories.edit', $category) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.tour-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-600">Belum ada kategori.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $categories->links() }}</div>
@endsection
