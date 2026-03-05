<?php

namespace App\Http\Controllers;

use App\Models\AnalyticsEvent;
use App\Services\GeoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AnalyticsEventController extends Controller
{
    public function store(Request $request, GeoService $geoService): JsonResponse
    {
        $validated = $request->validate([
            'event_type' => ['required', 'string', 'in:page_view,contact_click'],
            'session_id' => ['nullable', 'string', 'max:64'],
            'page_path' => ['nullable', 'string', 'max:255'],
            'page_url' => ['nullable', 'string', 'max:2000'],
            'referrer' => ['nullable', 'string', 'max:2000'],
            'contact_target' => ['nullable', 'string', 'max:120'],
            'engagement_seconds' => ['nullable', 'integer', 'min:0', 'max:7200'],
            'utm_source' => ['nullable', 'string', 'max:120'],
            'utm_medium' => ['nullable', 'string', 'max:120'],
            'utm_campaign' => ['nullable', 'string', 'max:120'],
            'utm_term' => ['nullable', 'string', 'max:120'],
            'utm_content' => ['nullable', 'string', 'max:120'],
        ]);

        $userAgent = (string) ($request->userAgent() ?? '');
        $deviceType = $this->resolveDeviceType($userAgent);
        $browser = $this->resolveBrowser($userAgent);

        $detected = $geoService->detect((string) $request->ip());
        $country = strtoupper((string) ($detected['country'] ?? ''));

        [$sourceChannel, $sourceDetail] = $this->resolveTrafficSource(
            (string) ($validated['utm_source'] ?? ''),
            (string) ($validated['referrer'] ?? '')
        );

        AnalyticsEvent::query()->create([
            'session_id' => $validated['session_id'] ?? $request->session()->getId(),
            'event_type' => $validated['event_type'],
            'page_path' => $validated['page_path'] ?? null,
            'page_url' => $validated['page_url'] ?? null,
            'referrer' => $validated['referrer'] ?? null,
            'source_channel' => $sourceChannel,
            'source_detail' => $sourceDetail,
            'utm_source' => $validated['utm_source'] ?? null,
            'utm_medium' => $validated['utm_medium'] ?? null,
            'utm_campaign' => $validated['utm_campaign'] ?? null,
            'utm_term' => $validated['utm_term'] ?? null,
            'utm_content' => $validated['utm_content'] ?? null,
            'country_code' => $country !== '' ? $country : null,
            'device_type' => $deviceType,
            'browser' => $browser,
            'contact_target' => $validated['contact_target'] ?? null,
            'engagement_seconds' => (int) ($validated['engagement_seconds'] ?? 0),
            'ip_address' => (string) $request->ip(),
            'user_agent' => $userAgent !== '' ? Str::limit($userAgent, 2000, '') : null,
            'occurred_at' => Carbon::now(),
        ]);

        return response()->json(['ok' => true]);
    }

    private function resolveDeviceType(string $userAgent): string
    {
        $ua = Str::lower($userAgent);

        if ($ua === '') {
            return 'unknown';
        }

        if (Str::contains($ua, ['ipad', 'tablet'])) {
            return 'tablet';
        }

        if (Str::contains($ua, ['mobile', 'iphone', 'android'])) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function resolveBrowser(string $userAgent): string
    {
        $ua = Str::lower($userAgent);

        if (Str::contains($ua, 'edg/')) {
            return 'edge';
        }

        if (Str::contains($ua, 'chrome/') && !Str::contains($ua, 'edg/')) {
            return 'chrome';
        }

        if (Str::contains($ua, 'firefox/')) {
            return 'firefox';
        }

        if (Str::contains($ua, 'safari/') && !Str::contains($ua, 'chrome/')) {
            return 'safari';
        }

        return 'other';
    }

    private function resolveTrafficSource(string $utmSource, string $referrer): array
    {
        if ($utmSource !== '') {
            return ['campaign', Str::lower($utmSource)];
        }

        $host = '';
        if ($referrer !== '') {
            $host = (string) (parse_url($referrer, PHP_URL_HOST) ?? '');
            $host = Str::lower($host);
        }

        if ($host === '') {
            return ['direct', 'direct'];
        }

        if (Str::contains($host, ['google.', 'bing.', 'yahoo.', 'duckduckgo.', 'baidu.'])) {
            return ['organic', $host];
        }

        if (Str::contains($host, ['facebook.', 'instagram.', 'tiktok.', 'x.com', 'twitter.', 'linkedin.', 'youtube.'])) {
            return ['social', $host];
        }

        return ['referral', $host];
    }
}
