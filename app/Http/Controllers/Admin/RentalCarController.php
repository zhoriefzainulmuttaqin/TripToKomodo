<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use App\Models\RentalCar;
use App\Models\RentalCarTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RentalCarController
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();

        $query = RentalCar::query()
            ->withTrashed()
            ->with([
                'translations' => fn ($q) => $q->where('language_code', $locale),
            ])
            ->orderByDesc('updated_at');

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where(function ($sub) use ($q) {
                $sub->whereHas('translations', fn ($t) => $t->where('name', 'like', "%{$q}%"))
                    ->orWhere('transmission', 'like', "%{$q}%")
                    ->orWhere('fuel', 'like', "%{$q}%");
            });
        }

        if ($request->string('trashed')->toString() === 'only') {
            $query->onlyTrashed();
        } elseif ($request->string('trashed')->toString() === 'without') {
            $query->withoutTrashed();
        }

        if ($request->filled('status')) {
            if ($request->string('status')->toString() === 'active') {
                $query->where('is_active', true);
            }
            if ($request->string('status')->toString() === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $cars = $query->paginate(15)->withQueryString();

        return view('admin.rental-cars.index', [
            'cars' => $cars,
        ]);
    }

    public function create(): View
    {
        return view('admin.rental-cars.create', [
            'languages' => $this->availableLanguages(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:99'],
            'luggage' => ['nullable', 'integer', 'min:0', 'max:99'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'fuel' => ['nullable', 'string', 'max:50'],
            'price_per_day_idr' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],

            'translations' => ['required', 'array'],
        ]);

        $translations = (array) ($validated['translations'] ?? []);
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $fallbackName = trim((string) ($translations[$fallbackLocale]['name'] ?? ''));
        if ($fallbackName === '') {
            throw ValidationException::withMessages([
                "translations.{$fallbackLocale}.name" => 'Nama mobil wajib diisi untuk bahasa utama.',
            ]);
        }

        $car = new RentalCar();
        $car->is_active = (bool) ($validated['is_active'] ?? true);
        $car->seats = $validated['seats'] ?? null;
        $car->luggage = $validated['luggage'] ?? null;
        $car->transmission = $validated['transmission'] ?? null;
        $car->fuel = $validated['fuel'] ?? null;
        $car->price_per_day_idr = (int) round((float) $validated['price_per_day_idr']);

        if ($request->hasFile('image')) {
            $path = Storage::disk('public')->putFile('rental-cars', $request->file('image'));
            $car->image = '/storage/' . ltrim($path, '/');
        }

        $car->save();

        $this->upsertTranslations($car, $translations);

        return redirect()->route('admin.rental-cars.edit', $car)->with('status', 'Rental mobil berhasil dibuat.');
    }

    public function edit(RentalCar $rentalCar): View
    {
        $rentalCar->load(['translations']);

        return view('admin.rental-cars.edit', [
            'car' => $rentalCar,
            'languages' => $this->availableLanguages(),
        ]);
    }

    public function update(Request $request, RentalCar $rentalCar): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'seats' => ['nullable', 'integer', 'min:1', 'max:99'],
            'luggage' => ['nullable', 'integer', 'min:0', 'max:99'],
            'transmission' => ['nullable', 'string', 'max:50'],
            'fuel' => ['nullable', 'string', 'max:50'],
            'price_per_day_idr' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],

            'translations' => ['required', 'array'],
        ]);

        $translations = (array) ($validated['translations'] ?? []);
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $fallbackName = trim((string) ($translations[$fallbackLocale]['name'] ?? ''));
        if ($fallbackName === '') {
            throw ValidationException::withMessages([
                "translations.{$fallbackLocale}.name" => 'Nama mobil wajib diisi untuk bahasa utama.',
            ]);
        }

        $rentalCar->is_active = (bool) ($validated['is_active'] ?? true);
        $rentalCar->seats = $validated['seats'] ?? null;
        $rentalCar->luggage = $validated['luggage'] ?? null;
        $rentalCar->transmission = $validated['transmission'] ?? null;
        $rentalCar->fuel = $validated['fuel'] ?? null;
        $rentalCar->price_per_day_idr = (int) round((float) $validated['price_per_day_idr']);

        if (($validated['remove_image'] ?? false) && !empty($rentalCar->image)) {
            $this->deletePublicUrlFile($rentalCar->image);
            $rentalCar->image = null;
        }

        if ($request->hasFile('image')) {
            if (!empty($rentalCar->image)) {
                $this->deletePublicUrlFile($rentalCar->image);
            }

            $path = Storage::disk('public')->putFile('rental-cars', $request->file('image'));
            $rentalCar->image = '/storage/' . ltrim($path, '/');
        }

        $rentalCar->save();

        $this->upsertTranslations($rentalCar, $translations);

        return redirect()->route('admin.rental-cars.edit', $rentalCar)->with('status', 'Rental mobil berhasil diperbarui.');
    }

    public function destroy(RentalCar $rentalCar): RedirectResponse
    {
        $rentalCar->translations()->delete();
        $rentalCar->delete();

        return redirect()->route('admin.rental-cars.index')->with('status', 'Rental mobil dipindahkan ke trash.');
    }

    public function restore(int $id): RedirectResponse
    {
        $car = RentalCar::withTrashed()->findOrFail($id);
        $car->restore();
        $car->translations()->withTrashed()->restore();

        return redirect()->route('admin.rental-cars.edit', $car)->with('status', 'Rental mobil berhasil direstore.');
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
                (object) ['code' => 'en', 'name' => 'English'],
                (object) ['code' => 'id', 'name' => 'Indonesia'],
            ]);
        }

        return $languages;
    }

    private function upsertTranslations(RentalCar $car, array $translations): void
    {
        foreach ($translations as $languageCode => $data) {
            $name = trim((string) ($data['name'] ?? ''));
            if ($name === '') {
                continue;
            }

            $existing = $car->translations()->withTrashed()->where('language_code', $languageCode)->first();

            $slugInput = trim((string) ($data['slug'] ?? ''));
            $slugBase = $slugInput !== '' ? $slugInput : Str::slug($name);
            $slug = $this->uniqueSlug($languageCode, $slugBase, $existing?->id);

            $payload = [
                'language_code' => $languageCode,
                'slug' => $slug,
                'name' => $name,
                'excerpt' => $data['excerpt'] ?? null,
                'description' => $data['description'] ?? null,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
                'is_active' => (bool) ($data['is_active'] ?? true),
            ];

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                }
                $existing->fill($payload);
                $existing->save();
            } else {
                $car->translations()->create($payload);
            }
        }
    }

    private function uniqueSlug(string $languageCode, string $slugBase, ?int $ignoreId = null): string
    {
        $slugBase = trim($slugBase);
        $slugBase = $slugBase !== '' ? Str::slug($slugBase) : Str::random(8);

        $slug = $slugBase;
        $i = 2;

        while (true) {
            $query = RentalCarTranslation::query()
                ->where('language_code', $languageCode)
                ->where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }

            if (!$query->exists()) {
                return $slug;
            }

            $slug = $slugBase . '-' . $i;
            $i++;
        }
    }

    private function deletePublicUrlFile(string $publicUrl): void
    {
        $publicUrl = trim($publicUrl);
        if ($publicUrl === '') {
            return;
        }

        if (str_starts_with($publicUrl, '/storage/')) {
            $path = ltrim(substr($publicUrl, strlen('/storage/')), '/');
            Storage::disk('public')->delete($path);
        }
    }
}
