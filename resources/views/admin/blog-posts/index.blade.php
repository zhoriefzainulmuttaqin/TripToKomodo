@extends('layouts.admin')

@section('title', 'Komodo Insider | Admin')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Komodo Insider</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola artikel/blog untuk SEO organik: konten, meta, OG, canonical, robots, dan schema.</p>
        </div>
        <a href="{{ route('admin.blog-posts.create', ['lang' => $lang]) }}" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Tulis Artikel</a>
    </div>

    <form class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Bahasa</label>
                <select name="lang" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    @foreach ($supportedLocales as $code)
                        <option value="{{ $code }}" {{ $lang === $code ? 'selected' : '' }}>{{ strtoupper($code) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Status</label>
                <select name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                    <option value="published" {{ $status !== 'draft' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Cari</label>
                <input type="text" name="q" value="{{ $q }}" placeholder="judul / slug / meta title" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('admin.blog-posts.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Reset</a>
            <button class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Terapkan</button>
        </div>
    </form>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-600">
                <tr>
                    <th class="px-6 py-4 text-left">Judul</th>
                    <th class="px-6 py-4 text-left">Slug</th>
                    <th class="px-6 py-4 text-left">Status</th>
                    <th class="px-6 py-4 text-left">Views</th>
                    <th class="px-6 py-4 text-left">Waktu Baca</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
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
                                <span class="inline-flex rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Draft</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            {{ number_format((int) ($post->view_count ?? 0)) }}
                        </td>
                        <td class="px-6 py-4 text-slate-700">
                            @php($reading = $post->readingTimeMinutesComputed())
                            {{ !empty($reading) ? ($reading . ' min') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-700">Edit</a>
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
