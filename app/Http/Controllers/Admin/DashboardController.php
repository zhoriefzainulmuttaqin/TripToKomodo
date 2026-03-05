<?php

namespace App\Http\Controllers\Admin;

use App\Models\BlogPost;
use App\Models\Customer;
use App\Models\Destination;
use App\Models\TourPackage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController
{
    public function index(): View
    {
        $countTable = static function (string $table): int {
            try {
                return (int) DB::table($table)->count();
            } catch (\Throwable) {
                return 0;
            }
        };

        $destinationCount = $countTable('destinations');
        $tourPackageCount = $countTable('tour_packages');
        $customerCount = $countTable('customers');
        $rentalCarCount = $countTable('rental_cars');

        $visitMonthLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $visitYear = (int) now()->year;
        $visitMonthlyCounts = array_fill(0, 12, 0);
        $visitsBars = array_fill(0, 12, 0);
        $totalVisitsYear = 0;

        $since30Days = Carbon::now()->subDays(30)->startOfDay();
        $currentVisits = 0;
        $mobileVisits = 0;
        $desktopVisits = 0;
        $tabletVisits = 0;
        $otherDeviceVisits = 0;

        $contactClicks = 0;
        $totalCountries = 0;
        $topCountries = collect();

        try {
            if (Schema::hasTable('analytics_events')) {
                $monthlyRows = DB::table('analytics_events')
                    ->selectRaw('MONTH(occurred_at) as month_no, COUNT(*) as total')
                    ->where('event_type', 'page_view')
                    ->whereYear('occurred_at', $visitYear)
                    ->groupBy('month_no')
                    ->pluck('total', 'month_no');

                for ($month = 1; $month <= 12; $month++) {
                    $visitMonthlyCounts[$month - 1] = (int) ($monthlyRows[$month] ?? 0);
                }

                $totalVisitsYear = array_sum($visitMonthlyCounts);
                $maxMonthly = max(1, max($visitMonthlyCounts));
                $visitsBars = array_map(
                    static fn (int $count): int => (int) round(($count / $maxMonthly) * 100),
                    $visitMonthlyCounts
                );

                $currentVisits = (int) DB::table('analytics_events')
                    ->where('event_type', 'page_view')
                    ->where('occurred_at', '>=', $since30Days)
                    ->count();

                $deviceRows = DB::table('analytics_events')
                    ->selectRaw("COALESCE(NULLIF(device_type, ''), 'unknown') as device_type, COUNT(*) as total")
                    ->where('event_type', 'page_view')
                    ->where('occurred_at', '>=', $since30Days)
                    ->groupBy('device_type')
                    ->pluck('total', 'device_type');

                $mobileVisits = (int) ($deviceRows['mobile'] ?? 0);
                $desktopVisits = (int) ($deviceRows['desktop'] ?? 0);
                $tabletVisits = (int) ($deviceRows['tablet'] ?? 0);

                $knownTotal = $mobileVisits + $desktopVisits + $tabletVisits;
                $allDeviceTotal = array_sum(array_map('intval', $deviceRows->toArray()));
                $otherDeviceVisits = max(0, $allDeviceTotal - $knownTotal);

                $contactClicks = (int) DB::table('analytics_events')
                    ->where('event_type', 'contact_click')
                    ->where('occurred_at', '>=', $since30Days)
                    ->count();

                $countryRows = DB::table('analytics_events')
                    ->selectRaw("COALESCE(NULLIF(country_code, ''), 'Unknown') as country_code, COUNT(*) as total")
                    ->where('event_type', 'page_view')
                    ->where('occurred_at', '>=', $since30Days)
                    ->groupBy('country_code')
                    ->orderByDesc('total')
                    ->get();

                $totalCountries = $countryRows->count();
                $topCountries = $countryRows->take(5);
            }
        } catch (\Throwable) {
            $visitsBars = array_fill(0, 12, 0);
        }

        $latestCustomers = collect();
        $latestArticles = collect();
        $latestDestinations = collect();
        $latestTourPackages = collect();

        try {
            if (Schema::hasTable('customers')) {
                $latestCustomers = Customer::query()
                    ->select(['id', 'full_name', 'email', 'phone', 'country', 'created_at'])
                    ->latest('id')
                    ->limit(8)
                    ->get();
            }
        } catch (\Throwable) {
            $latestCustomers = collect();
        }

        try {
            if (Schema::hasTable('blog_posts')) {
                $latestArticles = BlogPost::query()
                    ->select(['id', 'title', 'language_code', 'is_published', 'published_at', 'created_at'])
                    ->orderByDesc('published_at')
                    ->orderByDesc('id')
                    ->limit(8)
                    ->get();
            }
        } catch (\Throwable) {
            $latestArticles = collect();
        }

        try {
            if (Schema::hasTable('destinations')) {
                $latestDestinations = Destination::query()
                    ->select(['id', 'name', 'category', 'is_active', 'created_at'])
                    ->latest('id')
                    ->limit(8)
                    ->get();
            }
        } catch (\Throwable) {
            $latestDestinations = collect();
        }

        try {
            if (Schema::hasTable('tour_packages')) {
                $latestTourPackages = TourPackage::query()
                    ->select(['id', 'code', 'status', 'base_price_idr', 'duration_days', 'duration_nights', 'created_at'])
                    ->latest('id')
                    ->limit(8)
                    ->get();
            }
        } catch (\Throwable) {
            $latestTourPackages = collect();
        }

        return view('admin.dashboard', [
            'destinationCount' => $destinationCount,
            'tourPackageCount' => $tourPackageCount,
            'customerCount' => $customerCount,
            'rentalCarCount' => $rentalCarCount,
            'visitMonthLabels' => $visitMonthLabels,
            'visitYear' => $visitYear,
            'visitMonthlyCounts' => $visitMonthlyCounts,
            'visitsBars' => $visitsBars,
            'totalVisitsYear' => $totalVisitsYear,
            'currentVisits' => $currentVisits,
            'mobileVisits' => $mobileVisits,
            'desktopVisits' => $desktopVisits,
            'tabletVisits' => $tabletVisits,
            'otherDeviceVisits' => $otherDeviceVisits,
            'contactClicks' => $contactClicks,
            'totalCountries' => $totalCountries,
            'topCountries' => $topCountries,
            'latestCustomers' => $latestCustomers,
            'latestArticles' => $latestArticles,
            'latestDestinations' => $latestDestinations,
            'latestTourPackages' => $latestTourPackages,
        ]);
    }
}
