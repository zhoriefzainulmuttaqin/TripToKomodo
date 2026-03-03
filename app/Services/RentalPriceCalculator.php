<?php

namespace App\Services;

use App\Models\Currency;
use App\Models\RentalCar;

class RentalPriceCalculator
{
    public function calculatePricePerDay(RentalCar $car, ?string $currencyCode = null): array
    {
        $baseIdr = (float) ($car->price_per_day_idr ?? 0);

        $currencyCode = strtoupper($currencyCode ?: 'IDR');
        $converted = $this->convertFromIdr($baseIdr, $currencyCode);
        $converted = $this->applyPsychologyPricing($converted, $currencyCode);

        return [
            'price_per_day_idr' => $baseIdr,
            'price_per_day_converted' => $converted,
            'currency_code' => $currencyCode,
        ];
    }

    protected function convertFromIdr(float $amount, string $currencyCode): float
    {
        if ($currencyCode === 'IDR') {
            return $amount;
        }

        $rate = Currency::query()->where('code', $currencyCode)->value('exchange_rate_to_idr');
        if (!$rate || $rate == 0) {
            return $amount;
        }

        return $amount / (float) $rate;
    }

    protected function applyPsychologyPricing(float $amount, string $currencyCode): float
    {
        $rounded = round($amount, 2);

        if (in_array($currencyCode, ['USD', 'EUR', 'GBP', 'AUD', 'CAD'], true)) {
            return floor($rounded) + 0.99;
        }

        if ($currencyCode === 'IDR') {
            return floor($rounded / 1000) * 1000 + 990;
        }

        return $rounded;
    }
}
