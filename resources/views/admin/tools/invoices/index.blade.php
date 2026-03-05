@extends('layouts.admin')

@section('title', 'Invoices | Tools')

@section('content')
    @php
        $totalData = $invoices->total();
        $shownData = $invoices->count();
        $paidData = $invoices->where('status', 'paid')->count();
        $dpData = $shownData - $paidData;
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Invoice</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Invoice</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }} invoices</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }} invoices</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Paid</p><p class="mt-1 text-2xl font-bold">{{ $paidData }} invoices</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Deposit</p><p class="mt-1 text-2xl font-bold">{{ $dpData }} invoices</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full flex-col gap-3 md:flex-row">
                <div class="relative w-full md:max-w-md">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input name="q" value="{{ request('q') }}" class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-4 text-sm" placeholder="Invoice / name / email / phone / trip" />
                </div>
                <select name="status" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">All</option>
                    <option value="dp" {{ request('status')==='dp'?'selected':'' }}>Deposit</option>
                    <option value="paid" {{ request('status')==='paid'?'selected':'' }}>Paid</option>
                </select>
                <button class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
            </div>

            <a href="{{ route('admin.tools.invoices.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Add Invoice</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                    <th class="px-5 py-4">Invoice</th>
                    <th class="px-5 py-4">Customer</th>
                    <th class="px-5 py-4">Trip</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Total</th>
                    <th class="px-5 py-4">Paid</th>
                    <th class="px-5 py-4">Issued</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($invoices as $inv)
                    <tr>
                        <td class="px-5 py-4 font-semibold text-slate-900">{{ $inv->invoice_number }}</td>
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-900">{{ $inv->customer_name ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $inv->customer_email ?? $inv->customer_phone ?? '' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-slate-900">{{ $inv->tour_package_title ?? '-' }}</div>
                            <div class="text-xs text-slate-500">{{ $inv->booking_id ? 'Booking #' . $inv->booking_id : 'Manual' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $inv->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $inv->status === 'paid' ? 'Paid' : 'Deposit' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">Rp {{ number_format((float) $inv->total_amount_idr, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">Rp {{ number_format((float) $inv->paid_amount_idr, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-xs text-slate-600">{{ optional($inv->issued_at)->format('d M Y H:i') ?? '-' }}</td>
                        <td class="px-5 py-4 text-right">
                            <a class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700" href="{{ route('admin.tools.invoices.show', $inv) }}">View</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-600">No invoices yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $invoices->links() }}</div>
@endsection
