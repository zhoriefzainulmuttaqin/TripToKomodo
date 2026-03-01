<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function updateRates(?array $symbols = null): void
    {
        // Default endpoint if not set
        $endpoint = config('services.exchange_rate.endpoint', 'https://open.er-api.com/v6/latest/IDR');
        
        $response = Http::timeout(10)->get($endpoint);

        if (!$response->successful()) {
            return;
        }

        $rates = Arr::get($response->json(), 'rates', []);
        
        // If symbols are not provided, update all active non-IDR currencies
        if (empty($symbols)) {
            $symbols = Currency::query()
                ->where('is_active', true)
                ->where('code', '!=', 'IDR')
                ->pluck('code')
                ->toArray();
        }

        foreach ($symbols as $code) {
            $code = strtoupper($code);
            // API returns 1 IDR = X Currency.
            // We need 1 Currency = Y IDR. So Y = 1 / X.
            $rate = Arr::get($rates, $code);
            
            if (!$rate || $rate == 0) {
                continue;
            }

            $rateToIdr = 1 / $rate;

            Currency::query()->updateOrCreate(
                ['code' => $code],
                [
                    'exchange_rate_to_idr' => $rateToIdr,
                    'is_active' => true,
                ]
            );
        }
    }
}
