@extends('layouts.admin')

@section('title', 'Tulis Artikel | Admin')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900">Tulis Artikel</h1>
            <p class="mt-1 text-sm text-slate-600">Buat artikel Komodo Insider dengan pengaturan SEO lengkap.</p>
        </div>
        <a href="{{ route('admin.blog-posts.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.blog-posts.store') }}" enctype="multipart/form-data" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        @include('admin.blog-posts.partials.form', ['post' => $post, 'supportedLocales' => $supportedLocales, 'translateFrom' => $translateFrom])

        <div class="mt-6 flex justify-end gap-3">
            <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
@endsection
