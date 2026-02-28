@extends('layouts.app')

@section('title', 'Rental Mobil Labuan Bajo')
@section('meta_description', 'Rental mobil Labuan Bajo: driver profesional, unit nyaman, dan itinerary fleksibel untuk trip Flores & sekitarnya.')

@section('content')
    <section class="mx-auto max-w-6xl px-6 py-16">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Rental Mobil</p>
        <h1 class="mt-3 text-4xl font-semibold text-slate-900">Rental Mobil Labuan Bajo</h1>
        <p class="mt-4 text-sm text-slate-600">Halaman ini siap diisi paket rental (unit, harga, durasi, dan layanan driver). Untuk sementara, silakan hubungi tim kami untuk rekomendasi tercepat.</p>

        <div class="mt-10 rounded-3xl border border-emerald-100 bg-emerald-50 p-8">
            <h2 class="text-xl font-semibold text-slate-900">Butuh rekomendasi cepat?</h2>
            <p class="mt-2 text-sm text-emerald-800">Klik tombol konsultasi untuk dapat pilihan unit sesuai rute Flores yang kamu inginkan.</p>
            <a href="{{ route('home', ['lang' => app()->getLocale()]) }}#contact" class="mt-6 inline-flex rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Konsultasi</a>
        </div>
    </section>
@endsection
