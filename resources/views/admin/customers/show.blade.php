@extends('layouts.admin')

@section('title', ($customer->full_name ?? 'Customer') . ' | CRM')

@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">CRM / Customers</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">{{ $customer->full_name }}</h1>
            <p class="mt-2 text-sm text-slate-600">Detail customer untuk pencatatan internal.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700">Kembali</a>
            <a href="{{ route('admin.customers.edit', $customer) }}" class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Edit</a>

            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="inline" onsubmit="return confirm('Hapus customer ini?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-5 py-2.5 text-sm font-semibold text-rose-700">Hapus</button>
            </form>
        </div>

    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="rounded-3xl border border-slate-200 bg-white p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Kontak</p>
                    <div class="mt-2 text-sm">
                        <div class="text-slate-900"><span class="text-slate-500">WhatsApp:</span> {{ $customer->phone ?? '-' }}</div>
                        <div class="mt-1 text-slate-900"><span class="text-slate-500">Email:</span> {{ $customer->email ?? '-' }}</div>
                    </div>
                </div>
                <div>
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Asal</p>
                    <div class="mt-2 text-sm text-slate-900">{{ $customer->country ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-6">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Kontak lain</p>
                @php
                    $others = (array) ($customer->other_contacts ?? []);
                @endphp
                @if (!empty($others))
                    <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-slate-700">
                        @foreach ($others as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-2 text-sm text-slate-600">-</p>
                @endif
            </div>

            @if (!empty($customer->notes))
                <div class="mt-6">
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Catatan</p>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700">{{ $customer->notes }}</p>
                </div>
            @endif
        </div>

        <aside class="rounded-3xl border border-slate-200 bg-white p-6">
            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Dokumen</p>
            @if (!empty($customer->document_path))
                <p class="mt-2 text-sm font-semibold text-slate-900">Ada dokumen</p>
                <p class="mt-1 text-xs text-slate-500">{{ $customer->document_original_name ?? '' }}</p>
                <a href="{{ asset('storage/' . $customer->document_path) }}" target="_blank" class="mt-4 inline-flex rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Lihat / Download</a>
            @else
                <p class="mt-2 text-sm text-slate-600">Belum ada dokumen.</p>
            @endif

            <div class="mt-6 border-t border-slate-200 pt-5 text-xs text-slate-500">
                <p>ID: #{{ $customer->id }}</p>
                <p>Created: {{ $customer->created_at?->format('d M Y H:i') ?? '-' }}</p>
                <p>By: {{ $customer->creator?->name ?? 'System' }}</p>
            </div>
        </aside>
    </div>

    {{-- Invoice History --}}
    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
        <div class="flex items-center justify-between gap-4">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">Invoice History</p>
            <a href="{{ route('admin.tools.invoices.create') }}" class="rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">+ New Invoice</a>
        </div>

        @if ($invoices->isEmpty())
            <p class="mt-4 text-sm text-slate-500">No invoices linked to this customer yet.</p>
        @else
            <div class="mt-4 overflow-hidden rounded-2xl border border-slate-200">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-xs uppercase tracking-[0.2em] text-slate-500">
                            <th class="px-4 py-3">Invoice #</th>
                            <th class="px-4 py-3">Trip</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-right">Remaining</th>
                            <th class="px-4 py-3">Issued</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $inv)
                            <tr class="border-t border-slate-200">
                                <td class="px-4 py-3 font-semibold text-slate-900">{{ $inv->invoice_number }}</td>
                                <td class="px-4 py-3 text-slate-700">{{ $inv->tour_package_title ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($inv->status === 'paid')
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Paid</span>
                                    @else
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">Deposit</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-slate-700">Rp {{ number_format((float) $inv->total_amount_idr, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right {{ (float) $inv->remaining_amount_idr > 0 ? 'text-amber-700 font-semibold' : 'text-emerald-700' }}">
                                    Rp {{ number_format((float) $inv->remaining_amount_idr, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-slate-500">{{ $inv->issued_at?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.tools.invoices.show', $inv) }}" class="text-xs font-semibold text-emerald-700 hover:underline">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
