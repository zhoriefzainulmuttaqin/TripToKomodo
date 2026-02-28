<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class LabuanBajoWeatherService
{
    private const CACHE_KEY = 'weather.labuanbajo.v1';

    public function get(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addMinutes(15), function () {
            $timezone = 'Asia/Makassar';
            $now = CarbonImmutable::now($timezone);

            $response = Http::timeout(8)
                ->retry(2, 150)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => -8.4866,
                    'longitude' => 119.8892,
                    'timezone' => $timezone,
                    'current_weather' => 'true',
                    'forecast_days' => 14,
                    'daily' => implode(',', [
                        'weathercode',
                        'temperature_2m_max',
                        'temperature_2m_min',
                        'precipitation_probability_max',
                    ]),
                ]);

            if (!$response->ok()) {
                throw new \RuntimeException('Weather API request failed.');
            }

            $json = $response->json();

            $daily = $json['daily'] ?? [];
            $dates = $daily['time'] ?? [];
            $codes = $daily['weathercode'] ?? [];
            $tmax = $daily['temperature_2m_max'] ?? [];
            $tmin = $daily['temperature_2m_min'] ?? [];
            $pop = $daily['precipitation_probability_max'] ?? [];

            $days = [];
            $count = is_array($dates) ? count($dates) : 0;

            for ($i = 0; $i < $count; $i++) {
                $date = CarbonImmutable::parse($dates[$i], $timezone);
                $code = isset($codes[$i]) ? (int) $codes[$i] : null;

                $days[] = [
                    'index' => $i,
                    'day_label' => 'Hari ' . ($i + 1),
                    'dow' => $this->dayOfWeekShortId($date),
                    'date' => $date->toDateString(),
                    'temp_max' => isset($tmax[$i]) ? (int) round((float) $tmax[$i]) : null,
                    'temp_min' => isset($tmin[$i]) ? (int) round((float) $tmin[$i]) : null,
                    'precipitation_probability_max' => isset($pop[$i]) ? (int) round((float) $pop[$i]) : null,
                    'weathercode' => $code,
                    'status' => $this->weatherCodeToTextId($code),
                    'scene' => $this->weatherCodeToScene($code),
                ];
            }

            $today = $days[0] ?? null;

            $current = $json['current_weather'] ?? [];
            $currentCode = isset($current['weathercode'])
                ? (int) $current['weathercode']
                : ($today['weathercode'] ?? null);

            return [
                'location' => [
                    'name' => 'Labuan Bajo',
                    'lat' => -8.4866,
                    'lng' => 119.8892,
                    'timezone' => $timezone,
                ],
                'fetched_at' => $now->toIso8601String(),
                'current' => [
                    'temperature' => isset($current['temperature']) ? (int) round((float) $current['temperature']) : null,
                    'windspeed' => isset($current['windspeed']) ? (float) $current['windspeed'] : null,
                    'weathercode' => $currentCode,
                    'status' => $this->weatherCodeToTextId($currentCode),
                    'scene' => $this->weatherCodeToScene($currentCode),
                    'is_day' => isset($current['is_day']) ? (bool) $current['is_day'] : null,
                    'time' => $current['time'] ?? null,
                ],
                'today' => $today,
                'daily' => $days,
            ];
        });
    }

    private function dayOfWeekShortId(CarbonImmutable $date): string
    {
        $map = [
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
            7 => 'Min',
        ];

        return $map[$date->dayOfWeekIso] ?? $date->format('D');
    }

    private function weatherCodeToScene(?int $code): string
    {
        if ($code === null) {
            return 'cloudy';
        }

        if ($code === 0) {
            return 'sunny';
        }

        if (in_array($code, [1, 2, 3], true)) {
            return 'cloudy';
        }

        if (in_array($code, [45, 48], true)) {
            return 'mist';
        }

        if (($code >= 51 && $code <= 67) || ($code >= 80 && $code <= 82)) {
            return 'rainy';
        }

        if ($code >= 95) {
            return 'storm';
        }

        return 'cloudy';
    }

    private function weatherCodeToTextId(?int $code): string
    {
        return match (true) {
            $code === null => 'Tidak diketahui',
            $code === 0 => 'Cerah',
            $code === 1 => 'Cerah',
            $code === 2 => 'Cerah Berawan',
            $code === 3 => 'Berawan',
            in_array($code, [45, 48], true) => 'Berkabut',
            $code >= 51 && $code <= 57 => 'Gerimis',
            $code >= 61 && $code <= 65 => 'Hujan',
            $code >= 66 && $code <= 67 => 'Hujan Lebat',
            $code >= 80 && $code <= 82 => 'Hujan',
            $code >= 95 && $code <= 99 => 'Badai Petir',
            default => 'Berawan',
        };
    }
}
