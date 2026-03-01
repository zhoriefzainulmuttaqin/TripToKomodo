@extends('layouts.admin')

@section('title', 'Edit FAQ')

@section('content')
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">FAQ</p>
            <h1 class="mt-2 text-3xl font-semibold text-slate-900">Edit FAQ</h1>
            <p class="mt-2 text-sm text-slate-600">Perbarui pertanyaan dan jawaban sesuai kebutuhan.</p>
        </div>
        <a href="{{ route('admin.faqs.index') }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-sm">Kembali</a>
    </div>

    <form method="POST" action="{{ route('admin.faqs.update', $faq) }}" class="mt-8 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        @csrf
        @method('PUT')
        @include('admin.faqs._form', ['faq' => $faq])

        <div class="mt-8 flex justify-end">
            <button type="submit" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Simpan Perubahan</button>
        </div>
    </form>
@endsection
