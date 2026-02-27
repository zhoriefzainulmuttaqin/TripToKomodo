<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DestinationController
{
    public function index(Request $request): View
    {
        $query = DB::table('destinations');

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where('name', 'like', "%{$q}%");
        }

        if (Schema::hasColumn('destinations', 'is_active') && $request->filled('status')) {
            if ($request->string('status')->toString() === 'active') {
                $query->where('is_active', true);
            }

            if ($request->string('status')->toString() === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $destinations = $query
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.destinations.index', [
            'destinations' => $destinations,
        ]);
    }

    public function create(): View
    {
        return view('admin.destinations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'distance' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'distance' => $validated['distance'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
        ];

        if (Schema::hasColumn('destinations', 'is_active')) {
            $payload['is_active'] = (bool) ($validated['is_active'] ?? true);
        }

        $now = now();
        if (Schema::hasColumn('destinations', 'created_at')) {
            $payload['created_at'] = $now;
        }
        if (Schema::hasColumn('destinations', 'updated_at')) {
            $payload['updated_at'] = $now;
        }

        DB::table('destinations')->insert($payload);

        return redirect()->route('admin.destinations.index')->with('status', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $destination = DB::table('destinations')->where('id', $id)->first();
        abort_unless($destination, 404);

        return view('admin.destinations.edit', [
            'destination' => $destination,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $destination = DB::table('destinations')->where('id', $id)->first();
        abort_unless($destination, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'distance' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'category' => $validated['category'] ?? null,
            'distance' => $validated['distance'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
        ];

        if (Schema::hasColumn('destinations', 'is_active')) {
            $payload['is_active'] = (bool) ($validated['is_active'] ?? false);
        }

        if (Schema::hasColumn('destinations', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        DB::table('destinations')->where('id', $id)->update($payload);

        return redirect()->route('admin.destinations.index')->with('status', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        $destination = DB::table('destinations')->where('id', $id)->first();
        abort_unless($destination, 404);

        DB::table('destinations')->where('id', $id)->delete();

        return redirect()->route('admin.destinations.index')->with('status', 'Destinasi berhasil dihapus.');
    }
}
