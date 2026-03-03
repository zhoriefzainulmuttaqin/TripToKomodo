<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Destination;
use App\Models\Faq;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\WebSetting;
use App\Services\LabuanBajoWeatherService;
use App\Services\PriceCalculator;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(PriceCalculator $calculator): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $translationLocales = array_values(array_unique([$locale, $fallbackLocale]));

        try {
            $packages = TourPackage::query()

                ->where('status', 'published')
                ->with([
                    'category',
                    'translations' => fn ($query) => $query->whereIn('language_code', $translationLocales)->where('is_active', true),
                    'images',
                ])
                ->orderByDesc('is_featured')
                ->limit(6)
                ->get();

            $currencyCode = session('currency', 'IDR');
            $packages->transform(function ($package) use ($calculator, $currencyCode) {
                $package->pricing = $calculator->calculateSellingPrice($package, null, $currencyCode);

                return $package;
            });
        } catch (\Throwable) {
            $packages = collect();
        }

        // Untuk map (butuh lat/lng, image, description)
        try {
            $destinations = Destination::query()
                ->whereNull('deleted_at')
                ->with(['translations' => fn ($q) => $q->whereIn('language_code', $translationLocales)])
                ->orderBy('name')
                ->get()
                ->map(function ($destination) use ($locale, $fallbackLocale) {
                    $translation = $destination->translationFor($locale, $fallbackLocale);

                    return (object) [
                        'name' => $translation?->name ?? $destination->name,
                        'image' => $destination->image,
                        'description' => $translation?->description ?? $destination->description,
                        'category' => $translation?->category ?? $destination->category,
                        'distance' => $translation?->distance ?? $destination->distance,
                        'lat' => $destination->lat,
                        'lng' => $destination->lng,
                    ];
                });
        } catch (\Throwable $e) {
            // Jangan "menelan" error: log supaya cepat ketahuan kalau schema/relasi belum ada.
            report($e);

            // Fallback: tetap tampilkan map marker dari field dasar (tanpa translasi)
            try {
                $destinations = Destination::query()
                    ->orderBy('name')
                    ->get()
                    ->map(fn ($destination) => (object) [
                        'name' => $destination->name,
                        'image' => $destination->image,
                        'description' => $destination->description,
                        'category' => $destination->category,
                        'distance' => $destination->distance,
                        'lat' => $destination->lat,
                        'lng' => $destination->lng,
                    ]);
            } catch (\Throwable $e2) {
                report($e2);
                $destinations = collect();
            }
        }



        // Untuk Trip Finder form
        try {
            $filterCategories = TourCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']);
        } catch (\Throwable) {
            $filterCategories = collect();
        }

        try {
            $filterDestinations = Destination::query()
                ->where('is_active', true)
                ->whereNull('deleted_at')
                ->with(['translations' => fn ($q) => $q->whereIn('language_code', $translationLocales)])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(function ($destination) use ($locale, $fallbackLocale) {
                    $translation = $destination->translationFor($locale, $fallbackLocale);

                    return (object) [
                        'id' => $destination->id,
                        'name' => $translation?->name ?? $destination->name,
                    ];
                });
        } catch (\Throwable $e) {
            report($e);

            // Fallback tanpa translasi/soft delete (supaya Trip Finder tetap terisi)
            try {
                $filterDestinations = Destination::query()
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn ($destination) => (object) [
                        'id' => $destination->id,
                        'name' => $destination->name,
                    ]);
            } catch (\Throwable $e2) {
                report($e2);
                $filterDestinations = collect();
            }
        }



        try {
            $filterDurations = TourPackage::query()
                ->where('status', 'published')
                ->select(['duration_days', 'duration_nights'])
                ->distinct()
                ->orderBy('duration_days')
                ->orderBy('duration_nights')
                ->get();
        } catch (\Throwable) {
            $filterDurations = collect();
        }

        try {
            $weather = app(LabuanBajoWeatherService::class)->get(false);
        } catch (\Throwable) {
            $weather = null;
        }

        $heroBackgroundUrl = null;
        try {
            $heroBackgroundPath = WebSetting::get(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE);
            if (!empty($heroBackgroundPath)) {
                // Pakai URL relatif supaya tidak tergantung APP_URL (mis. localhost vs triptokomodo.test)
                $heroBackgroundUrl = '/storage/' . ltrim((string) $heroBackgroundPath, '/');
            }
        } catch (\Throwable) {
            $heroBackgroundUrl = null;
        }

        $faqItems = collect();
        try {
            $fallbackLocale = (string) config('app.fallback_locale', 'en');
            $faqs = Faq::query()
                ->where('is_active', true)
                ->where('language_code', $locale)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            if ($faqs->isEmpty() && $fallbackLocale !== $locale) {
                $faqs = Faq::query()
                    ->where('is_active', true)
                    ->where('language_code', $fallbackLocale)
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            }

            $faqItems = $faqs->map(fn ($faq) => [
                'q' => $faq->question,
                'a' => $faq->answer,
            ]);
        } catch (\Throwable) {
            $faqItems = collect();
        }

        $blogPosts = collect();
        try {
            $blogPosts = BlogPost::query()
                ->published()
                ->where('language_code', $locale)
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get();

            if ($blogPosts->isEmpty() && $fallbackLocale !== $locale) {
                $blogPosts = BlogPost::query()
                    ->published()
                    ->where('language_code', $fallbackLocale)
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->limit(3)
                    ->get();
            }
        } catch (\Throwable) {
            $blogPosts = collect();
        }

        return view('home', [

            'packages' => $packages,
            'destinations' => $destinations,
            'filterCategories' => $filterCategories,
            'filterDestinations' => $filterDestinations,
            'filterDurations' => $filterDurations,
            'weather' => $weather,
            'heroBackgroundUrl' => $heroBackgroundUrl,
            'faqItems' => $faqItems,
            'blogPosts' => $blogPosts,
        ]);
    }
}

