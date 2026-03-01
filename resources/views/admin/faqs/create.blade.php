@extends('layouts.admin')

@section('title', 'Tambah FAQ')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">FAQ</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Tambah FAQ</h1>
            <p class="mt-2 text-sm text-slate-600">FAQ akan tampil di halaman home sesuai bahasa.</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.faqs.store') }}" class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        @csrf
        @include('admin.faqs._form')

        <div class="mt-8 flex justify-end">
            <button type="submit" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Simpan</button>
        </div>
    </form>
@endsection
