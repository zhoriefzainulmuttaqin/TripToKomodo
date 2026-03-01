<footer class="border-t border-emerald-100 bg-white">
    @php
        $contactEmail = $contactSettings['email'] ?? 'hello@triptokomodo.com';
        $contactPhone = $contactSettings['phone'] ?? '+62 812 0000 0000';
        $contactWhatsapp = $contactSettings['whatsapp'] ?? $contactPhone;
        $contactWhatsappUrl = $contactSettings['whatsapp_url'] ?? 'https://wa.me/6281200000000';
        $contactPageUrl = route('contact', ['lang' => app()->getLocale()]);
    @endphp
    <div class="mx-auto max-w-6xl px-6 py-12">

        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <h3 class="text-lg font-semibold">Labuan Bajo Travel Platform</h3>
                <p class="mt-3 text-sm text-slate-600">Eksklusif untuk penjualan paket trip Labuan Bajo: kapal premium, itinerary eksklusif, dan layanan concierge.</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-emerald-600">Kontak</h4>
                <p class="mt-3 text-sm text-slate-600">Telepon: {{ $contactPhone }}</p>
                <a href="{{ $contactWhatsappUrl }}" class="mt-2 inline-flex text-sm text-slate-600 hover:text-emerald-700">WhatsApp: {{ $contactWhatsapp }}</a>
                <a href="mailto:{{ $contactEmail }}" class="block text-sm text-slate-600 hover:text-emerald-700">Email: {{ $contactEmail }}</a>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-emerald-600">Quick Links</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="hover:text-emerald-700">Paket Trip</a></li>

                    <li><a href="#faq" class="hover:text-emerald-700">FAQ</a></li>
                    <li><a href="{{ $contactPageUrl }}" class="hover:text-emerald-700">Konsultasi</a></li>

                </ul>
            </div>
        </div>
        <div class="mt-8 text-xs text-slate-500">© {{ date('Y') }} Trip Labuan Bajo. All rights reserved.</div>
    </div>
</footer>
