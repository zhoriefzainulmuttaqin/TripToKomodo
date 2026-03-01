@extends('layouts.app')

@php
    $t = trans('home');
    $contactEmail = $contactSettings['email'] ?? 'hello@triptokomodo.com';
    $contactPhone = $contactSettings['phone'] ?? '+62 812 0000 0000';
    $contactWhatsapp = $contactSettings['whatsapp'] ?? $contactPhone;
    $contactWhatsappUrl = $contactSettings['whatsapp_url'] ?? 'https://wa.me/6281200000000';
@endphp

@section('title', $t['contact']['title'] ?? 'Kontak Kami')
@section('meta_description', $t['contact']['desc'] ?? 'Hubungi tim TriptoKomodo untuk konsultasi perjalanan Labuan Bajo terbaik.')

@section('content')


    <section class="mx-auto max-w-6xl px-6 py-16">
        @php
            $booking = $t['booking'] ?? [];
            $labels = $booking['labels'] ?? [];
            $placeholders = $booking['placeholders'] ?? [];
            $buttons = $booking['buttons'] ?? [];
        @endphp
        <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
            <div class="w-full max-w-md space-y-4">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">{{ $booking['title'] ?? 'Form Booking' }}</p>
                    <p class="mt-2 text-sm text-slate-600">{{ $booking['desc'] ?? 'Isi detail perjalanan. Pilih kirim ke email atau WhatsApp.' }}</p>

                    @if (session('status'))
                        <div class="mt-4 rounded-2xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->has('form'))
                        <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-700">
                            {{ $errors->first('form') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('contact.booking', ['lang' => app()->getLocale()]) }}" class="mt-4 space-y-4">
                        @csrf
                        <input type="text" name="website" value="" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true" />

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['name'] ?? 'Nama' }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $placeholders['name'] ?? 'Nama lengkap' }}" />
                            @error('name')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['email'] ?? 'Email' }}</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $placeholders['email'] ?? 'email@domain.com' }}" />
                                @error('email')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['phone'] ?? 'Telepon/WhatsApp' }}</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" required class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $placeholders['phone'] ?? '+62 812 0000 0000' }}" />
                                @error('phone')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid gap-3 md:grid-cols-2">
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['travel_date'] ?? 'Tanggal Trip' }}</label>
                                <input type="date" name="travel_date" value="{{ old('travel_date') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" />
                                @error('travel_date')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['traveler_count'] ?? 'Jumlah Orang' }}</label>
                                <input type="number" name="traveler_count" min="1" max="100" value="{{ old('traveler_count') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $placeholders['traveler_count'] ?? '2' }}" />
                                @error('traveler_count')
                                    <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['budget'] ?? 'Budget' }}</label>
                            <input type="text" name="budget" value="{{ old('budget') }}" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $placeholders['budget'] ?? 'Contoh: 6-10 juta/orang' }}" />
                            @error('budget')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $labels['message'] ?? 'Catatan Tambahan' }}</label>
                            <textarea name="message" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm" placeholder="{{ $placeholders['message'] ?? 'Tipe kapal, durasi, aktivitas favorit, dll.' }}">{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <button type="submit" name="channel" value="email" class="rounded-full bg-emerald-600 px-5 py-3 text-sm font-semibold text-white">{{ $buttons['send_email'] ?? 'Kirim ke Email' }}</button>
                            <button type="submit" name="channel" value="whatsapp" class="rounded-full border border-emerald-200 px-5 py-3 text-sm font-semibold text-emerald-700">{{ $buttons['send_whatsapp'] ?? 'Kirim ke WhatsApp' }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="max-w-xl">
                <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">{{ $booking['contact_tag'] ?? 'Kontak' }}</p>

                <h1 class="mt-3 text-4xl font-semibold text-slate-900">{{ $t['contact']['title'] ?? 'Konsultasi Trip' }}</h1>
                <p class="mt-4 text-sm text-slate-600">{{ $t['contact']['desc'] ?? 'Tim kami siap bantu itinerary, kapal, hingga jadwal terbaik untuk Labuan Bajo.' }}</p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ $contactWhatsappUrl }}" class="inline-flex items-center gap-2 rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">
                        <span>{{ $booking['whatsapp_label'] ?? 'WhatsApp' }}</span>
                        <span class="text-xs font-normal">{{ $contactWhatsapp }}</span>
                    </a>
                    <a href="mailto:{{ $contactEmail }}" class="inline-flex items-center gap-2 rounded-full border border-emerald-200 px-6 py-3 text-sm text-emerald-700">
                        <span>{{ $booking['email_label'] ?? 'Email' }}</span>
                        <span class="text-xs font-normal">{{ $contactEmail }}</span>
                    </a>
                </div>

                <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-600">{{ $booking['quick_contact'] ?? 'Kontak Cepat' }}</p>
                    <div class="mt-4 space-y-4 text-sm text-slate-600">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $booking['phone_label'] ?? 'Telepon' }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">{{ $contactPhone }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-emerald-700">{{ $booking['whatsapp_label'] ?? 'WhatsApp' }}</p>
                            <p class="mt-2 text-base font-semibold text-emerald-900">{{ $contactWhatsapp }}</p>
                            <a href="{{ $contactWhatsappUrl }}" class="mt-3 inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-800">{{ $t['contact']['cta_whatsapp'] ?? 'Chat Sekarang' }}</a>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-500">{{ $booking['email_label'] ?? 'Email' }}</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">{{ $contactEmail }}</p>
                            <a href="mailto:{{ $contactEmail }}" class="mt-3 inline-flex text-sm font-semibold text-emerald-700 hover:text-emerald-800">{{ $t['contact']['cta_email'] ?? 'Kirim Email' }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
