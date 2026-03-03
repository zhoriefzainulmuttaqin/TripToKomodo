@extends('layouts.app')

@php
    $locale = app()->getLocale();
    $supported = ['id', 'en', 'zh', 'es', 'de', 'ru'];
    $lang = in_array($locale, $supported, true) ? $locale : 'en';
@endphp

@section('title', '404 | Halaman tidak ditemukan')
@section('meta_description', 'Halaman yang Anda cari tidak ditemukan. Silakan kembali ke beranda atau jelajahi paket trip kami.')
@section('og_type', 'website')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-20">
        <div class="rounded-3xl border border-slate-200 bg-white p-10 text-center shadow-sm">
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">404</p>
            <h1 class="mt-4 text-3xl font-semibold text-slate-900 md:text-4xl">Halaman tidak ditemukan</h1>
            <p class="mx-auto mt-4 max-w-2xl text-sm text-slate-600">
                Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.
            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('home', ['lang' => $lang]) }}" class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white hover:bg-emerald-700">Kembali ke Beranda</a>
                <a href="{{ route('tours.index', ['lang' => $lang]) }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 hover:text-emerald-700">Lihat Paket Trip</a>
            </div>
        </div>
    </section>
@endsection
