<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Currency;
use App\Models\PriceMargin;
use App\Models\TourPackage;

class PriceCalculator
{
    public function calculateSellingPrice(TourPackage $package, ?Coupon $coupon, ?string $currencyCode = null): array
    {
        $baseIdr = (float) $package->base_price_idr;
        $margin = $this->resolveMargin($package);
        $sellingIdr = $this->applyMargin($baseIdr, $margin['type'], $margin['value']);

        if ($coupon) {
            $sellingIdr = $this->applyCoupon($sellingIdr, $coupon);
        }

        $currencyCode = strtoupper($currencyCode ?: 'IDR');
        $converted = $this->convertFromIdr($sellingIdr, $currencyCode);
        $converted = $this->applyPsychologyPricing($converted);

        return [
            'base_price_idr' => $baseIdr,
            'selling_price_idr' => $sellingIdr,
            'selling_price_converted' => $converted,
            'currency_code' => $currencyCode,
        ];
    }

    protected function resolveMargin(TourPackage $package): array
    {
        $margin = PriceMargin::query()
            ->where('is_active', true)
            ->where(function ($query) use ($package): void {
                $query->where(function ($query) use ($package): void {
                    $query->where('scope_type', 'package')
                        ->where('scope_id', $package->id);
                })->orWhere(function ($query) use ($package): void {
                    $query->where('scope_type', 'operator')
                        ->where('scope_id', $package->tour_operator_id);
                });
            })
            ->orderByDesc('scope_type')
            ->first();

        return [
            'type' => $margin?->margin_type ?? 'percentage',
            'value' => (float) ($margin?->margin_value ?? 0),
        ];
    }

    protected function applyMargin(float $amount, string $type, float $value): float
    {
        if ($type === 'fixed') {
            return max(0, $amount + $value);
        }

        return max(0, $amount + ($amount * ($value / 100)));
    }

    protected function applyCoupon(float $amount, Coupon $coupon): float
    {
        if ($coupon->type === 'fixed') {
            return max(0, $amount - (float) $coupon->value);
        }

        return max(0, $amount - ($amount * ((float) $coupon->value / 100)));
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

    protected function applyPsychologyPricing(float $amount): float
    {
        // Psychology pricing dimatikan untuk semua currency.
        // Harga hanya dibulatkan normal agar sesuai nilai perhitungan sebenarnya.
        return round($amount, 2);
    }
}
