@extends('layouts.admin')

@section('title', 'Paket Trip')

@section('content')
    @php
        $totalData = $packages->total();
        $shownData = $packages->count();
        $publishedData = $packages->where('status', 'published')->count();
        $draftData = $packages->where('status', 'draft')->count();
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Paket Trip</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Paket Trip</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }}</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }}</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Published</p><p class="mt-1 text-2xl font-bold">{{ $publishedData }}</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Draft</p><p class="mt-1 text-2xl font-bold">{{ $draftData }}</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" action="{{ route('admin.tour-packages.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full flex-col gap-3 lg:flex-row">
                <div class="relative w-full lg:max-w-sm">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode/judul..." class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-4 text-sm" />
                </div>
                <select name="category" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Semua status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                </select>
                <select name="trashed" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="without" @selected(request('trashed', 'without') === 'without')>Tanpa trash</option>
                    <option value="only" @selected(request('trashed') === 'only')>Hanya trash</option>
                    <option value="with" @selected(request('trashed') === 'with')>Termasuk trash</option>
                </select>
                <button type="submit" class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
            </div>
            <a href="{{ route('admin.tour-packages.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tambah Data</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-5 py-4">Paket</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Update</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($packages as $package)
                    @php
                        $t = $package->translations->first();
                        $thumb = $package->primaryImage?->url;
                    @endphp
                    <tr class="@if($package->trashed()) bg-rose-50/30 @endif">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-slate-100" style="background-image:url('{{ $thumb }}'); background-size:cover; background-position:center;"></div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $t?->title ?? $package->code }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Code: {{ $package->code }} • {{ $package->duration_days }}D/{{ $package->duration_nights }}N • Max: {{ $package->max_people ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">{{ $package->category?->name ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold @if($package->status === 'published') bg-emerald-100 text-emerald-700 @elseif($package->status === 'draft') bg-amber-100 text-amber-700 @else bg-slate-100 text-slate-700 @endif">{{ ucfirst($package->status) }}</span>
                            @if($package->is_featured)
                                <span class="ml-2 inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">Featured</span>
                            @endif
                            @if($package->trashed())
                                <span class="ml-2 inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Trashed</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $package->updated_at?->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if($package->trashed())
                                    <form method="POST" action="{{ route('admin.tour-packages.restore', $package->id) }}" onsubmit="return confirm('Restore paket ini?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.tour-packages.edit', $package->id) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                    <form method="POST" action="{{ route('admin.tour-packages.destroy', $package->id) }}" onsubmit="return confirm('Pindahkan paket ini ke trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Trash</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-600">Belum ada paket trip.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $packages->links() }}</div>
@endsection
