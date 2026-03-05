@extends('layouts.admin')

@section('title', $invoice->invoice_number . ' | Invoice')

@push('styles')
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: 0 !important; }
        }
    </style>
@endpush

@section('content')
    @php
        $customer = $invoice->customer_name ?? '-';
        $email = $invoice->customer_email ?? '-';
        $phone = $invoice->customer_phone ?? '-';
        $trip = $invoice->tour_package_title ?? '-';

        $total = (float) $invoice->total_amount_idr;
        $paid = (float) $invoice->paid_amount_idr;
        $remaining = (float) $invoice->remaining_amount_idr;

        $issuedAt = $invoice->issued_at ? $invoice->issued_at->format('d M Y H:i') : '-';
        $paidAt = $invoice->paid_at ? $invoice->paid_at->format('d M Y H:i') : '-';

        $statusLabel = $invoice->status === 'paid' ? 'Paid' : 'Deposit';

    @endphp

    <div class="no-print flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Tools / Invoicing</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Invoice {{ $invoice->invoice_number }}</h1>
            <p class="mt-2 text-sm text-slate-600">Download a server-generated PDF matching the invoice template.</p>

        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tools.invoices.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700">Back</a>

            <a href="{{ route('admin.tools.invoices.pdf', $invoice) }}" class="rounded-full bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white">Download PDF</a>
        </div>
    </div>

    <div class="print-area mt-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <div class="flex items-start justify-between gap-6">
            <div>
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Invoice</p>
                <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $invoice->invoice_number }}</p>
                <div class="mt-3 text-sm text-slate-600">
                    <div><span class="text-slate-500">Issued:</span> {{ $issuedAt }}</div>
                    <div><span class="text-slate-500">Status:</span> <span class="font-semibold">{{ $statusLabel }}</span></div>
                </div>
            </div>

            <div class="text-right">
                <p class="text-sm font-semibold text-slate-900">{{ config('app.name', 'Trip to Komodo') }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ $siteName ?? '' }}</p>
            </div>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-slate-200 p-5">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Bill To</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $customer }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $email }}</p>
                <p class="mt-1 text-sm text-slate-600">{{ $phone }}</p>
                @if (!empty($invoice->customer_address))
                    <p class="mt-2 text-sm text-slate-600">{{ $invoice->customer_address }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 p-5">
                <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Trip</p>
                <p class="mt-2 font-semibold text-slate-900">{{ $trip }}</p>
                @if (!empty($invoice->booking_id))
                    <p class="mt-1 text-sm text-slate-600">Booking ID: #{{ $invoice->booking_id }}</p>
                @endif
                @if (!empty($invoice->travel_date))
                    <p class="mt-1 text-sm text-slate-600">Travel Date: {{ $invoice->travel_date->format('d M Y') }}</p>
                @endif
                @if (!empty($invoice->traveler_count))
                    <p class="mt-1 text-sm text-slate-600">Traveler: {{ $invoice->traveler_count }}</p>
                @endif
            </div>
        </div>

        <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 text-xs uppercase tracking-[0.25em] text-slate-500">
                        <th class="px-5 py-3">Item</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-200">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-900">Trip Payment ({{ $statusLabel }})</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $invoice->payment_method ? 'Method: ' . $invoice->payment_method : '' }} {{ $invoice->payment_reference ? '• Ref: ' . $invoice->payment_reference : '' }}</p>
                            <p class="mt-1 text-xs text-slate-500">Paid At: {{ $paidAt }}</p>
                        </td>
                        <td class="px-5 py-4 text-right font-semibold text-slate-900">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div>
                @if (!empty($invoice->notes))
                    <p class="text-xs uppercase tracking-[0.25em] text-slate-500">Notes</p>
                    <p class="mt-2 text-sm text-slate-700 whitespace-pre-line">{{ $invoice->notes }}</p>
                @endif
            </div>

            <div class="rounded-2xl border border-slate-200 p-5">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-600">Total</span>
                    <span class="font-semibold text-slate-900">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="text-slate-600">Paid</span>
                    <span class="font-semibold text-slate-900">Rp {{ number_format($paid, 0, ',', '.') }}</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-sm">
                    <span class="text-slate-600">Remaining</span>
                    <span class="font-semibold {{ $remaining > 0 ? 'text-amber-700' : 'text-emerald-700' }}">Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t border-slate-200 pt-6 text-xs text-slate-500">
            <p>This invoice was created by an admin{{ $invoice->creator?->name ? ' (' . $invoice->creator->name . ')' : '' }}.</p>

        </div>
    </div>
@endsection

