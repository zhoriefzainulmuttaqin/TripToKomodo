<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        /* DomPDF friendly CSS (avoid flex/grid). */
        @page { margin: 26px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #0f172a; }
        .muted { color: #64748b; }
        .small { font-size: 11px; }
        .title { font-size: 20px; font-weight: 700; margin: 0; }
        .h2 { font-size: 14px; font-weight: 700; margin: 0 0 6px 0; }
        .card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; }
        .row { width: 100%; }
        .col { vertical-align: top; }
        .w-50 { width: 50%; }
        .w-60 { width: 60%; }
        .w-40 { width: 40%; }
        .mt-8 { margin-top: 8px; }
        .mt-12 { margin-top: 12px; }
        .mt-16 { margin-top: 16px; }
        .mt-24 { margin-top: 24px; }
        .right { text-align: right; }
        .bold { font-weight: 700; }
        .pill { display: inline-block; padding: 4px 10px; border-radius: 999px; border: 1px solid #cbd5e1; font-size: 11px; }
        .pill-paid { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
        .pill-dp { background: #fffbeb; border-color: #fde68a; color: #92400e; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px 12px; border-top: 1px solid #e2e8f0; }
        thead th { background: #f8fafc; border-top: 1px solid #e2e8f0; font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: #64748b; }
        .totals td { border-top: none; padding: 6px 0; }
        .divider { border-top: 1px solid #e2e8f0; margin-top: 18px; padding-top: 12px; }
    </style>
</head>
<body>
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

    $isPaid = $invoice->status === 'paid';
    $statusLabel = $isPaid ? 'Paid' : 'Deposit';

@endphp

<table class="row">
    <tr>
        <td class="col w-60">
            <p class="muted small" style="margin:0; text-transform: uppercase; letter-spacing: 0.2em;">Invoice</p>
            <p class="title" style="margin:6px 0 0 0;">{{ $invoice->invoice_number }}</p>
            <div class="mt-8 small muted">
                <div><span class="muted">Issued:</span> {{ $issuedAt }}</div>
                <div class="mt-8"><span class="muted">Status:</span>
                    <span class="pill {{ $isPaid ? 'pill-paid' : 'pill-dp' }}">{{ $statusLabel }}</span>
                </div>
            </div>
        </td>
        <td class="col w-40 right">
            <p class="bold" style="margin:0;">{{ config('app.name', 'Trip to Komodo') }}</p>
            <p class="muted small" style="margin:6px 0 0 0;">{{ config('app.url') }}</p>
        </td>
    </tr>
</table>

<table class="row mt-16">
    <tr>
        <td class="col w-50" style="padding-right:10px;">
            <div class="card">
                <p class="muted small" style="margin:0; text-transform: uppercase; letter-spacing: 0.12em;">Bill To</p>
                <p class="h2 mt-12">{{ $customer }}</p>
                <div class="small muted">{{ $email }}</div>
                <div class="small muted mt-8">{{ $phone }}</div>
                @if (!empty($invoice->customer_address))
                    <div class="small muted mt-8">{{ $invoice->customer_address }}</div>
                @endif
            </div>
        </td>
        <td class="col w-50" style="padding-left:10px;">
            <div class="card">
                <p class="muted small" style="margin:0; text-transform: uppercase; letter-spacing: 0.12em;">Trip</p>
                <p class="h2 mt-12">{{ $trip }}</p>
                @if (!empty($invoice->booking_id))
                    <div class="small muted">Booking ID: #{{ $invoice->booking_id }}</div>
                @endif
                @if (!empty($invoice->travel_date))
                    <div class="small muted mt-8">Travel Date: {{ $invoice->travel_date->format('d M Y') }}</div>
                @endif
                @if (!empty($invoice->traveler_count))
                    <div class="small muted mt-8">Traveler: {{ $invoice->traveler_count }}</div>
                @endif
            </div>
        </td>
    </tr>
</table>

<div class="mt-16 card" style="padding:0; overflow:hidden;">
    <table>
        <thead>
            <tr>
                <th style="text-align:left;">Item</th>
                <th style="text-align:right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="bold">Trip Payment ({{ $statusLabel }})</div>
                    <div class="small muted mt-8">
                        {{ $invoice->payment_method ? 'Method: ' . $invoice->payment_method : '' }}
                        {{ $invoice->payment_reference ? ' • Ref: ' . $invoice->payment_reference : '' }}
                    </div>
                    <div class="small muted mt-8">Paid At: {{ $paidAt }}</div>
                </td>
                <td style="text-align:right;" class="bold">Rp {{ number_format($paid, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
</div>

<table class="row mt-16">
    <tr>
        <td class="col w-50" style="padding-right:10px;">
            @if (!empty($invoice->notes))
                <div class="card">
                    <p class="muted small" style="margin:0; text-transform: uppercase; letter-spacing: 0.12em;">Notes</p>
                    <div class="mt-12" style="white-space: pre-line;">{{ $invoice->notes }}</div>
                </div>
            @endif
        </td>
        <td class="col w-50" style="padding-left:10px;">
            <div class="card">
                <table class="totals">
                    <tr>
                        <td class="muted">Total</td>
                        <td class="right bold">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="muted">Paid</td>
                        <td class="right bold">Rp {{ number_format($paid, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="muted">Remaining</td>
                        <td class="right bold" style="color: {{ $remaining > 0 ? '#92400e' : '#065f46' }};">Rp {{ number_format($remaining, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<div class="divider small muted">
    This invoice was created by an admin{{ $invoice->creator?->name ? ' (' . $invoice->creator->name . ')' : '' }}.

</div>
</body>
</html>
