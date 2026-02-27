<?php

namespace App\Http\Middleware;

use App\Services\GeoService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectLocaleAndCurrency
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeLocale = $request->route('lang');
        if ($routeLocale) {
            session(['locale' => $routeLocale]);
        }

        $locale = session('locale') ?? config('app.locale', 'en');
        app()->setLocale($locale);

        if (!session()->has('currency')) {
            $geo = app(GeoService::class)->detect($request->ip());
            session(['currency' => $geo['currency'] ?? 'IDR']);
        }

        return $next($request);
    }
}
