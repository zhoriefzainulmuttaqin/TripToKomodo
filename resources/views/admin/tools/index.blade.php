@extends('layouts.admin')

@section('title', 'Tools | Admin')

@section('content')
    <div class="mb-6">
        <p class="text-xs uppercase tracking-[0.3em] text-emerald-600">Tools</p>
        <h1 class="mt-2 text-2xl font-semibold text-slate-900">Admin Tools</h1>
        <p class="mt-2 text-sm text-slate-600">Operational utilities for administrators.</p>

    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('admin.tools.invoices.index') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm hover:border-emerald-200">
            <p class="text-xs uppercase tracking-[0.25em] text-emerald-600">Invoicing</p>
            <p class="mt-2 text-lg font-semibold text-slate-900">Invoices</p>
            <p class="mt-2 text-sm text-slate-600">Create and manage manual invoices (no web transactions).</p>

        </a>
    </div>
@endsection

