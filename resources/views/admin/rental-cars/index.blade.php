@extends('layouts.admin')

@section('title', 'Admin | Rental Mobil')

@section('content')
    @php
        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
        $adminLang = (string) request()->query('lang', (string) (session('locale') ?? config('app.locale', 'en')));
        if (!in_array($adminLang, $supportedLocales, true)) {
            $adminLang = 'en';
        }

        $totalData = $cars->total();
        $shownData = $cars->count();
        $activeData = $cars->where('is_active', true)->count();
        $trashData = $cars->filter(fn ($car) => method_exists($car, 'trashed') && $car->trashed())->count();
    @endphp

    <div class="rounded-2xl border border-slate-200 bg-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-2xl font-semibold text-slate-900">Rental Mobil</h1>
            <p class="text-sm text-slate-500">Home • <span class="font-semibold text-emerald-700">Rental Cars</span></p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-slate-200/70 p-4"><p class="text-sm text-slate-600">Total</p><p class="mt-1 text-2xl font-bold">{{ $totalData }}</p></div>
        <div class="rounded-2xl bg-lime-100/50 p-4"><p class="text-sm text-slate-600">Ditampilkan</p><p class="mt-1 text-2xl font-bold">{{ $shownData }}</p></div>
        <div class="rounded-2xl bg-emerald-100/50 p-4"><p class="text-sm text-slate-600">Aktif (halaman ini)</p><p class="mt-1 text-2xl font-bold">{{ $activeData }}</p></div>
        <div class="rounded-2xl bg-amber-100/50 p-4"><p class="text-sm text-slate-600">Trash (halaman ini)</p><p class="mt-1 text-2xl font-bold">{{ $trashData }}</p></div>
    </div>

    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-4">
        <form method="GET" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex w-full flex-col gap-3 lg:flex-row">
                <div class="relative w-full lg:max-w-sm">
                    <span class="material-symbols-outlined pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-slate-400">search</span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / transmisi / bahan bakar..." class="h-11 w-full rounded-xl border border-slate-200 pl-10 pr-4 text-sm" />
                </div>
                <select name="status" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Semua status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <select name="trashed" class="h-11 rounded-xl border border-slate-200 px-4 text-sm">
                    <option value="">Dengan trash</option>
                    <option value="without" {{ request('trashed') === 'without' ? 'selected' : '' }}>Tanpa trash</option>
                    <option value="only" {{ request('trashed') === 'only' ? 'selected' : '' }}>Hanya trash</option>
                </select>
                <button type="submit" class="h-11 rounded-xl border border-slate-200 px-4 text-sm font-semibold text-slate-700">Filter</button>
            </div>
            <a href="{{ route('admin.rental-cars.create') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-emerald-800 px-5 text-sm font-semibold text-white">Tambah Mobil</a>
        </form>
    </div>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-slate-200 text-xs uppercase tracking-[0.15em] text-slate-500">
                <tr>
                    <th class="px-5 py-4">Mobil</th>
                    <th class="px-5 py-4">Harga / Hari (IDR)</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($cars as $car)
                    @php
                        $t = ($car->translations->first() ?? null);
                        $name = $t?->name ?? '—';
                    @endphp
                    <tr class="{{ $car->trashed() ? 'bg-rose-50/40' : '' }}">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                @if (!empty($car->image))
                                    <img src="{{ $car->image }}" alt="" class="h-10 w-14 rounded-xl object-cover border border-slate-200" loading="lazy" />
                                @else
                                    <div class="h-10 w-14 rounded-xl border border-dashed border-slate-300 bg-white"></div>
                                @endif
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $name }}</div>
                                    <div class="text-xs text-slate-500">{{ $car->transmission ?? '—' }} · {{ $car->fuel ?? '—' }} · {{ $car->seats ? ($car->seats . ' seats') : '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">Rp {{ number_format((int) $car->price_per_day_idr, 0, ',', '.') }}</td>
                        <td class="px-5 py-4">
                            @if ($car->is_active)
                                <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Aktif</span>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Nonaktif</span>
                            @endif
                            @if ($car->trashed())
                                <span class="ml-2 inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Trash</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap items-center justify-end gap-2">
                                @if ($car->trashed())
                                    <form method="POST" action="{{ route('admin.rental-cars.restore', $car->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.rental-cars.edit', $car) }}" class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">Edit</a>
                                    <form method="POST" action="{{ route('admin.rental-cars.destroy', $car) }}" onsubmit="return confirm('Hapus mobil ini ke trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Trash</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-slate-500">Belum ada mobil rental.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $cars->links() }}</div>

    <div class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50 p-5 text-sm text-emerald-900">
        <div class="font-semibold">Pengaturan halaman Rental</div>
        <div class="mt-1 text-emerald-800">SEO / Hero / CTA untuk halaman rental tetap bisa diatur di halaman CMS.</div>
        <a href="{{ route('admin.rental.edit', ['lang' => $adminLang]) }}" class="mt-3 inline-flex rounded-xl bg-emerald-700 px-4 py-2 text-xs font-semibold text-white">Buka Pengaturan Halaman</a>
    </div>
@endsection
