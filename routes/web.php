<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DestinationController as AdminDestinationController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\TourCategoryController as AdminTourCategoryController;
use App\Http\Controllers\Admin\TourPackageAvailabilityController as AdminTourPackageAvailabilityController;
use App\Http\Controllers\Admin\TourPackageController as AdminTourPackageController;
use App\Http\Controllers\Admin\TourPackageImageController as AdminTourPackageImageController;
use App\Http\Controllers\Admin\WebSettingController as AdminWebSettingController;
use App\Http\Controllers\Admin\RentalController as AdminRentalController;
use App\Http\Controllers\Admin\RentalCarController as AdminRentalCarController;
use App\Http\Controllers\Admin\BlogPostController as AdminBlogPostController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ContactBookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RentalCarController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\WeatherController;
use App\Http\Middleware\DetectLocaleAndCurrency;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SeoRedirectMiddleware;
use App\Models\BlogPost;
use App\Models\RentalCarTranslation;
use App\Models\TourPackageTranslation;
use Illuminate\Support\Facades\Route;

Route::get('/currency/{code}', function (string $code) {
    session(['currency' => strtoupper($code)]);

    return redirect()->back();
})->name('currency.switch');

Route::get('/lang/{lang}', function (string $lang) {
    $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
    if (!in_array($lang, $supportedLocales, true)) {
        $lang = 'en';
    }

    session(['locale' => $lang]);

    $referer = url()->previous();
    $parts = parse_url($referer);
    $path = $parts['path'] ?? '';
    $segments = array_values(array_filter(explode('/', $path), fn($seg) => $seg !== ''));

    if (!empty($segments) && in_array($segments[0], $supportedLocales, true)) {
        $fromLang = (string) $segments[0];
        $segments[0] = $lang;

        // Special case: jika sedang di detail tour, ganti slug mengikuti bahasa target.
        // Format: /{lang}/tours/{slug}
        if (($segments[1] ?? null) === 'tours' && !empty($segments[2] ?? null)) {
            $currentSlug = (string) $segments[2];

            $currentTranslation = TourPackageTranslation::query()
                ->where('slug', $currentSlug)
                ->where('language_code', $fromLang)
                ->where('is_active', true)
                ->first();

            if ($currentTranslation) {
                $targetTranslation = TourPackageTranslation::query()
                    ->where('tour_package_id', $currentTranslation->tour_package_id)
                    ->where('language_code', $lang)
                    ->where('is_active', true)
                    ->first();

                if ($targetTranslation) {
                    $segments[2] = $targetTranslation->slug;
                }
            }
        }

        // Special case: jika sedang di detail rental mobil, ganti slug mengikuti bahasa target.
        // Format: /{lang}/rental-mobil/{slug}
        if (($segments[1] ?? null) === 'rental-mobil' && !empty($segments[2] ?? null)) {
            $currentSlug = (string) $segments[2];

            $currentTranslation = RentalCarTranslation::query()
                ->where('slug', $currentSlug)
                ->where('language_code', $fromLang)
                ->where('is_active', true)
                ->first();

            if ($currentTranslation) {
                $targetTranslation = RentalCarTranslation::query()
                    ->where('rental_car_id', $currentTranslation->rental_car_id)
                    ->where('language_code', $lang)
                    ->where('is_active', true)
                    ->first();

                if ($targetTranslation) {
                    $segments[2] = $targetTranslation->slug;
                } else {
                    // If no translation exists, fallback to rental listing.
                    $segments = array_slice($segments, 0, 2);
                }
            }
        }

        // Special case: jika sedang di detail Komodo Insider, ganti slug mengikuti bahasa target.
        // Format: /{lang}/komodo-insider/{slug}
        if (($segments[1] ?? null) === 'komodo-insider' && !empty($segments[2] ?? null)) {
            $currentSlug = (string) $segments[2];

            $currentPost = BlogPost::query()
                ->where('slug', $currentSlug)
                ->where('language_code', $fromLang)
                ->where('is_published', true)
                ->first();

            if ($currentPost) {
                $targetPost = BlogPost::query()
                    ->where('group_key', $currentPost->group_key)
                    ->where('language_code', $lang)
                    ->where('is_published', true)
                    ->first();

                if ($targetPost) {
                    $segments[2] = $targetPost->slug;
                } else {
                    // If no translation exists, fallback to blog index.
                    $segments = array_slice($segments, 0, 2);
                }
            }
        }

        $newPath = '/' . implode('/', $segments);
    } else {
        $newPath = '/' . $lang;
    }

    $query = isset($parts['query']) ? '?' . $parts['query'] : '';

    return redirect($newPath . $query);
})->name('lang.switch');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/{lang}/sitemap.xml', [SitemapController::class, 'index'])->where(['lang' => 'id|en|zh|es|de|ru'])->name('sitemap.lang');

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
        Route::resource('faqs', AdminFaqController::class)->except(['show']);
        Route::resource('blog-posts', AdminBlogPostController::class)->except(['show']);
        Route::resource('tour-categories', AdminTourCategoryController::class)->except(['show']);
        Route::resource('tour-packages', AdminTourPackageController::class)->except(['show']);
        Route::put('tour-packages/{id}/restore', [AdminTourPackageController::class, 'restore'])->name('tour-packages.restore');

        Route::delete('tour-packages/{tourPackage}/images/{tourImage}', [AdminTourPackageImageController::class, 'destroy'])->name('tour-packages.images.destroy');
        Route::post('tour-packages/{tourPackage}/availabilities', [AdminTourPackageAvailabilityController::class, 'store'])->name('tour-packages.availabilities.store');
        Route::delete('tour-packages/{tourPackage}/availabilities/{availability}', [AdminTourPackageAvailabilityController::class, 'destroy'])->name('tour-packages.availabilities.destroy');

        Route::get('web-settings', [AdminWebSettingController::class, 'edit'])->name('web-settings.edit');
        Route::put('web-settings', [AdminWebSettingController::class, 'update'])->name('web-settings.update');

        Route::resource('rental-cars', AdminRentalCarController::class)->except(['show']);
        Route::put('rental-cars/{id}/restore', [AdminRentalCarController::class, 'restore'])->name('rental-cars.restore');

        Route::get('rental', [AdminRentalController::class, 'edit'])->name('rental.edit');
        Route::put('rental', [AdminRentalController::class, 'update'])->name('rental.update');
    });

Route::get('/', function () {
    $lang = session('locale') ?? config('app.locale', 'en');
    $lang = in_array($lang, ['id', 'en', 'zh', 'es', 'de', 'ru'], true) ? $lang : 'en';

    return redirect()->route('home', ['lang' => $lang]);
})->name('root');

Route::prefix('{lang}')
    ->where(['lang' => 'id|en|zh|es|de|ru'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');

        Route::get('/api/weather/labuan-bajo', [WeatherController::class, 'labuanBajo'])->name('weather.labuanbajo');

        Route::get('/tours', [TourController::class, 'index'])->name('tours.index');
        Route::get('/tours/{slug}', [TourController::class, 'show'])->name('tours.show');

        Route::get('/rental-mobil', [RentalCarController::class, 'index'])->name('rental.mobil');
        Route::get('/rental-mobil/{slug}', [RentalCarController::class, 'show'])->name('rental.mobil.show');

        Route::get('/komodo-insider', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/komodo-insider/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('/about', [PageController::class, 'about'])->name('about');
        Route::get('/contact', [PageController::class, 'contact'])->name('contact');
        Route::post('/contact/booking', ContactBookingController::class)->middleware('throttle:10,1')->name('contact.booking');
    });

require __DIR__ . '/auth.php';
