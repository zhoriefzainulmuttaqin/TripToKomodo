@extends('layouts.admin')

@section('title', 'Paket Trip')

@section('content')
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">CMS</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Paket Trip</h1>
            <p class="mt-2 text-sm text-slate-600">Kelola paket trip, multi-bahasa, SEO, destinasi, galeri, dan ketersediaan.</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <form method="GET" action="{{ route('admin.tour-packages.index') }}" class="flex flex-wrap gap-3">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari kode/judul..." class="w-64 rounded-full border border-slate-200 bg-white px-4 py-2 text-sm" />

                <select name="category" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                    <option value="">Semua status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="published" @selected(request('status') === 'published')>Published</option>
                    <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                </select>

                <select name="trashed" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">
                    <option value="without" @selected(request('trashed', 'without') === 'without')>Tanpa trash</option>
                    <option value="only" @selected(request('trashed') === 'only')>Hanya trash</option>
                    <option value="with" @selected(request('trashed') === 'with')>Termasuk trash</option>
                </select>

                <button type="submit" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Filter</button>
            </form>

            <a href="{{ route('admin.tour-packages.create') }}" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Tambah</a>
        </div>
    </div>

    <div class="mt-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-left text-sm">
            <thead class="bg-emerald-50 text-xs uppercase tracking-[0.2em] text-emerald-700">
                <tr>
                    <th class="px-5 py-4">Paket</th>
                    <th class="px-5 py-4">Kategori</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Update</th>
                    <th class="px-5 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($packages as $package)
                    @php
                        $t = $package->translations->first();
                        $thumb = $package->primaryImage?->url;
                    @endphp
                    <tr class="border-t border-slate-100 @if($package->trashed()) bg-rose-50/30 @endif">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-2xl bg-slate-100" style="background-image:url('{{ $thumb }}'); background-size:cover; background-position:center;"></div>
                                <div>
                                    <p class="font-semibold text-slate-900">{{ $t?->title ?? $package->code }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Code: {{ $package->code }} • {{ $package->duration_days }}D/{{ $package->duration_nights }}N • Max: {{ $package->max_people ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-slate-700">{{ $package->category?->name ?? '-' }}</td>
                        <td class="px-5 py-4">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold
                                @if($package->status === 'published') bg-emerald-50 text-emerald-700
                                @elseif($package->status === 'draft') bg-slate-100 text-slate-700
                                @else bg-amber-50 text-amber-800 @endif
                            ">
                                {{ ucfirst($package->status) }}
                            </span>
                            @if($package->is_featured)
                                <span class="ml-2 rounded-full bg-emerald-600/10 px-3 py-1 text-xs font-semibold text-emerald-800">Featured</span>
                            @endif
                            @if($package->trashed())
                                <span class="ml-2 rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Trashed</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $package->updated_at?->format('d M Y H:i') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-3">
                                @if($package->trashed())
                                    <form method="POST" action="{{ route('admin.tour-packages.restore', $package->id) }}" onsubmit="return confirm('Restore paket ini?')">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-slate-700 hover:text-emerald-800">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.tour-packages.edit', $package->id) }}" class="text-emerald-700 hover:text-emerald-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.tour-packages.destroy', $package->id) }}" onsubmit="return confirm('Pindahkan paket ini ke trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-700">Trash</button>
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
