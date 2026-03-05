<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function index(Request $request): View
    {
        $allowedRanges = [7, 30, 90];
        $days = (int) $request->query('range', 30);
        if (!in_array($days, $allowedRanges, true)) {
            $days = 30;
        }

        $startAt = Carbon::now()->subDays($days)->startOfDay();

        $baseQuery = AnalyticsEvent::query()
            ->where('occurred_at', '>=', $startAt);

        $pageViewsQuery = (clone $baseQuery)->where('event_type', 'page_view');

        $totalVisits = (clone $pageViewsQuery)->count();
        $uniqueVisitors = (clone $pageViewsQuery)
            ->selectRaw("COUNT(DISTINCT COALESCE(NULLIF(session_id, ''), NULLIF(ip_address, ''))) as aggregate")
            ->value('aggregate') ?? 0;

        $contactClicks = (clone $baseQuery)
            ->where('event_type', 'contact_click')
            ->count();

        $avgEngagement = (float) ((clone $pageViewsQuery)->avg('engagement_seconds') ?? 0);
        $engagedVisits = (clone $pageViewsQuery)
            ->where('engagement_seconds', '>=', 30)
            ->count();
        $engagementRate = $totalVisits > 0 ? ($engagedVisits / $totalVisits) * 100 : 0;

        $visitsByPage = (clone $pageViewsQuery)
            ->select('page_path', DB::raw('COUNT(*) as total'))
            ->groupBy('page_path')
            ->orderByDesc('total')
            ->limit(12)
            ->get();

        $visitsByCountry = (clone $pageViewsQuery)
            ->select('country_code', DB::raw('COUNT(*) as total'))
            ->groupBy('country_code')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $visitsByDevice = (clone $pageViewsQuery)
            ->select('device_type', DB::raw('COUNT(*) as total'))
            ->groupBy('device_type')
            ->orderByDesc('total')
            ->get();

        $trafficSources = (clone $pageViewsQuery)
            ->select('source_channel', DB::raw('COUNT(*) as total'))
            ->groupBy('source_channel')
            ->orderByDesc('total')
            ->get();

        $contactTargets = (clone $baseQuery)
            ->where('event_type', 'contact_click')
            ->select('contact_target', DB::raw('COUNT(*) as total'))
            ->groupBy('contact_target')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        return view('admin.analytics.index', [
            'days' => $days,
            'totalVisits' => (int) $totalVisits,
            'uniqueVisitors' => (int) $uniqueVisitors,
            'contactClicks' => (int) $contactClicks,
            'avgEngagement' => $avgEngagement,
            'engagementRate' => $engagementRate,
            'visitsByPage' => $visitsByPage,
            'visitsByCountry' => $visitsByCountry,
            'visitsByDevice' => $visitsByDevice,
            'trafficSources' => $trafficSources,
            'contactTargets' => $contactTargets,
        ]);
    }
}
