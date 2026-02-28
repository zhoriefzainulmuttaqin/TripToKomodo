<?php

namespace App\Http\Controllers\Admin;

use App\Models\Destination;
use App\Models\Language;
use App\Models\TourCategory;

use App\Models\TourOperator;
use App\Models\TourPackage;
use App\Models\TourPackageTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TourPackageController
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();

        $query = TourPackage::query()
            ->withTrashed()
            ->with([
                'category',
                'operator',
                'primaryImage',
                'translations' => fn ($q) => $q->where('language_code', $locale),
            ])
            ->orderByDesc('updated_at');

        if ($request->filled('q')) {
            $q = trim((string) $request->string('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('code', 'like', "%{$q}%")
                    ->orWhereHas('translations', fn ($t) => $t->where('title', 'like', "%{$q}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $query->whereHas('category', fn ($c) => $c->where('slug', $slug));
        }

        if ($request->string('trashed')->toString() === 'only') {
            $query->onlyTrashed();
        } elseif ($request->string('trashed')->toString() === 'without') {
            $query->withoutTrashed();
        }

        $packages = $query->paginate(15)->withQueryString();

        $categories = TourCategory::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.tour-packages.index', [
            'packages' => $packages,
            'categories' => $categories,
        ]);
    }

    public function create(): View
    {
        return view('admin.tour-packages.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $locale = app()->getLocale();

        $validated = $request->validate([
            'tour_operator_id' => ['required', 'integer', Rule::exists('tour_operators', 'id')->whereNull('deleted_at')],
            'tour_category_id' => ['nullable', 'integer', Rule::exists('tour_categories', 'id')],
            'code' => ['required', 'string', 'max:255', 'unique:tour_packages,code'],
            'base_price_idr' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'duration_nights' => ['nullable', 'integer', 'min:0'],
            'min_people' => ['required', 'integer', 'min:1'],
            'max_people' => ['nullable', 'integer', 'min:1'],
            'difficulty' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'starts_from' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],

            'destinations' => ['nullable', 'array'],
            'destinations.*' => ['integer', Rule::exists('destinations', 'id')->whereNull('deleted_at')],

            'primary_image' => ['required', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:5120'],

            'translations' => ['required', 'array'],
            "translations.{$locale}.title" => ['required', 'string', 'max:255'],
        ]);

        $package = new TourPackage();
        $package->tour_operator_id = (int) $validated['tour_operator_id'];
        $package->tour_category_id = $validated['tour_category_id'] ?? null;
        $package->code = $validated['code'];
        $package->base_price_idr = $validated['base_price_idr'];
        $package->duration_days = (int) $validated['duration_days'];
        $package->duration_nights = (int) ($validated['duration_nights'] ?? 0);
        $package->min_people = (int) $validated['min_people'];
        $package->max_people = $validated['max_people'] !== null ? (int) $validated['max_people'] : null;
        $package->difficulty = $validated['difficulty'] ?? null;
        $package->status = $validated['status'];
        $package->starts_from = $validated['starts_from'] ?? null;
        $package->ends_at = $validated['ends_at'] ?? null;
        $package->is_featured = (bool) ($validated['is_featured'] ?? false);
        $package->save();

        $this->syncDestinations($package, $validated['destinations'] ?? []);
        $this->upsertTranslations($package, (array) ($validated['translations'] ?? []));

        $this->storeImages($package, $request);

        return redirect()->route('admin.tour-packages.edit', $package)->with('status', 'Paket trip berhasil dibuat.');
    }

    public function edit(TourPackage $tourPackage): View
    {
        $tourPackage->load([
            'translations',
            'images' => fn ($q) => $q->orderByDesc('is_primary')->orderBy('sort_order'),
            'destinations',
            'availabilities',
        ]);

        $data = $this->formData();
        $data['package'] = $tourPackage;

        return view('admin.tour-packages.edit', $data);
    }

    public function update(Request $request, TourPackage $tourPackage): RedirectResponse
    {
        $validated = $request->validate([
            'tour_operator_id' => ['required', 'integer', Rule::exists('tour_operators', 'id')->whereNull('deleted_at')],
            'tour_category_id' => ['nullable', 'integer', Rule::exists('tour_categories', 'id')],
            'code' => ['required', 'string', 'max:255', Rule::unique('tour_packages', 'code')->ignore($tourPackage->id)],
            'base_price_idr' => ['required', 'numeric', 'min:0'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'duration_nights' => ['nullable', 'integer', 'min:0'],
            'min_people' => ['required', 'integer', 'min:1'],
            'max_people' => ['nullable', 'integer', 'min:1'],
            'difficulty' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'string', Rule::in(['draft', 'published', 'archived'])],
            'starts_from' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],

            'destinations' => ['nullable', 'array'],
            'destinations.*' => ['integer', Rule::exists('destinations', 'id')->whereNull('deleted_at')],

            'primary_image' => ['nullable', 'image', 'max:5120'],
            'gallery_images' => ['nullable', 'array'],
            'gallery_images.*' => ['image', 'max:5120'],

            'translations' => ['required', 'array'],
        ]);

        $tourPackage->tour_operator_id = (int) $validated['tour_operator_id'];
        $tourPackage->tour_category_id = $validated['tour_category_id'] ?? null;
        $tourPackage->code = $validated['code'];
        $tourPackage->base_price_idr = $validated['base_price_idr'];
        $tourPackage->duration_days = (int) $validated['duration_days'];
        $tourPackage->duration_nights = (int) ($validated['duration_nights'] ?? 0);
        $tourPackage->min_people = (int) $validated['min_people'];
        $tourPackage->max_people = $validated['max_people'] !== null ? (int) $validated['max_people'] : null;
        $tourPackage->difficulty = $validated['difficulty'] ?? null;
        $tourPackage->status = $validated['status'];
        $tourPackage->starts_from = $validated['starts_from'] ?? null;
        $tourPackage->ends_at = $validated['ends_at'] ?? null;
        $tourPackage->is_featured = (bool) ($validated['is_featured'] ?? false);
        $tourPackage->save();

        $this->syncDestinations($tourPackage, $validated['destinations'] ?? []);
        $this->upsertTranslations($tourPackage, (array) ($validated['translations'] ?? []));

        $this->storeImages($tourPackage, $request);

        return redirect()->route('admin.tour-packages.edit', $tourPackage)->with('status', 'Paket trip berhasil diperbarui.');
    }

    public function destroy(TourPackage $tourPackage): RedirectResponse
    {
        $tourPackage->translations()->delete();
        $tourPackage->images()->delete();
        $tourPackage->faqs()->delete();
        $tourPackage->reviews()->delete();
        $tourPackage->availabilities()->delete();
        $tourPackage->destinations()->detach();

        $tourPackage->delete();

        return redirect()->route('admin.tour-packages.index')->with('status', 'Paket trip dipindahkan ke trash.');
    }

    public function restore(int $id): RedirectResponse
    {
        $package = TourPackage::withTrashed()->findOrFail($id);
        $package->restore();

        $package->translations()->withTrashed()->restore();
        $package->images()->withTrashed()->restore();
        $package->faqs()->withTrashed()->restore();
        $package->reviews()->withTrashed()->restore();
        $package->availabilities()->withTrashed()->restore();

        return redirect()->route('admin.tour-packages.edit', $package)->with('status', 'Paket trip berhasil direstore.');
    }

    private function formData(): array
    {
        try {
            $languages = Language::query()->where('is_active', true)->orderBy('code')->get();
        } catch (\Throwable) {
            $languages = collect();
        }

        if ($languages->isEmpty()) {
            // Fallback kalau tabel languages belum di-seed
            $languages = collect([
                (object) ['code' => 'en', 'name' => 'English'],
                (object) ['code' => 'id', 'name' => 'Indonesia'],
            ]);
        }

        $operators = TourOperator::query()->where('is_active', true)->orderBy('name')->get();
        $categories = TourCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        $destinations = Destination::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return [
            'languages' => $languages,
            'operators' => $operators,
            'categories' => $categories,
            'destinations' => $destinations,
        ];
    }

    private function syncDestinations(TourPackage $package, array $destinationIds): void
    {
        $sync = [];
        $order = 0;
        foreach ($destinationIds as $id) {
            $sync[(int) $id] = ['sort_order' => $order++];
        }

        $package->destinations()->sync($sync);
    }

    private function upsertTranslations(TourPackage $package, array $translations): void
    {
        foreach ($translations as $languageCode => $data) {
            $title = trim((string) ($data['title'] ?? ''));
            if ($title === '') {
                continue;
            }

            $existing = $package->translations()->withTrashed()->where('language_code', $languageCode)->first();
            $slugInput = trim((string) ($data['slug'] ?? ''));
            $slugBase = $slugInput !== '' ? $slugInput : Str::slug($title);
            $slug = $this->uniqueSlug($languageCode, $slugBase, $existing?->id);

            $payload = [
                'language_code' => $languageCode,
                'slug' => $slug,
                'title' => $title,
                'summary' => $data['summary'] ?? null,
                'description' => $data['description'] ?? null,
                'itinerary' => $data['itinerary'] ?? null,
                'includes' => $data['includes'] ?? null,
                'excludes' => $data['excludes'] ?? null,
                'transportation' => $data['transportation'] ?? null,
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
                $package->translations()->create($payload);
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
            $query = TourPackageTranslation::query()
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

    private function storeImages(TourPackage $package, Request $request): void
    {
        if ($request->hasFile('primary_image')) {
            $file = $request->file('primary_image');
            $path = Storage::disk('public')->putFile('tour-images', $file);
            $url = '/storage/' . ltrim($path, '/');

            // demote old primary
            $package->images()->where('is_primary', true)->update(['is_primary' => false]);

            $package->images()->create([
                'url' => $url,
                'alt_text' => null,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        if ($request->hasFile('gallery_images')) {
            $files = $request->file('gallery_images');
            $startOrder = (int) ($package->images()->max('sort_order') ?? 0);
            $order = $startOrder + 1;

            foreach ($files as $file) {
                if (!$file) {
                    continue;
                }

                $path = Storage::disk('public')->putFile('tour-images', $file);
                $url = '/storage/' . ltrim($path, '/');

                $package->images()->create([
                    'url' => $url,
                    'alt_text' => null,
                    'is_primary' => false,
                    'sort_order' => $order++,
                ]);
            }
        }

        // Ensure at least one primary image exists
        if (!$package->images()->where('is_primary', true)->exists()) {
            $first = $package->images()->orderBy('sort_order')->first();
            if ($first) {
                $first->is_primary = true;
                $first->save();
            }
        }
    }
}
