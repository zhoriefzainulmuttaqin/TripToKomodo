<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;


class CustomerController
{
    public function index(Request $request): View
    {
        $query = Customer::query()->orderByDesc('id');

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('country', 'like', "%{$q}%");
            });
        }

        $customers = $query->paginate(20)->withQueryString();

        return view('admin.customers.index', [
            'customers' => $customers,
        ]);
    }

    public function create(): View
    {
        return view('admin.customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:120'],
            'country' => ['nullable', 'string', 'max:80'],
            'other_contacts' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $customer = new Customer();
        $customer->full_name = $validated['full_name'];
        $customer->phone = $validated['phone'] ?? null;
        $customer->email = $validated['email'] ?? null;
        $customer->country = $validated['country'] ?? null;
        $customer->other_contacts = $this->parseContacts($validated['other_contacts'] ?? null);
        $customer->notes = $validated['notes'] ?? null;
        $customer->created_by = Auth::id();

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $path = $file->store('customers/documents', 'public');
            $customer->document_path = $path;
            $customer->document_original_name = $file->getClientOriginalName();
            $customer->document_mime = $file->getClientMimeType();
            $customer->document_size = (int) $file->getSize();
        }

        $customer->save();

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Customer berhasil ditambahkan.');
    }

    public function show(Customer $customer): View
    {
        $customer->loadMissing(['creator']);

        $invoices = \App\Models\Invoice::query()
            ->where('customer_id', $customer->id)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->get(['id', 'invoice_number', 'tour_package_title', 'status', 'total_amount_idr', 'paid_amount_idr', 'remaining_amount_idr', 'issued_at']);

        return view('admin.customers.show', [
            'customer' => $customer,
            'invoices' => $invoices,
        ]);
    }

    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:160'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:120'],
            'country' => ['nullable', 'string', 'max:80'],
            'other_contacts' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:5000'],

            'remove_document' => ['nullable', 'boolean'],
            'document' => ['nullable', 'file', 'mimes:pdf,jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $customer->full_name = $validated['full_name'];
        $customer->phone = $validated['phone'] ?? null;
        $customer->email = $validated['email'] ?? null;
        $customer->country = $validated['country'] ?? null;
        $customer->other_contacts = $this->parseContacts($validated['other_contacts'] ?? null);
        $customer->notes = $validated['notes'] ?? null;

        if (($validated['remove_document'] ?? false) && !empty($customer->document_path)) {
            Storage::disk('public')->delete($customer->document_path);
            $customer->document_path = null;
            $customer->document_original_name = null;
            $customer->document_mime = null;
            $customer->document_size = null;
        }

        if ($request->hasFile('document')) {
            if (!empty($customer->document_path)) {
                Storage::disk('public')->delete($customer->document_path);
            }

            $file = $request->file('document');
            $path = $file->store('customers/documents', 'public');
            $customer->document_path = $path;
            $customer->document_original_name = $file->getClientOriginalName();
            $customer->document_mime = $file->getClientMimeType();
            $customer->document_size = (int) $file->getSize();
        }

        $customer->save();

        return redirect()->route('admin.customers.show', $customer)->with('status', 'Customer berhasil diperbarui.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if (!empty($customer->document_path)) {
            Storage::disk('public')->delete($customer->document_path);
        }

        $customer->delete();

        return redirect()->route('admin.customers.index')->with('status', 'Customer berhasil dihapus.');
    }

    public function countries(): JsonResponse
    {
        $countries = Cache::remember('crm_countries_v1', now()->addDay(), function (): array {
            try {
                $response = Http::timeout(10)->get('https://restcountries.com/v3.1/all', [
                    'fields' => 'name',
                ]);

                if (!$response->successful()) {
                    return [];
                }

                $rows = (array) $response->json();
                $names = [];

                foreach ($rows as $row) {
                    $name = (string) data_get($row, 'name.common', '');
                    $name = trim($name);
                    if ($name !== '') {
                        $names[] = $name;
                    }
                }

                $names = array_values(array_unique($names));
                sort($names, SORT_NATURAL | SORT_FLAG_CASE);

                return $names;
            } catch (\Throwable) {
                return [];
            }
        });

        return response()->json([
            'countries' => $countries,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min(50, $limit));

        $query = Customer::query()->select(['id', 'full_name', 'phone', 'email', 'country'])->orderBy('full_name');

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('country', 'like', "%{$q}%");
            });
        }

        $customers = $query->limit($limit)->get();

        return response()->json([
            'customers' => $customers,
        ]);
    }

    private function parseContacts(?string $raw): ?array


    {
        $raw = $raw !== null ? trim($raw) : '';
        if ($raw === '') {
            return null;
        }

        $lines = preg_split("/\r\n|\r|\n/", $raw) ?: [];
        $items = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            $items[] = $line;
        }

        return empty($items) ? null : $items;
    }
}
