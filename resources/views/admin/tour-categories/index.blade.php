@extends('layouts.admin')

@section('title', 'Kategori Trip')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Data</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Kategori Trip</h1>
            <p class="mt-2 text-sm text-slate-600">Dipakai untuk dropdown menu Paket Trip.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" action="{{ route('admin.tour-categories.index') }}" class="flex flex-wrap gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kategori..." class="w-64 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm" />
                <select name="status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Filter</button>
            </form>

            <a href="{{ route('admin.tour-categories.create') }}" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Tambah</a>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-emerald-50 text-xs uppercase tracking-[0.2em] text-emerald-700">
                <tr>
                    <th class="px-5 py-4">Nama</th>
                    <th class="px-5 py-4">Slug</th>
                    <th class="px-5 py-4">Urutan</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr class="border-t border-slate-100">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $category->name }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-700">{{ $category->slug }}</td>
                        <td class="px-5 py-4 text-slate-700">{{ $category->sort_order }}</td>
                        <td class="px-5 py-4">
                            @if ($category->is_active)
                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.tour-categories.edit', $category) }}" class="text-emerald-700 hover:text-emerald-800">Edit</a>
                                <form method="POST" action="{{ route('admin.tour-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700">Hapus</button>
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
