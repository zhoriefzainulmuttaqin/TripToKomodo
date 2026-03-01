<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Faq;
use App\Models\TourCategory;
use App\Models\TourPackage;
use App\Models\WebSetting;
use App\Services\LabuanBajoWeatherService;


use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $translationLocales = array_values(array_unique([$locale, $fallbackLocale]));

        try {
            $packages = TourPackage::query()

                ->where('status', 'published')
                ->with([
                    'translations' => fn ($query) => $query->whereIn('language_code', $translationLocales)->where('is_active', true),
                    'images',
                ])
                ->orderByDesc('is_featured')
                ->limit(6)
                ->get();
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
        } catch (\Throwable) {
            $destinations = collect();
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
        } catch (\Throwable) {
            $filterDestinations = collect();
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

        return view('welcome', [
            'packages' => $packages,
            'destinations' => $destinations,
            'filterCategories' => $filterCategories,
            'filterDestinations' => $filterDestinations,
            'filterDurations' => $filterDurations,
            'weather' => $weather,
            'heroBackgroundUrl' => $heroBackgroundUrl,
            'faqItems' => $faqItems,
        ]);
    }
}

