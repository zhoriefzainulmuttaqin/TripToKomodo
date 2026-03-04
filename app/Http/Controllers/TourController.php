<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Faq;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\TourPackageTranslation;
use App\Services\InternalLinkService;
use App\Services\PriceCalculator;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;



class TourController extends Controller
{
    public function index(Request $request, PriceCalculator $calculator): View
    {
        $locale = app()->getLocale();
        $selectedCategory = $request->string('category')->toString();
        $selectedDuration = $request->string('duration')->toString(); // format: "{days}-{nights}", contoh "3-2"

        $selectedCategoryName = null;
        if ($selectedCategory !== '') {
            $selectedCategoryName = TourCategory::query()
                ->where('slug', $selectedCategory)
                ->where('is_active', true)
                ->value('name');
        }

        // Support both 'destination' (single) and 'destinations' (array) parameters
        $selectedDestinations = [];
        if ($request->has('destination')) {
            $destId = (int) $request->input('destination');
            if ($destId > 0) {
                $selectedDestinations = [$destId];
            }
        } else {
            $selectedDestinations = $request->input('destinations', []);
            if (!is_array($selectedDestinations)) {
                $selectedDestinations = [$selectedDestinations];
            }
            $selectedDestinations = array_values(array_filter(array_map('intval', $selectedDestinations), fn ($v) => $v > 0));
        }

        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $translationLocales = array_values(array_unique([$locale, $fallbackLocale]));

        $query = TourPackage::query()
            ->where('status', 'published')
            ->with([
                'category',
                'translations' => fn ($q) => $q->whereIn('language_code', $translationLocales)->where('is_active', true),
                'images',
            ]);

        if ($selectedCategory !== '') {
            $query->whereHas('category', fn ($q) => $q->where('slug', $selectedCategory)->where('is_active', true));
        }

        if ($selectedDuration !== '') {
            $parts = explode('-', $selectedDuration);
            $days = isset($parts[0]) ? (int) $parts[0] : 0;
            $nights = isset($parts[1]) ? (int) $parts[1] : 0;

            if ($days > 0) {
                $query->where('duration_days', $days);
            }
            if ($nights >= 0 && $days > 0) {
                $query->where('duration_nights', $nights);
            }
        }

        if (!empty($selectedDestinations)) {
            $query->whereHas('destinations', fn ($q) => $q->whereIn('destinations.id', $selectedDestinations));
        }

        $packages = $query->paginate(9)->withQueryString();

        $currencyCode = session('currency', 'IDR');
        $packages->getCollection()->transform(function (TourPackage $package) use ($calculator, $currencyCode) {
            $package->pricing = $calculator->calculateSellingPrice($package, null, $currencyCode);

            return $package;
        });

        $selectedDestinationNames = [];
        if (!empty($selectedDestinations)) {
            try {
                $selectedDestinationNames = Destination::query()
                    ->whereIn('id', $selectedDestinations)
                    ->with(['translations' => fn ($q) => $q->whereIn('language_code', $translationLocales)])
                    ->get()
                    ->map(function ($destination) use ($locale, $fallbackLocale) {
                        $translation = $destination->translationFor($locale, $fallbackLocale);

                        return $translation?->name ?? $destination->name;
                    })
                    ->toArray();
            } catch (\Throwable) {
                $selectedDestinationNames = [];
            }
        }


        return view('tours.index', [
            'packages' => $packages,
            'selectedCategory' => $selectedCategory,
            'selectedCategoryName' => $selectedCategoryName,
            'selectedDuration' => $selectedDuration,
            'selectedDestinations' => $selectedDestinations,
            'selectedDestinationNames' => $selectedDestinationNames,
        ]);
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(string $lang, string $slug, PriceCalculator $calculator, SeoService $seo, InternalLinkService $internalLinkService)
    {
        $locale = app()->getLocale();
        $lang = strtolower($lang);

        // Pastikan locale sinkron dengan prefix bahasa di URL.
        if ($lang !== $locale) {
            app()->setLocale($lang);
            $locale = $lang;
        }

        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $translationLocales = array_values(array_unique([$locale, $fallbackLocale]));

        $translation = TourPackageTranslation::query()
            ->where('language_code', $locale)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$translation) {
            // Slug yang diketik/di-klik mungkin berasal dari bahasa lain.
            // Cari dulu translasi apapun berdasarkan slug tersebut, lalu coba map ke translasi locale aktif.
            $anyTranslation = TourPackageTranslation::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$anyTranslation) {
                abort(404);
            }

            $targetTranslation = TourPackageTranslation::query()
                ->where('tour_package_id', $anyTranslation->tour_package_id)
                ->where('language_code', $locale)
                ->where('is_active', true)
                ->first();

            // Jika ada translasi sesuai locale aktif, arahkan ke slug yang benar tanpa mengganti bahasa.
            if ($targetTranslation && !empty($targetTranslation->slug) && $targetTranslation->slug !== $slug) {
                return redirect()->route('tours.show', [
                    'lang' => $locale,
                    'slug' => $targetTranslation->slug,
                ], 301);
            }

            // Kalau tidak ada translasi di locale aktif, fallback ke bahasa dari slug yang ditemukan.
            if ($anyTranslation->language_code !== $locale) {
                return redirect()->route('tours.show', [
                    'lang' => $anyTranslation->language_code,
                    'slug' => $anyTranslation->slug,
                ], 301);
            }

            $translation = $anyTranslation;
        }

        $package = TourPackage::query()
            ->whereKey($translation->tour_package_id)
            ->with([
                'images',
                'faqs' => fn ($q) => $q->whereIn('language_code', $translationLocales)->orderBy('sort_order'),
                'reviews',
                'translations',
                'operator',
                'category',
                'destinations.translations' => fn ($q) => $q->whereIn('language_code', $translationLocales),

                'availabilities' => fn ($q) => $q->where('date', '>=', today())->orderBy('date')->limit(10),
            ])
            ->first();

        if (!$package) {
            abort(404);
        }


        $pricing = $calculator->calculateSellingPrice($package, null, session('currency', 'IDR'));
        $description = $internalLinkService->inject($translation->description ?? '', $locale);

        // FAQ: utamakan FAQ khusus paket (tour_faqs) sesuai bahasa, fallback ke bahasa default.
        $packageFaqItems = $package->faqs->where('language_code', $locale);
        if ($packageFaqItems->isEmpty()) {
            $packageFaqItems = $package->faqs->where('language_code', $fallbackLocale);
        }
        $packageFaqItems = $packageFaqItems->sortBy('sort_order')->values();

        // Jika paket belum punya FAQ, fallback ke FAQ global (admin -> FAQs).
        $globalFaqItems = Faq::query()
            ->where('is_active', true)
            ->where('language_code', $locale)
            ->orderBy('sort_order')
            ->get();

        if ($globalFaqItems->isEmpty() && $fallbackLocale !== $locale) {
            $globalFaqItems = Faq::query()
                ->where('is_active', true)
                ->where('language_code', $fallbackLocale)
                ->orderBy('sort_order')
                ->get();
        }

        return view('tours.show', [
            'package' => $package,
            'translation' => $translation,
            'descriptionHtml' => $description,
            'pricing' => $pricing,
            'seo' => $seo,
            'packageFaqItems' => $packageFaqItems,
            'globalFaqItems' => $globalFaqItems,
        ]);
    }
}
