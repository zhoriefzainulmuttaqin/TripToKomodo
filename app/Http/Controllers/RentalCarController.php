<?php

namespace App\Http\Controllers;

use App\Models\RentalCar;
use App\Models\RentalCarTranslation;
use App\Services\RentalPriceCalculator;
use Illuminate\View\View;

class RentalCarController extends Controller
{
    public function index(RentalPriceCalculator $priceCalculator): View
    {
        $locale = app()->getLocale();
        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $currencyCode = session('currency', 'IDR');

        $cars = RentalCar::query()
            ->where('is_active', true)
            ->withoutTrashed()
            ->with([
                'translations' => fn ($q) => $q
                    ->whereIn('language_code', array_values(array_unique([$locale, $fallbackLocale])))
                    ->where('is_active', true),
            ])
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (RentalCar $car) use ($locale, $fallbackLocale, $priceCalculator, $currencyCode) {
                $translation = $car->translationFor($locale, $fallbackLocale);
                $car->display_translation = $translation;
                $car->pricing = $priceCalculator->calculatePricePerDay($car, $currencyCode);

                return $car;
            })
            ->filter(fn (RentalCar $car) => !empty($car->display_translation))
            ->values();

        return view('pages.rental-mobil', [
            'cars' => $cars,
        ]);
    }

    public function show(string $lang, string $slug, RentalPriceCalculator $priceCalculator)
    {
        $locale = app()->getLocale();
        $lang = strtolower($lang);

        // Pastikan locale sinkron dengan prefix bahasa di URL.
        if ($lang !== $locale) {
            app()->setLocale($lang);
            $locale = $lang;
        }

        $fallbackLocale = (string) config('app.fallback_locale', 'en');
        $currencyCode = session('currency', 'IDR');

        $currentTranslation = RentalCarTranslation::query()
            ->where('language_code', $locale)
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$currentTranslation) {
            // Try to find by slug in any language, then redirect to the proper translated slug (if exists).
            $anyTranslation = RentalCarTranslation::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->first();

            if ($anyTranslation) {
                $target = RentalCarTranslation::query()
                    ->where('rental_car_id', $anyTranslation->rental_car_id)
                    ->where('language_code', $locale)
                    ->where('is_active', true)
                    ->first()
                    ?: RentalCarTranslation::query()
                        ->where('rental_car_id', $anyTranslation->rental_car_id)
                        ->where('language_code', $fallbackLocale)
                        ->where('is_active', true)
                        ->first();

                if ($target) {
                    return redirect()->route('rental.mobil.show', [
                        'lang' => $locale,
                        'slug' => $target->slug,
                    ]);
                }
            }

            abort(404);
        }

        $car = RentalCar::query()
            ->whereKey($currentTranslation->rental_car_id)
            ->where('is_active', true)
            ->withoutTrashed()
            ->with([
                'translations' => fn ($q) => $q
                    ->whereIn('language_code', array_values(array_unique([$locale, $fallbackLocale])))
                    ->where('is_active', true),
            ])
            ->firstOrFail();

        $translation = $car->translationFor($locale, $fallbackLocale);
        $pricing = $priceCalculator->calculatePricePerDay($car, $currencyCode);

        return view('rentals.show', [
            'car' => $car,
            'translation' => $translation,
            'pricing' => $pricing,
        ]);
    }
}
