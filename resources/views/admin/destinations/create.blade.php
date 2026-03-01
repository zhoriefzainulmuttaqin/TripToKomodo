@extends('layouts.admin')

@section('title', 'Tambah Destinasi')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Destinasi</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Tambah Destinasi</h1>
            <p class="mt-2 text-sm text-slate-600">Data akan langsung dipakai untuk marker di peta.</p>
        </div>
        <a href="{{ route('admin.destinations.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm hover:text-emerald-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.destinations.store') }}" enctype="multipart/form-data" class="mt-8 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf

        @include('admin.destinations._form')

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('admin.destinations.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-3 text-sm">Batal</a>
            <button type="submit" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
@endsection
