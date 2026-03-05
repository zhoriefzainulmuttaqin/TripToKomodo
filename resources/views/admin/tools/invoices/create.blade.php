@extends('layouts.admin')

@section('title', 'Create Invoice | Tools')


@section('content')
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Tools / Invoicing</p>
            <h1 class="mt-2 text-2xl font-semibold text-slate-900">Create Invoice</h1>
            <p class="mt-2 text-sm text-slate-600">Create a <span class="font-semibold">manual</span> invoice (no web transaction data).</p>

        </div>

        <a href="{{ route('admin.tools.invoices.index') }}" class="rounded-full border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-700">Back</a>

    </div>

    <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6">
        <form method="POST" action="{{ route('admin.tools.invoices.store') }}" class="grid gap-4">
            @csrf

            <input type="hidden" name="customer_id" id="customer_id" value="{{ old('customer_id') }}" />

            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-slate-600">Customer (optional)</p>
                <p class="mt-2 text-sm text-slate-600">Select from CRM to auto-fill, or type manually.</p>

                <div class="mt-3 grid gap-3 md:grid-cols-[1fr_auto]">
                    <div class="relative">
                        <input id="customer_search" type="text" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm" placeholder="Search by name / WhatsApp / email" autocomplete="off" />
                        <div id="customer_results" class="absolute z-10 mt-2 hidden w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg"></div>
                    </div>
                    <button type="button" id="customer_clear" class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700">Clear</button>
                </div>

                <p id="customer_hint" class="mt-2 text-xs text-slate-500"></p>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Customer Name</label>
                    <input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="Full name" />
                    @error('customer_name')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Email (optional)</label>
                    <input id="customer_email" name="customer_email" value="{{ old('customer_email') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="email@example.com" />
                    @error('customer_email')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Phone / WhatsApp (optional)</label>
                    <input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="08xxxxxxxxxx" />

                    @error('customer_phone')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Customer Address (optional)</label>

                    <input name="customer_address" value="{{ old('customer_address') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" />
                    @error('customer_address')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Trip / Package</label>

                    <input name="tour_package_title" value="{{ old('tour_package_title') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="e.g. Open Trip Komodo 3D2N" />

                    @error('tour_package_title')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Travel Date (optional)</label>
                    <input type="date" name="travel_date" value="{{ old('travel_date') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" />
                    @error('travel_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Traveler Count (optional)</label>
                    <input name="traveler_count" value="{{ old('traveler_count') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" />
                    @error('traveler_count')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Issued At (optional)</label>
                    <input type="datetime-local" name="issued_at" value="{{ old('issued_at') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" />
                    @error('issued_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Payment Status</label>

                    <select name="status" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm">
                        <option value="dp" {{ old('status', 'dp') === 'dp' ? 'selected' : '' }}>Deposit</option>

                        <option value="paid" {{ old('status') === 'paid' ? 'selected' : '' }}>Paid</option>

                    </select>
                    @error('status')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Total (IDR)</label>
                    <input name="total_amount_idr" value="{{ old('total_amount_idr') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="e.g. 3500000" />

                    @error('total_amount_idr')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Paid (IDR)</label>
                    <input name="paid_amount_idr" value="{{ old('paid_amount_idr', 0) }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="e.g. 1000000" />

                    @error('paid_amount_idr')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Payment Method (optional)</label>
                    <input name="payment_method" value="{{ old('payment_method') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="Bank transfer / Cash / etc" />

                    @error('payment_method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Payment Reference (optional)</label>
                    <input name="payment_reference" value="{{ old('payment_reference') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="Transaction ID / note" />

                    @error('payment_reference')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600">Paid At (optional)</label>
                    <input type="datetime-local" name="paid_at" value="{{ old('paid_at') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" />
                    @error('paid_at')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600">Booking ID (optional)</label>
                    <input name="booking_id" value="{{ old('booking_id') }}" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm" placeholder="Fill only if you want to link" />

                    @error('booking_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600">Notes (optional)</label>
                <textarea name="notes" rows="3" class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-2 text-sm">{{ old('notes') }}</textarea>
                @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-3">
                <button class="rounded-full bg-emerald-600 px-6 py-3 text-sm font-semibold text-white">Create Invoice</button>
                <a href="{{ route('admin.tools.invoices.index') }}" class="rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700">Cancel</a>

            </div>
        </form>
    </div>

    <script>
        (function () {
            var searchInput = document.getElementById('customer_search');
            var results = document.getElementById('customer_results');
            var hint = document.getElementById('customer_hint');
            var clearBtn = document.getElementById('customer_clear');

            var customerId = document.getElementById('customer_id');
            var nameInput = document.getElementById('customer_name');
            var emailInput = document.getElementById('customer_email');
            var phoneInput = document.getElementById('customer_phone');

            var source = '{{ route('admin.customers.search') }}';
            var t = null;
            var controller = null;

            function setHint(text) {
                if (hint) hint.textContent = text || '';
            }

            function showResults() {
                if (results) results.classList.remove('hidden');
            }

            function hideResults() {
                if (results) results.classList.add('hidden');
            }

            function escapeHtml(s) {
                return (s || '').replace(/[&<>"']/g, function (c) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
                });
            }

            function render(items) {
                if (!results) return;
                if (!items || items.length === 0) {
                    results.innerHTML = '<div class="px-4 py-3 text-sm text-slate-600">No customers found.</div>';
                    showResults();
                    return;
                }

                var html = '';
                for (var i = 0; i < items.length; i++) {
                    var c = items[i] || {};
                    var line2 = [c.phone, c.email].filter(Boolean).join(' • ');
                    html += ''
                        + '<button type="button" data-id="' + escapeHtml(String(c.id || '')) + '"'
                        + ' data-name="' + escapeHtml(String(c.full_name || '')) + '"'
                        + ' data-email="' + escapeHtml(String(c.email || '')) + '"'
                        + ' data-phone="' + escapeHtml(String(c.phone || '')) + '"'
                        + ' class="block w-full px-4 py-3 text-left hover:bg-emerald-50">'
                        + '  <div class="text-sm font-semibold text-slate-900">' + escapeHtml(String(c.full_name || '')) + '</div>'
                        + '  <div class="mt-1 text-xs text-slate-500">' + escapeHtml(line2) + '</div>'
                        + '</button>';
                }

                results.innerHTML = html;
                showResults();
            }

            function applyCustomer(c) {
                if (!c) return;
                if (customerId) customerId.value = c.id || '';
                if (nameInput && (!nameInput.value || nameInput.value.trim() === '')) nameInput.value = c.full_name || '';
                if (emailInput && (!emailInput.value || emailInput.value.trim() === '')) emailInput.value = c.email || '';
                if (phoneInput && (!phoneInput.value || phoneInput.value.trim() === '')) phoneInput.value = c.phone || '';
                if (searchInput) searchInput.value = c.full_name || '';
                setHint('Selected from CRM. You can still edit the fields manually.');
                hideResults();
            }

            function clearCustomer() {
                if (customerId) customerId.value = '';
                setHint('');
                if (searchInput) searchInput.value = '';
                hideResults();
            }

            function fetchCustomers(q) {
                if (!source) return;
                if (controller) controller.abort();
                controller = new AbortController();

                var url = source + '?q=' + encodeURIComponent(q || '') + '&limit=20';
                setHint(q ? 'Searching…' : '');

                fetch(url, { headers: { 'Accept': 'application/json' }, signal: controller.signal })
                    .then(function (r) { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(function (data) {
                        var customers = (data && data.customers) ? data.customers : [];
                        render(customers);
                        setHint('');
                    })
                    .catch(function (e) {
                        if (e && e.name === 'AbortError') return;
                        setHint('Could not load customers.');
                        hideResults();
                    });
            }

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    var q = (searchInput.value || '').trim();
                    if (t) clearTimeout(t);
                    t = setTimeout(function () {
                        if (q.length < 2) {
                            hideResults();
                            setHint('');
                            return;
                        }
                        fetchCustomers(q);
                    }, 250);
                });

                searchInput.addEventListener('focus', function () {
                    var q = (searchInput.value || '').trim();
                    if (q.length >= 2) fetchCustomers(q);
                });
            }

            if (results) {
                results.addEventListener('click', function (e) {
                    var target = e.target;
                    while (target && target !== results && !target.getAttribute('data-id')) {
                        target = target.parentNode;
                    }
                    if (!target || target === results) return;

                    applyCustomer({
                        id: target.getAttribute('data-id'),
                        full_name: target.getAttribute('data-name'),
                        email: target.getAttribute('data-email'),
                        phone: target.getAttribute('data-phone')
                    });
                });
            }

            document.addEventListener('click', function (e) {
                if (!results || results.classList.contains('hidden')) return;
                if (e.target === results || results.contains(e.target)) return;
                if (e.target === searchInput) return;
                hideResults();
            });

            if (clearBtn) {
                clearBtn.addEventListener('click', clearCustomer);
            }
        })();
    </script>
@endsection
