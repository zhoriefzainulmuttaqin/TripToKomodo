<?php

use App\Services\ExchangeRateService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('rates:update', function (ExchangeRateService $service): void {
    $service->updateRates(['USD', 'EUR', 'CNY', 'RUB', 'MYR']);
    $this->info('Exchange rates updated.');
})->purpose('Update currency exchange rates daily');

app(Schedule::class)->command('rates:update')->daily();
