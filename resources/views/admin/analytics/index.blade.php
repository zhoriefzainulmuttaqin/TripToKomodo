@extends('layouts.admin')

@section('title', 'Analytics | ' . ($siteName ?? 'Trip to Komodo'))

@section('content')
    <div class="space-y-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:p-8">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Web Analytics</p>
                    <h1 class="mt-2 text-2xl font-bold text-slate-900 lg:text-3xl">Ringkasan Traffic Website</h1>
                    <p class="mt-2 text-sm text-slate-500">Kunjungan web, halaman teratas, negara user, device, klik kontak, engagement, dan source traffic.</p>
                </div>

                <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1 text-sm">
                    @foreach ([7, 30, 90] as $range)
                        <a href="{{ route('admin.analytics.index', ['range' => $range]) }}" class="rounded-lg px-3 py-1.5 font-medium {{ (int) $days === $range ? 'bg-emerald-900 text-white' : 'text-slate-600 hover:text-slate-900' }}">{{ $range }} hari</a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Total Kunjungan</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($totalVisits) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Unique Visitor</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($uniqueVisitors) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Klik Tombol Kontak</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($contactClicks) }}</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.15em] text-slate-500">Engagement</p>
                <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($engagementRate, 1) }}%</p>
                <p class="mt-1 text-xs text-slate-500">Rata-rata {{ number_format($avgEngagement, 1) }} detik/kunjungan</p>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-slate-600">Total Kunjungan Tiap Halaman</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Halaman</th>
                                <th class="px-5 py-3 text-right">Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($visitsByPage as $item)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-slate-700">{{ $item->page_path ?: '-' }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-slate-900">{{ number_format((int) $item->total) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-5 py-6 text-center text-slate-500">Belum ada data kunjungan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-slate-600">Negara User</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.12em] text-slate-500">
                            <tr>
                                <th class="px-5 py-3">Negara</th>
                                <th class="px-5 py-3 text-right">Kunjungan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($visitsByCountry as $item)
                                <tr>
                                    <td class="px-5 py-3 font-medium text-slate-700">{{ $item->country_code ?: 'Unknown' }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-slate-900">{{ number_format((int) $item->total) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="px-5 py-6 text-center text-slate-500">Belum ada data negara.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-3">
            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-slate-600">Device User</h2>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($visitsByDevice as $item)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                            <span class="font-medium text-slate-700">{{ ucfirst($item->device_type ?: 'unknown') }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format((int) $item->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada data device.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-slate-600">Source Traffic</h2>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($trafficSources as $item)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                            <span class="font-medium text-slate-700">{{ ucfirst($item->source_channel ?: 'unknown') }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format((int) $item->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada data source traffic.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 px-5 py-4">
                    <h2 class="text-sm font-semibold uppercase tracking-[0.15em] text-slate-600">Tombol Kontak Diklik</h2>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($contactTargets as $item)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                            <span class="font-medium text-slate-700 truncate">{{ $item->contact_target ?: 'Unknown' }}</span>
                            <span class="font-semibold text-slate-900">{{ number_format((int) $item->total) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada klik tombol kontak.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
