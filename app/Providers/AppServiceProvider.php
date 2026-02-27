<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Language;
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
        } catch (\Throwable) {
            $languages = collect();
            $currencies = collect();
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
            'currentCurrency' => session('currency', 'IDR'),
        ]);
    }
}
