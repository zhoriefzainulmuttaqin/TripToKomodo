@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    @php
        $totalAssets = max(1, $destinationCount + $tourPackageCount + $customerCount + $rentalCarCount);

        $totalDeviceVisits = $mobileVisits + $desktopVisits + $tabletVisits + $otherDeviceVisits;

        if ($totalDeviceVisits > 0) {
            $mobileRatio = (int) round(($mobileVisits / $totalDeviceVisits) * 100);
            $desktopRatio = (int) round(($desktopVisits / $totalDeviceVisits) * 100);
            $tabletRatio = (int) round(($tabletVisits / $totalDeviceVisits) * 100);
            $otherRatio = max(0, 100 - ($mobileRatio + $desktopRatio + $tabletRatio));
        } else {
            $mobileRatio = 25;
            $desktopRatio = 25;
            $tabletRatio = 25;
            $otherRatio = 25;
        }

        $currentMonthIndex = (int) now()->month - 1;
        $activeMonthName = $visitMonthLabels[$currentMonthIndex] ?? 'Jan';
        $activeMonthCount = (int) ($visitMonthlyCounts[$currentMonthIndex] ?? 0);
    @endphp

    <section class="grid gap-6 xl:grid-cols-[2fr_1fr]">
        <div class="rounded-3xl bg-emerald-900 px-6 py-7 text-white shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-200">Dashboard</p>
            <h1 class="mt-3 text-3xl font-bold">Good Night Admin</h1>
            <p class="mt-2 max-w-xl text-sm text-emerald-100">Pantau performa website, konten, dan data bisnis dalam satu tampilan ringkas.</p>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('admin.destinations.create') }}" class="rounded-xl bg-lime-300 px-4 py-2 text-sm font-semibold text-emerald-950 hover:bg-lime-200">Tambah Destinasi</a>
                <a href="{{ route('admin.tools.invoices.index') }}" class="rounded-xl border border-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Lihat Invoices</a>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <div class="flex items-start justify-between">
                <h2 class="text-xl font-semibold text-slate-900">Key Insights</h2>
                <span class="material-symbols-outlined text-slate-400">more_horiz</span>
            </div>

            <p class="mt-5 text-xs uppercase tracking-[0.2em] text-slate-400">Total Data</p>
            <p class="mt-1 text-4xl font-bold text-slate-900">{{ $destinationCount + $tourPackageCount + $customerCount + $rentalCarCount }}</p>

            <div class="mt-5 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r from-emerald-700 via-amber-400 to-lime-300" style="width: 100%"></div>
            </div>

            <div class="mt-5 grid grid-cols-2 gap-3 text-xs">
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-slate-500">Destinasi</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $destinationCount }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-slate-500">Paket Trip</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $tourPackageCount }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-slate-500">Customers</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $customerCount }}</p>
                </div>
                <div class="rounded-xl bg-slate-50 p-3">
                    <p class="text-slate-500">Rental Cars</p>
                    <p class="mt-1 text-lg font-semibold text-slate-900">{{ $rentalCarCount }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Negara User (30 Hari)</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($totalCountries) }}</p>
            <p class="mt-1 text-xs text-slate-500">Negara unik terdeteksi</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Tombol Kontak Diklik</p>
            <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($contactClicks) }}</p>
            <p class="mt-1 text-xs text-slate-500">Total klik 30 hari terakhir</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-2">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Top Negara User</p>
            <div class="mt-3 flex flex-wrap gap-2 text-xs">
                @forelse ($topCountries as $country)
                    <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 font-medium text-slate-700">{{ $country->country_code }} • {{ number_format((int) $country->total) }}</span>
                @empty
                    <span class="text-slate-500">Belum ada data negara user.</span>
                @endforelse
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-[2fr_1fr]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm" x-data="{ activeMonth: '{{ $activeMonthName }}', activeCount: {{ $activeMonthCount }} }">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-900">Website Visits</h2>
                    <p class="mt-1 text-sm text-slate-500">Ringkasan trafik {{ $visitYear }} • Total {{ number_format($totalVisitsYear) }} kunjungan</p>
                </div>
                <span class="rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-500">{{ $visitYear }}</span>
            </div>

            <div class="mt-5 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-800">
                <span class="font-semibold" x-text="activeMonth"></span>
                <span> : </span>
                <span class="font-bold" x-text="new Intl.NumberFormat().format(activeCount)"></span>
                <span> kunjungan</span>
            </div>

            <div class="mt-6 grid grid-cols-12 items-end gap-3">
                @foreach ($visitsBars as $bar)
                    <button
                        type="button"
                        @click="activeMonth='{{ $visitMonthLabels[$loop->index] }}'; activeCount={{ (int) $visitMonthlyCounts[$loop->index] }}"
                        class="group flex flex-col items-center gap-2"
                    >
                        <div class="relative h-44 w-full max-w-[26px] overflow-hidden rounded-lg bg-slate-100">
                            <div class="absolute bottom-0 left-0 right-0 rounded-lg bg-gradient-to-t from-emerald-900 to-lime-300 transition-all group-hover:from-emerald-700" style="height: {{ max(3, $bar) }}%"></div>
                        </div>
                        <p class="text-[10px] font-medium text-slate-400">{{ $visitMonthLabels[$loop->index] }}</p>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-2xl font-semibold text-slate-900">Traffic Device (30 Hari)</h2>

            <div class="mx-auto mt-6 grid h-52 w-52 place-items-center rounded-full" style="background: conic-gradient(#064e3b 0 {{ $mobileRatio }}%, #fbbf24 {{ $mobileRatio }}% {{ $mobileRatio + $desktopRatio }}%, #84cc16 {{ $mobileRatio + $desktopRatio }}% {{ $mobileRatio + $desktopRatio + $tabletRatio }}%, #22d3ee {{ $mobileRatio + $desktopRatio + $tabletRatio }}% 100%);">
                <div class="grid h-32 w-32 place-items-center rounded-full bg-white text-center">
                    <div>
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400">Total</p>
                        <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($currentVisits) }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <p class="flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-emerald-900"></span>Mobile</p>
                    <p class="font-semibold text-slate-900">{{ number_format($mobileVisits) }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>Desktop</p>
                    <p class="font-semibold text-slate-900">{{ number_format($desktopVisits) }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-lime-500"></span>Tablet</p>
                    <p class="font-semibold text-slate-900">{{ number_format($tabletVisits) }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="flex items-center gap-2 text-slate-600"><span class="h-2.5 w-2.5 rounded-full bg-cyan-400"></span>Lainnya</p>
                    <p class="font-semibold text-slate-900">{{ number_format($otherDeviceVisits) }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">List Data Customer</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Nama</th>
                            <th class="px-5 py-3">Kontak</th>
                            <th class="px-5 py-3">Negara</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestCustomers as $customer)
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $customer->full_name }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $customer->email ?: ($customer->phone ?: '-') }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $customer->country ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-slate-500">Belum ada data customer.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">List Data Artikel Terbaru</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Judul</th>
                            <th class="px-5 py-3">Bahasa</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestArticles as $article)
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $article->title }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ strtoupper($article->language_code) }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $article->is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ $article->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-slate-500">Belum ada data artikel.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="mt-6 grid gap-6 xl:grid-cols-2">
        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">List Data Destinasi</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Nama</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestDestinations as $destination)
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $destination->name }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ $destination->category ?: '-' }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $destination->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                                        {{ $destination->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-slate-500">Belum ada data destinasi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-4">
                <h3 class="text-lg font-semibold text-slate-900">List Data Paket Trip</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                        <tr>
                            <th class="px-5 py-3">Kode</th>
                            <th class="px-5 py-3">Durasi</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latestTourPackages as $pkg)
                            <tr>
                                <td class="px-5 py-3 font-medium text-slate-800">{{ $pkg->code }}</td>
                                <td class="px-5 py-3 text-slate-600">{{ (int) $pkg->duration_days }}D/{{ (int) $pkg->duration_nights }}N</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $pkg->status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                        {{ ucfirst($pkg->status ?? 'draft') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-slate-500">Belum ada data paket trip.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
