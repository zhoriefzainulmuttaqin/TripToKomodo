<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use App\Models\DestinationTranslation;
use App\Models\Language;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DestinationController
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        $query = DB::table('destinations as d')->whereNull('d.deleted_at');

        if (Schema::hasTable('destination_translations')) {
            $query
                ->leftJoin('destination_translations as t_locale', function ($join) use ($locale) {
                    $join->on('t_locale.destination_id', '=', 'd.id')
                        ->where('t_locale.language_code', '=', $locale);
                })
                ->leftJoin('destination_translations as t_fallback', function ($join) use ($fallbackLocale) {
                    $join->on('t_fallback.destination_id', '=', 'd.id')
                        ->where('t_fallback.language_code', '=', $fallbackLocale);
                });
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where(function ($builder) use ($q) {
                $builder->where('d.name', 'like', "%{$q}%");

                if (Schema::hasTable('destination_translations')) {
                    $builder
                        ->orWhere('t_locale.name', 'like', "%{$q}%")
                        ->orWhere('t_fallback.name', 'like', "%{$q}%");
                }
            });
        }

        if (Schema::hasColumn('destinations', 'is_active') && $request->filled('status')) {
            if ($request->string('status')->toString() === 'active') {
                $query->where('d.is_active', true);
            }

            if ($request->string('status')->toString() === 'inactive') {
                $query->where('d.is_active', false);
            }
        }

        $select = ['d.*'];
        if (Schema::hasTable('destination_translations')) {
            $select[] = DB::raw('COALESCE(t_locale.name, t_fallback.name, d.name) as display_name');
            $select[] = DB::raw('COALESCE(t_locale.description, t_fallback.description, d.description) as display_description');
            $select[] = DB::raw('COALESCE(t_locale.category, t_fallback.category, d.category) as display_category');
            $select[] = DB::raw('COALESCE(t_locale.distance, t_fallback.distance, d.distance) as display_distance');
        }

        $destinations = $query
            ->select($select)
            ->orderByDesc('d.id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.destinations.index', [
            'destinations' => $destinations,
        ]);
    }

    public function create(): View
    {
        return view('admin.destinations.create', [
            'languages' => $this->availableLanguages(),
            'translations' => [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'translations' => ['nullable', 'array'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string', 'max:5000'],
            'translations.*.category' => ['nullable', 'string', 'max:255'],
            'translations.*.distance' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $translations = $validated['translations'] ?? [];
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $fallbackName = trim((string) ($translations[$fallbackLocale]['name'] ?? ''));

        if ($fallbackName === '') {
            throw ValidationException::withMessages([
                "translations.{$fallbackLocale}.name" => 'Nama destinasi wajib diisi untuk bahasa utama.',
            ]);
        }

        $payload = [
            'name' => $fallbackName,
            'category' => $translations[$fallbackLocale]['category'] ?? null,
            'distance' => $translations[$fallbackLocale]['distance'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
        ];

        if (Schema::hasColumn('destinations', 'description')) {
            $payload['description'] = $translations[$fallbackLocale]['description'] ?? null;
        }

        if ($request->hasFile('image') && Schema::hasColumn('destinations', 'image')) {
            $path = $request->file('image')->store('destinations', 'public');
            $payload['image'] = $path;
        }

        if (Schema::hasColumn('destinations', 'is_active')) {
            $payload['is_active'] = (bool) ($validated['is_active'] ?? true);
        }

        $destination = Destination::query()->create($payload);

        if (Schema::hasTable('destination_translations')) {
            $this->upsertTranslations($destination, $translations);
        }

        return redirect()->route('admin.destinations.index')->with('status', 'Destinasi berhasil ditambahkan.');
    }

    public function edit(int $id): View
    {
        $destination = Destination::query()->whereKey($id)->whereNull('deleted_at')->firstOrFail();
        $translations = [];

        if (Schema::hasTable('destination_translations')) {
            $translations = DestinationTranslation::query()
                ->where('destination_id', $destination->id)
                ->get()
                ->mapWithKeys(fn ($translation) => [
                    $translation->language_code => [
                        'name' => $translation->name,
                        'description' => $translation->description,
                        'category' => $translation->category,
                        'distance' => $translation->distance,
                    ],
                ])
                ->toArray();
        }

        return view('admin.destinations.edit', [
            'destination' => $destination,
            'languages' => $this->availableLanguages(),
            'translations' => $translations,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $destination = Destination::query()->whereKey($id)->whereNull('deleted_at')->firstOrFail();

        $validated = $request->validate([
            'translations' => ['nullable', 'array'],
            'translations.*.name' => ['nullable', 'string', 'max:255'],
            'translations.*.description' => ['nullable', 'string', 'max:5000'],
            'translations.*.category' => ['nullable', 'string', 'max:255'],
            'translations.*.distance' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'lat' => ['nullable', 'numeric', 'between:-90,90'],
            'lng' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $translations = $validated['translations'] ?? [];
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $fallbackName = trim((string) ($translations[$fallbackLocale]['name'] ?? ''));

        if ($fallbackName === '') {
            throw ValidationException::withMessages([
                "translations.{$fallbackLocale}.name" => 'Nama destinasi wajib diisi untuk bahasa utama.',
            ]);
        }

        $payload = [
            'name' => $fallbackName,
            'category' => $translations[$fallbackLocale]['category'] ?? null,
            'distance' => $translations[$fallbackLocale]['distance'] ?? null,
            'lat' => $validated['lat'] ?? null,
            'lng' => $validated['lng'] ?? null,
        ];

        if (Schema::hasColumn('destinations', 'description')) {
            $payload['description'] = $translations[$fallbackLocale]['description'] ?? null;
        }

        if (Schema::hasColumn('destinations', 'image')) {
            if (($validated['remove_image'] ?? false) && !empty($destination->image)) {
                Storage::disk('public')->delete($destination->image);
                $payload['image'] = null;
            }

            if ($request->hasFile('image')) {
                if (!empty($destination->image)) {
                    Storage::disk('public')->delete($destination->image);
                }
                $payload['image'] = $request->file('image')->store('destinations', 'public');
            }
        }

        if (Schema::hasColumn('destinations', 'is_active')) {
            $payload['is_active'] = (bool) ($validated['is_active'] ?? false);
        }

        $destination->fill($payload);
        $destination->save();

        if (Schema::hasTable('destination_translations')) {
            $this->upsertTranslations($destination, $translations);
        }

        return redirect()->route('admin.destinations.index')->with('status', 'Destinasi berhasil diperbarui.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Destination::query()->whereKey($id)->whereNull('deleted_at')->firstOrFail();

        $payload = ['deleted_at' => now()];

        if (Schema::hasColumn('destinations', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        Destination::query()->whereKey($id)->update($payload);

        return redirect()->route('admin.destinations.index')->with('status', 'Destinasi berhasil dihapus.');
    }

    private function availableLanguages()
    {
        try {
            $languages = Language::query()->where('is_active', true)->orderBy('code')->get();
        } catch (\Throwable) {
            $languages = collect();
        }

        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['code' => 'id', 'name' => 'Indonesia', 'native_name' => 'Bahasa Indonesia'],
                (object) ['code' => 'en', 'name' => 'English', 'native_name' => 'English'],
            ]);
        }

        return $languages;
    }

    private function upsertTranslations(Destination $destination, array $translations): void
    {
        foreach ($translations as $languageCode => $data) {
            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            DestinationTranslation::query()->updateOrCreate(
                [
                    'destination_id' => $destination->id,
                    'language_code' => $languageCode,
                ],
                [
                    'name' => $name,
                    'description' => $data['description'] ?? null,
                    'category' => $data['category'] ?? null,
                    'distance' => $data['distance'] ?? null,
                ]
            );
        }
    }
}
