<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use App\Models\TourPackageTranslation;
use App\Services\InternalLinkService;
use App\Services\PriceCalculator;
use App\Services\SeoService;
use Illuminate\View\View;


class TourController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();
        $packages = TourPackage::query()
            ->where('status', 'published')
            ->with([
                'translations' => fn ($query) => $query->where('language_code', $locale),
                'images',
            ])
            ->paginate(9);

        return view('tours.index', [
            'packages' => $packages,
        ]);
    }

    public function show(string $slug, PriceCalculator $calculator, SeoService $seo, InternalLinkService $internalLinkService): View

    {
        $locale = app()->getLocale();
        $translation = TourPackageTranslation::query()
            ->where('language_code', $locale)
            ->where('slug', $slug)
            ->firstOrFail();

        $package = TourPackage::query()
            ->whereKey($translation->tour_package_id)
            ->with(['images', 'faqs', 'reviews', 'translations'])
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
