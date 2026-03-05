@extends('layouts.admin')

@section('title', 'FAQ')

@section('content')
    @php
        $totalData = $faqs->total();
        $shownData = $faqs->count();
        $activeData = $faqs->where('is_active', true)->count();
        $inactiveData = $shownData - $activeData;
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">FAQ</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">FAQ</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }}</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }}</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Aktif (halaman ini)</p><p class="mt-1 text-2xl font-bold">{{ $activeData }}</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Nonaktif (halaman ini)</p><p class="mt-1 text-2xl font-bold">{{ $inactiveData }}</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full flex-col gap-3 lg:flex-row">
                <div class="relative w-full lg:max-w-sm">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari pertanyaan..." class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-4 text-sm" />
                </div>
                <select name="language" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Semua bahasa</option>
                    @foreach ($activeLanguages as $language)
                        <option value="{{ $language->code }}" @selected(request('language') === $language->code)>
                            {{ strtoupper($language->code) }} - {{ $language->name ?? $language->native_name ?? '' }}
                        </option>
                    @endforeach
                </select>
                <select name="status" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Semua</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Nonaktif</option>
                </select>
                <button type="submit" class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
            </div>
            <a href="{{ route('admin.faqs.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tambah Data</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-5 py-4">Pertanyaan</th>
                    <th class="px-5 py-4">Bahasa</th>
                    <th class="px-5 py-4">Urutan</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($faqs as $faq)
                    <tr>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">{{ $faq->question }}</p>
                            <p class="mt-1 text-xs text-slate-500 line-clamp-2">{{ $faq->answer }}</p>
                        </td>
                        <td class="px-5 py-4">{{ strtoupper($faq->language_code) }}</td>
                        <td class="px-5 py-4">{{ $faq->sort_order }}</td>
                        <td class="px-5 py-4">
                            @if ($faq->is_active)
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Aktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.faqs.edit', $faq) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" onsubmit="return confirm('Hapus FAQ ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-600">Belum ada FAQ.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $faqs->links() }}</div>
@endsection
