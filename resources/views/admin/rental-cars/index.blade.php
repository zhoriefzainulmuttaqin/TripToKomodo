@extends('layouts.admin')

@section('title', 'Admin | Rental Mobil')

@section('content')
    @php
        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
        $adminLang = (string) request()->query('lang', (string) (session('locale') ?? config('app.locale', 'en')));
        if (!in_array($adminLang, $supportedLocales, true)) {
            $adminLang = 'en';
        }
    @endphp

    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Rental Mobil</h1>
            <p class="mt-1 text-sm text-slate-600">Kelola daftar mobil rental (multi bahasa + harga).</p>
        </div>
        <a href="{{ route('admin.rental-cars.create') }}" class="inline-flex rounded-full bg-emerald-600 px-5 py-2 text-sm font-semibold text-white">Tambah Mobil</a>
    </div>

    <form method="GET" class="mt-6 grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 md:grid-cols-[1fr_auto_auto_auto]">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / transmisi / bahan bakar..." class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" />

        <select name="status" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
            <option value="">Semua status</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
        </select>

        <select name="trashed" class="w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
            <option value="">Dengan trash</option>
            <option value="without" {{ request('trashed') === 'without' ? 'selected' : '' }}>Tanpa trash</option>
            <option value="only" {{ request('trashed') === 'only' ? 'selected' : '' }}>Hanya trash</option>
        </select>

        <button type="submit" class="rounded-2xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white">Filter</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-600">
                <tr>
                    <th class="px-5 py-4">Mobil</th>
                    <th class="px-5 py-4">Harga / Hari (IDR)</th>
                    <th class="px-5 py-4">Status</th>
                    <th class="px-5 py-4">Aksi</th>
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
                                <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">Nonaktif</span>
                            @endif
                            @if ($car->trashed())
                                <span class="ml-2 inline-flex rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Trash</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-2">
                                @if ($car->trashed())
                                    <form method="POST" action="{{ route('admin.rental-cars.restore', $car->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-xs font-semibold text-emerald-800">Restore</button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.rental-cars.edit', $car) }}" class="rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-800">Edit</a>
                                    <form method="POST" action="{{ route('admin.rental-cars.destroy', $car) }}" onsubmit="return confirm('Hapus mobil ini ke trash?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-700">Trash</button>
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

    <div class="mt-6">
        {{ $cars->links() }}
    </div>

    <div class="mt-8 rounded-3xl border border-emerald-100 bg-emerald-50 p-5 text-sm text-emerald-900">
        <div class="font-semibold">Pengaturan halaman Rental</div>
        <div class="mt-1 text-emerald-800">SEO / Hero / CTA untuk halaman rental tetap bisa diatur di halaman CMS.</div>
        <a href="{{ route('admin.rental.edit', ['lang' => $adminLang]) }}" class="mt-3 inline-flex rounded-full bg-emerald-600 px-4 py-2 text-xs font-semibold text-white">Buka Pengaturan Halaman</a>
    </div>
@endsection
