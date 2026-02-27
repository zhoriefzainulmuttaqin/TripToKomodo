<?php

namespace App\Services;

use App\Models\CountryProfile;
use Illuminate\Support\Facades\Http;

class GeoService
{
    public function detect(string $ipAddress): array
    {
        $default = [
            'country' => null,
            'language' => config('app.locale', 'en'),
            'currency' => 'IDR',
        ];

        $endpoint = config('services.geoip.endpoint');
        if (!$endpoint) {
            return $this->resolveFromProfile(null, $default);
        }

        try {
            $response = Http::timeout(3)->get($endpoint, [
                'ip' => $ipAddress,
                'key' => config('services.geoip.key'),
            ]);

            if (!$response->successful()) {
                return $this->resolveFromProfile(null, $default);
            }

            $data = $response->json();
            $country = strtoupper(
                $data['country_code']
                    ?? $data['country_code2']
                    ?? $data['country']
                    ?? ''
            );

            return $this->resolveFromProfile($country ?: null, $default);
        } catch (\Throwable $exception) {
            return $this->resolveFromProfile(null, $default);
        }
    }

    protected function resolveFromProfile(?string $country, array $default): array
    {
        if (!$country) {
            return $default;
        }

        $profile = CountryProfile::query()->where('country_code', $country)->first();
        if (!$profile) {
            return $default;
        }

        return [
            'country' => $country,
            'language' => $profile->default_language_code,
            'currency' => $profile->default_currency_code,
        ];
    }
}
