<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\TourPackage;
use App\Models\TourPackageTranslation;
use App\Services\InternalLinkService;
use App\Services\PriceCalculator;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;



class TourController extends Controller
{
    public function index(Request $request): View
    {
        $locale = app()->getLocale();
        $selectedCategory = $request->string('category')->toString();
        $selectedDuration = $request->string('duration')->toString(); // format: "{days}-{nights}", contoh "3-2"

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



        $translation = TourPackageTranslation::query()
            ->where('language_code', $locale)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();



        if (!$translation) {
            $translation = TourPackageTranslation::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if (!$translation) {
                abort(404);
            }


            if ($translation->language_code !== $locale) {
                return redirect()->route('tours.show', [
                    'lang' => $translation->language_code,
                    'slug' => $translation->slug,
                ]);
            }
        }

        $package = TourPackage::query()
            ->whereKey($translation->tour_package_id)
            ->with([
                'images',
                'faqs',
                'reviews',
                'translations',
                'operator',
                'category',
                'destinations.translations' => fn ($q) => $q->whereIn('language_code', [$locale, (string) config('app.fallback_locale', 'en')]),

                'availabilities' => fn ($q) => $q->where('date', '>=', today())->orderBy('date')->limit(10),
            ])
            ->first();

        if (!$package) {
            abort(404);
        }


        $pricing = $calculator->calculateSellingPrice($package, null, session('currency', 'IDR'));
        $description = $internalLinkService->inject($translation->description ?? '', $locale);

        return view('tours.show', [
            'package' => $package,
            'translation' => $translation,
            'descriptionHtml' => $description,
            'pricing' => $pricing,
            'seo' => $seo,
        ]);
    }
}
