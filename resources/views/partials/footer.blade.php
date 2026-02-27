<footer class="border-t border-emerald-100 bg-white">
    <div class="mx-auto max-w-6xl px-6 py-12">
        <div class="grid gap-8 md:grid-cols-3">
            <div>
                <h3 class="text-lg font-semibold">Labuan Bajo Travel Platform</h3>
                <p class="mt-3 text-sm text-slate-600">Eksklusif untuk penjualan paket trip Labuan Bajo: kapal premium, itinerary eksklusif, dan layanan concierge.</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-emerald-600">Kontak</h4>
                <p class="mt-3 text-sm text-slate-600">WhatsApp: +62 812 0000 0000</p>
                <p class="text-sm text-slate-600">Email: hello@triptokomodo.com</p>
            </div>
            <div>
                <h4 class="text-sm font-semibold text-emerald-600">Quick Links</h4>
                <ul class="mt-3 space-y-2 text-sm text-slate-600">
                    <li><a href="{{ route('tours.index', ['lang' => app()->getLocale()]) }}" class="hover:text-emerald-700">Paket Trip</a></li>

                    <li><a href="#faq" class="hover:text-emerald-700">FAQ</a></li>
                    <li><a href="#contact" class="hover:text-emerald-700">Konsultasi</a></li>
                </ul>
            </div>
        </div>
        <div class="mt-8 text-xs text-slate-500">© {{ date('Y') }} Trip Labuan Bajo. All rights reserved.</div>
    </div>
</footer>
