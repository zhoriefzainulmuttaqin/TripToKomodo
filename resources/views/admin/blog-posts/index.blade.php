@extends('layouts.admin')

@section('title', 'Komodo Insider | Admin')

@section('content')
    @php
        $totalData = $posts->total();
        $shownData = $posts->count();
        $publishedData = $posts->where('is_published', true)->count();
        $draftData = $shownData - $publishedData;
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Komodo Insider</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Articles</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }}</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }}</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Published</p><p class="mt-1 text-2xl font-bold">{{ $publishedData }}</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Draft</p><p class="mt-1 text-2xl font-bold">{{ $draftData }}</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between" method="GET" action="{{ route('admin.blog-posts.index') }}">
            <div class="flex w-full flex-col gap-3 lg:flex-row">
                <div class="relative w-full lg:max-w-sm">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="judul / slug / meta title" class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-4 text-sm" />
                </div>
                <select name="lang" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    @foreach ($supportedLocales as $code)
                        <option value="{{ $code }}" {{ $lang === $code ? 'selected' : '' }}>{{ strtoupper($code) }}</option>
                    @endforeach
                </select>
                <select name="status" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="published" {{ $status !== 'draft' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                <button type="submit" class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Terapkan</button>
                <a href="{{ route('admin.blog-posts.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Reset</a>
            </div>
            <a href="{{ route('admin.blog-posts.create', ['lang' => $lang]) }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tulis Artikel</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-6 py-4 text-left">Judul</th>
                    <th class="px-6 py-4 text-left">Slug</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Views</th>
                    <th class="px-6 py-4 text-left">Waktu Baca</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($posts as $post)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-slate-900">{{ $post->title }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ strtoupper($post->language_code) }} • {{ $post->group_key }}</p>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-slate-700">{{ $post->slug }}</td>
                        <td class="px-6 py-4">
                            @if ($post->is_published)
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Published</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-700">{{ number_format((int) ($post->view_count ?? 0)) }}</td>
                        <td class="px-6 py-4 text-slate-700">
                            @php($reading = $post->readingTimeMinutesComputed())
                            {{ !empty($reading) ? ($reading . ' min') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-600">Belum ada artikel.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $posts->links() }}</div>
@endsection
