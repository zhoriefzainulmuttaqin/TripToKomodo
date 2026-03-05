<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;


class InvoiceController
{
    public function index(Request $request): View
    {
        $query = Invoice::query()
            ->with(['creator'])
            ->orderByDesc('issued_at')
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('invoice_number', 'like', "%{$q}%")
                    ->orWhere('customer_name', 'like', "%{$q}%")
                    ->orWhere('customer_email', 'like', "%{$q}%")
                    ->orWhere('customer_phone', 'like', "%{$q}%")
                    ->orWhere('tour_package_title', 'like', "%{$q}%");
            });
        }

        $invoices = $query->paginate(20)->withQueryString();

        return view('admin.tools.invoices.index', [
            'invoices' => $invoices,
        ]);
    }

    public function create(): View
    {
        return view('admin.tools.invoices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Optional: link to a booking if you want, but invoice stays manual.
            'booking_id' => ['nullable', 'integer', Rule::exists('bookings', 'id')],

            // Optional: link to a CRM customer (auto-fill), but admin can still override manually.
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')],

            'status' => ['required', 'string', Rule::in(['dp', 'paid'])],
            'issued_at' => ['nullable', 'date'],
            'paid_at' => ['nullable', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:120'],

            'customer_name' => ['required_without:customer_id', 'string', 'max:120'],
            'customer_email' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_address' => ['nullable', 'string', 'max:255'],

            // Snapshot trip/package info stored in invoice (manual)
            'tour_package_title' => ['required', 'string', 'max:150'],
            'tour_package_id' => ['nullable', 'integer', 'min:1'],
            'travel_date' => ['nullable', 'date'],
            'traveler_count' => ['nullable', 'integer', 'min:1', 'max:100'],

            'notes' => ['nullable', 'string', 'max:1000'],

            'total_amount_idr' => ['required', 'numeric', 'min:0'],
            'paid_amount_idr' => ['required', 'numeric', 'min:0'],
        ]);


        $total = (float) $validated['total_amount_idr'];
        $paid = (float) $validated['paid_amount_idr'];

        if ($paid > $total) {
            return back()->withErrors([
                'paid_amount_idr' => 'Paid amount cannot be greater than total.',
            ])->withInput();
        }


        $status = (string) $validated['status'];

        // If status is paid, force paid = total for consistency.
        if ($status === 'paid') {
            $paid = $total;
            if (empty($validated['paid_at'])) {
                $validated['paid_at'] = now();
            }
        }


        $remaining = max(0, $total - $paid);

        $customer = null;
        if (!empty($validated['customer_id'])) {
            $customer = Customer::query()->find($validated['customer_id']);
        }

        $invoice = new Invoice();
        $invoice->booking_id = $validated['booking_id'] ?? null;
        $invoice->customer_id = $customer?->id;
        $invoice->invoice_number = Invoice::generateNextNumber();

        $invoice->status = $status;
        $invoice->issued_at = !empty($validated['issued_at']) ? $validated['issued_at'] : now();

        $invoice->currency_code = 'IDR';
        $invoice->total_amount_idr = $total;
        $invoice->paid_amount_idr = $paid;
        $invoice->remaining_amount_idr = $remaining;

        $invoice->payment_method = $validated['payment_method'] ?? null;
        $invoice->payment_reference = $validated['payment_reference'] ?? null;
        $invoice->paid_at = $validated['paid_at'] ?? null;

        $customerName = trim((string) ($validated['customer_name'] ?? ''));
        $customerEmail = trim((string) ($validated['customer_email'] ?? ''));
        $customerPhone = trim((string) ($validated['customer_phone'] ?? ''));

        $invoice->customer_name = $customerName !== '' ? $customerName : ($customer?->full_name ?: null);
        $invoice->customer_email = $customerEmail !== '' ? $customerEmail : ($customer?->email ?: null);
        $invoice->customer_phone = $customerPhone !== '' ? $customerPhone : ($customer?->phone ?: null);
        $invoice->customer_address = !empty($validated['customer_address']) ? $validated['customer_address'] : null;



        $invoice->tour_package_id = $validated['tour_package_id'] ?? null;
        $invoice->tour_package_title = $validated['tour_package_title'] ?? null;
        $invoice->travel_date = $validated['travel_date'] ?? null;
        $invoice->traveler_count = $validated['traveler_count'] ?? null;

        $invoice->notes = $validated['notes'] ?? null;
        $invoice->created_by = Auth::id();
        $invoice->save();

        return redirect()->route('admin.tools.invoices.show', $invoice)->with('status', 'Invoice created successfully.');

    }

    public function show(Invoice $invoice): View
    {
        $invoice->loadMissing(['creator']);

        return view('admin.tools.invoices.show', [
            'invoice'  => $invoice,
            'siteName' => config('app.url'),
        ]);
    }

    public function pdf(Invoice $invoice): Response
    {
        $invoice->loadMissing(['creator']);

        $pdf = Pdf::loadView('admin.tools.invoices.pdf', [
            'invoice'  => $invoice,
            'siteName' => config('app.url'),
        ])->setPaper('a4');

        $filename = (string) $invoice->invoice_number . '.pdf';

        return $pdf->download($filename);
    }
}

