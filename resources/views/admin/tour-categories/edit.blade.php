@extends('layouts.admin')

@section('title', 'Edit Kategori Trip')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Kategori</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Edit Kategori Trip</h1>
            <p class="mt-2 text-sm text-slate-600">Perbarui nama/slug kategori.</p>
        </div>
        <a href="{{ route('admin.tour-categories.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.tour-categories.update', $category) }}" class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.tour-categories._form', ['category' => $category])

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.tour-categories.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm">Batal</a>
            <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>
@endsection
