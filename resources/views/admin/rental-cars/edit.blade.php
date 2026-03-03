@extends('layouts.admin')

@section('title', 'Admin | Edit Mobil Rental')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Edit Mobil Rental</h1>
            <p class="mt-1 text-sm text-slate-600">Perbarui data, konten multi bahasa, dan harga.</p>
        </div>
        <a href="{{ route('admin.rental-cars.index') }}" class="inline-flex rounded-full border border-slate-200 bg-white px-5 py-2 text-sm font-semibold text-slate-800">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.rental-cars.update', $car) }}" enctype="multipart/form-data" class="mt-6">
        @csrf
        @method('PUT')

        @include('admin.rental-cars._form', ['car' => $car])

        <div class="mt-6 flex items-center gap-3">
            <button type="submit" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Simpan</button>
            <a href="{{ route('admin.rental-cars.index') }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-800">Batal</a>
        </div>
    </form>
@endsection
