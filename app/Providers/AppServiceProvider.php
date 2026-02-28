<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\TourCategory;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $languages = Language::query()->where('is_active', true)->get();
            $currencies = Currency::query()->where('is_active', true)->get();
            $tourCategories = TourCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        } catch (\Throwable) {
            $languages = collect();
            $currencies = collect();
            $tourCategories = collect();
        }

        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['code' => 'id', 'name' => 'Indonesia'],
                (object) ['code' => 'en', 'name' => 'English'],
            ]);
        }

        if ($currencies->isEmpty()) {
            $currencies = collect([
                (object) ['code' => 'IDR', 'symbol' => 'Rp'],
                (object) ['code' => 'USD', 'symbol' => '$'],
            ]);
        }

        View::share([
            'activeLanguages' => $languages,
            'activeCurrencies' => $currencies,
            'activeTourCategories' => $tourCategories,
            'currentCurrency' => session('currency', 'IDR'),
        ]);
    }
}
