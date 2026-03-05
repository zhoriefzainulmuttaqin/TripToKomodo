@extends('layouts.admin')

@section('title', 'Customers | CRM')

@section('content')
    @php
        $totalData = $customers->total();
        $shownData = $customers->count();
        $withDoc = $customers->filter(fn ($c) => !empty($c->document_path))->count();
        $withoutDoc = $shownData - $withDoc;
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Customers</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Customers</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }}</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }}</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Ada Dokumen</p><p class="mt-1 text-2xl font-bold">{{ $withDoc }}</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Tanpa Dokumen</p><p class="mt-1 text-2xl font-bold">{{ $withoutDoc }}</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="relative w-full lg:max-w-md">
                <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                <input name="q" value="{{ request('q') }}" class="h-11 w-full rounded-xl border border-slate-200 bg-white pl-10 pr-4 text-sm" placeholder="Nama / WhatsApp / email / negara" />
            </div>
            <div class="flex items-center gap-2">
                <button class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
                <a href="{{ route('admin.customers.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tambah Customer</a>
            </div>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-5 py-4">Customer</th>
                    <th class="px-5 py-4">Kontak</th>
                    <th class="px-5 py-4">Negara</th>
                    <th class="px-5 py-4">Dokumen</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($customers as $c)
                    <tr>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-900">{{ $c->full_name }}</div>
                            <div class="text-xs text-slate-500">ID #{{ $c->id }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-slate-900">{{ $c->phone ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $c->email ?? '' }}</div>
                        </td>
                        <td class="px-5 py-4">{{ $c->country ?? '-' }}</td>
                        <td class="px-5 py-4">
                            @if (!empty($c->document_path))
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Ada</span>
                            @else
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">-</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700" href="{{ route('admin.customers.show', $c) }}">Detail</a>
                                <a class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700" href="{{ route('admin.customers.edit', $c) }}">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-sm text-slate-600">Belum ada customer.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $customers->links() }}</div>
@endsection
