@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="flex items-start justify-between gap-6">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Ringkasan</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Dashboard</h1>
            <p class="mt-2 text-sm text-slate-600">Kelola konten utama website dari sini.</p>
        </div>
        <a href="{{ route('admin.destinations.create') }}" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">Tambah Destinasi</a>
    </div>

    <div class="mt-8 grid gap-6 md:grid-cols-3">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Destinasi</p>
            <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $destinationCount }}</p>
            <p class="mt-1 text-sm text-slate-600">Jumlah data destinasi terdaftar.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Peta</p>
            <p class="mt-2 text-sm text-slate-600">Titik di peta home otomatis mengambil dari tabel destinasi.</p>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Tips</p>
            <p class="mt-2 text-sm text-slate-600">Isi koordinat (lat/lng) agar marker muncul di Leaflet.</p>
        </div>
    </div>
@endsection
