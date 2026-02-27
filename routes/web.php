<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TourController;
use App\Http\Middleware\DetectLocaleAndCurrency;
use App\Http\Middleware\SeoRedirectMiddleware;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/currency/{code}', function (string $code) {
    session(['currency' => strtoupper($code)]);

    return redirect()->back();
})->name('currency.switch');

Route::get('/lang/{lang}', function (string $lang) {
    session(['locale' => $lang]);

    return redirect()->back();
})->name('lang.switch');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/{lang}/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.lang');

Route::get('/dashboard', function () {
    if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()?->is_admin) {
        return redirect()->route('admin.dashboard');
    }

    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', EnsureAdmin::class])
    ->withoutMiddleware([
        SeoRedirectMiddleware::class,
        DetectLocaleAndCurrency::class,
    ])
    ->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('destinations', AdminDestinationController::class)->except(['show']);
    });

Route::prefix('{lang?}')
    ->where(['lang' => 'id|en|ru|zh|de'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
        Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.show');
    });

require __DIR__ . '/auth.php';
