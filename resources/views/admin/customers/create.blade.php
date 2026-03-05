@extends('layouts.admin')

@section('title', 'Tambah Customer | CRM')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">CRM / Customers</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Tambah Customer</h1>
            <p class="mt-2 text-sm text-slate-600">Input manual data customer dari WhatsApp/chat.</p>
        </div>

        <a href="{{ route('admin.customers.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.customers.store') }}" enctype="multipart/form-data" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
        @csrf

        @include('admin.customers._form')

        <div class="mt-8 flex items-center gap-3">
            <button class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Simpan</button>
            <a href="{{ route('admin.customers.index') }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>
@endsection
