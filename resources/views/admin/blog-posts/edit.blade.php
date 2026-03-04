@extends('layouts.admin')

@section('title', 'Edit Artikel | Admin')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Edit Artikel</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola konten & SEO. Gunakan Group Key untuk menyambungkan terjemahan antar bahasa.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.blog-posts.index', ['lang' => $post->language_code]) }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Kembali</a>
            <a href="{{ route('blog.show', ['lang' => $post->language_code, 'slug' => $post->slug]) }}" target="_blank" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Lihat</a>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_320px]">
        <form method="POST" action="{{ route('admin.blog-posts.update', $post) }}" enctype="multipart/form-data" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @method('PUT')

            @include('admin.blog-posts.partials.form', ['post' => $post, 'supportedLocales' => $supportedLocales, 'translateFrom' => null])

            <div class="mt-6 flex justify-end gap-3">
                <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
            </div>
        </form>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-sm font-semibold text-slate-900">Terjemahan</p>
            <p class="mt-1 text-xs text-slate-500">Artikel dengan Group Key yang sama akan otomatis dihubungkan (hreflang).</p>

            <div class="mt-4 space-y-2">
                @foreach ($translations as $t)
                    <a href="{{ route('admin.blog-posts.edit', $t->id) }}" class="block rounded-2xl border border-slate-200 px-4 py-3 text-sm hover:border-emerald-200">
                        <p class="font-semibold text-slate-900">{{ strtoupper($t->language_code) }} — {{ $t->title }}</p>
                        <p class="mt-1 font-mono text-xs text-slate-600">{{ $t->slug }}</p>
                        <p class="mt-2 text-xs text-slate-500">{{ $t->is_published ? 'Published' : 'Draft' }}</p>
                    </a>
                @endforeach
            </div>

            <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">Buat Terjemahan Baru</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach ($supportedLocales as $code)
                        <a href="{{ route('admin.blog-posts.create', ['lang' => $code, 'translate_from' => $post->id]) }}" class="rounded-full border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-emerald-200">{{ strtoupper($code) }}</a>
                    @endforeach
                </div>
            </div>

            <form method="POST" action="{{ route('admin.blog-posts.destroy', $post) }}" class="mt-8" onsubmit="return confirm('Hapus artikel ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full rounded-full bg-rose-600 px-5 py-3 text-sm font-semibold text-white">Hapus</button>
            </form>
        </aside>
    </div>
@endsection
