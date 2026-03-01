<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'IDR',
                'symbol' => 'Rp',
                'exchange_rate_to_idr' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'USD',
                'symbol' => '$',
                'exchange_rate_to_idr' => 15500,
                'is_active' => true,
            ],
            [
                'code' => 'EUR',
                'symbol' => '€',
                'exchange_rate_to_idr' => 16200,
                'is_active' => true,
            ],
            [
                'code' => 'SGD',
                'symbol' => 'S$',
                'exchange_rate_to_idr' => 11500,
                'is_active' => true,
            ],
            [
                'code' => 'AUD',
                'symbol' => 'A$',
                'exchange_rate_to_idr' => 10000,
                'is_active' => true,
            ],
            [
                'code' => 'MYR',
                'symbol' => 'RM',
                'exchange_rate_to_idr' => 3500,
                'is_active' => true,
            ],
            [
                'code' => 'CNY',
                'symbol' => '¥',
                'exchange_rate_to_idr' => 2150,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
