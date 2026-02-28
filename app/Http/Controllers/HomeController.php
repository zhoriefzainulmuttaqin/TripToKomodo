<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use App\Models\WebSetting;
use App\Services\LabuanBajoWeatherService;
use Illuminate\Support\Facades\DB;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();

        try {
            $fallbackLocale = (string) config('app.fallback_locale', 'en');
            $translationLocales = array_values(array_unique([$locale, $fallbackLocale]));

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

        try {
            $destinations = DB::table('destinations')
                ->select(['name', 'category', 'distance', 'lat', 'lng'])
                ->orderBy('name')
                ->get();
        } catch (\Throwable) {
            $destinations = collect();
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



        return view('welcome', [
            'packages' => $packages,
            'destinations' => $destinations,
            'weather' => $weather,
            'heroBackgroundUrl' => $heroBackgroundUrl,
        ]);
    }
}

