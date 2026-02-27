<?php

namespace App\Http\Controllers;

use App\Models\TourPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class HomeController extends Controller
{
    public function index(): View
    {
        $locale = app()->getLocale();

        try {
            $packages = TourPackage::query()
                ->where('status', 'published')
                ->with([
                    'translations' => fn ($query) => $query->where('language_code', $locale),
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

        return view('welcome', [
            'packages' => $packages,
            'destinations' => $destinations,
        ]);
    }

}
