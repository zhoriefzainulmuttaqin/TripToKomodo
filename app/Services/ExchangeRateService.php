<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function updateRates(array $symbols): void
    {
        $endpoint = config('services.exchange_rate.endpoint');
        if (!$endpoint) {
            return;
        }

        $response = Http::timeout(8)->get($endpoint, [
            'base' => 'IDR',
            'symbols' => implode(',', $symbols),
        ]);

        if (!$response->successful()) {
            return;
        }

        $rates = Arr::get($response->json(), 'rates', []);
        foreach ($symbols as $code) {
            $rate = Arr::get($rates, $code);
            if (!$rate) {
                continue;
            }

            Currency::query()->updateOrCreate(
                ['code' => strtoupper($code)],
                [
                    'symbol' => Currency::query()->where('code', strtoupper($code))->value('symbol') ?? $code,
                    'exchange_rate_to_idr' => $rate,
                    'is_active' => true,
                ]
            );
        }
    }
}
