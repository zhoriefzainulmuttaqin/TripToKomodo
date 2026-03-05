@extends('layouts.admin')

@section('title', 'Edit Customer | CRM')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">CRM / Customers</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Edit Customer</h1>
            <p class="mt-2 text-sm text-slate-600">Perbarui data customer.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.show', $customer) }}" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700">Detail</a>
            <a href="{{ route('admin.customers.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700">Kembali</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" enctype="multipart/form-data" class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')

        @include('admin.customers._form', ['customer' => $customer])

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <button class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Simpan Perubahan</button>
            <a href="{{ route('admin.customers.show', $customer) }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700">Batal</a>
        </div>
    </form>

    <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="mt-4" onsubmit="return confirm('Hapus customer ini?');">
        @csrf
        @method('DELETE')
        <button class="rounded-full border border-rose-200 bg-rose-50 px-6 py-3 text-sm font-semibold text-rose-700">Hapus</button>
    </form>
@endsection
