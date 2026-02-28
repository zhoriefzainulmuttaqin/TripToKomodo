<?php

namespace App\Http\Controllers;

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

        $packages = $query->paginate(9)->withQueryString();

        return view('tours.index', [
            'packages' => $packages,
            'selectedCategory' => $selectedCategory,
        ]);
    }

    /**
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(string $slug, PriceCalculator $calculator, SeoService $seo, InternalLinkService $internalLinkService)



    {
        $locale = app()->getLocale();
        $translation = TourPackageTranslation::query()
            ->where('language_code', $locale)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$translation) {
            $translation = TourPackageTranslation::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->firstOrFail();

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
                'destinations',
                'availabilities' => fn ($q) => $q->where('date', '>=', today())->orderBy('date')->limit(10),
            ])
            ->firstOrFail();

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
