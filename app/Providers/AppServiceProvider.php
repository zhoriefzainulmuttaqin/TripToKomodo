<?php

namespace App\Providers;

use App\Models\Currency;
use App\Models\Language;
use App\Models\TourCategory;
use App\Models\WebSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $languages = Language::query()->where('is_active', true)->get();
            $currencies = Currency::query()->where('is_active', true)->get();
            $tourCategories = TourCategory::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
        } catch (\Throwable) {
            $languages = collect();
            $currencies = collect();
            $tourCategories = collect();
        }

        if ($languages->isEmpty()) {
            $languages = collect([
                (object) ['code' => 'id', 'name' => 'Indonesia', 'native_name' => 'Bahasa Indonesia'],
                (object) ['code' => 'en', 'name' => 'English', 'native_name' => 'English'],
                (object) ['code' => 'zh', 'name' => 'Chinese', 'native_name' => '中文'],
                (object) ['code' => 'es', 'name' => 'Spanish', 'native_name' => 'Español'],
                (object) ['code' => 'de', 'name' => 'German', 'native_name' => 'Deutsch'],
                (object) ['code' => 'ru', 'name' => 'Russian', 'native_name' => 'Русский'],
            ]);
        }

        if ($currencies->isEmpty()) {
            $currencies = collect([
                (object) ['code' => 'IDR', 'symbol' => 'Rp'],
                (object) ['code' => 'USD', 'symbol' => '$'],
            ]);
        }

        try {
            $contactEmail = WebSetting::get(WebSetting::KEY_CONTACT_EMAIL);
            $contactPhone = WebSetting::get(WebSetting::KEY_CONTACT_PHONE);
            $contactWhatsapp = WebSetting::get(WebSetting::KEY_CONTACT_WHATSAPP);

            $whatsappDigits = preg_replace('/\D+/', '', (string) $contactWhatsapp);
            $contactWhatsappUrl = !empty($whatsappDigits) ? 'https://wa.me/' . $whatsappDigits : null;
        } catch (\Throwable) {
            $contactEmail = null;
            $contactPhone = null;
            $contactWhatsapp = null;
            $contactWhatsappUrl = null;
        }

        // CMS - About Us (override trans('about') when filled)
        $cmsAbout = [];
        try {
            $aboutTag = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_TAG, ''));
            $aboutHeadline = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_HEADLINE, ''));
            $aboutSubheadline = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_SUBHEADLINE, ''));
            $aboutLeadRaw = (string) WebSetting::get(WebSetting::KEY_ABOUT_LEAD, '');

            $aboutImagePath = WebSetting::get(WebSetting::KEY_ABOUT_IMAGE);
            $aboutImageUrl = !empty($aboutImagePath)
                ? '/storage/' . ltrim((string) $aboutImagePath, '/')
                : '';

            $aboutImageAlt = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_IMAGE_ALT, ''));
            $aboutBadge = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_BADGE, ''));
            $aboutBadgeTitle = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_BADGE_TITLE, ''));
            $aboutBadgeDesc = trim((string) WebSetting::get(WebSetting::KEY_ABOUT_BADGE_DESC, ''));

            $leadLines = collect(preg_split('/\r\n|\r|\n/', $aboutLeadRaw))
                ->map(fn ($line) => trim((string) $line))
                ->filter(fn ($line) => $line !== '')
                ->values()
                ->all();

            $stats = [
                [
                    'value' => trim((string) WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_VALUE, '')),
                    'label' => trim((string) WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_LABEL, '')),
                ],
                [
                    'value' => trim((string) WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_VALUE, '')),
                    'label' => trim((string) WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_LABEL, '')),
                ],
                [
                    'value' => trim((string) WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_VALUE, '')),
                    'label' => trim((string) WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_LABEL, '')),
                ],
            ];

            // Only attach keys that are actually filled, so translations remain default.
            $hero = array_filter([
                'tag' => $aboutTag !== '' ? $aboutTag : null,
                'headline' => $aboutHeadline !== '' ? $aboutHeadline : null,
                'subheadline' => $aboutSubheadline !== '' ? $aboutSubheadline : null,
                'lead' => !empty($leadLines) ? $leadLines : null,
                'image' => $aboutImageUrl !== '' ? $aboutImageUrl : null,
                'image_alt' => $aboutImageAlt !== '' ? $aboutImageAlt : null,
                'badge' => $aboutBadge !== '' ? $aboutBadge : null,
                'badge_title' => $aboutBadgeTitle !== '' ? $aboutBadgeTitle : null,
                'badge_desc' => $aboutBadgeDesc !== '' ? $aboutBadgeDesc : null,
            ], fn ($v) => $v !== null);

            $statsFilled = collect($stats)
                ->filter(fn ($s) => ($s['value'] ?? '') !== '' || ($s['label'] ?? '') !== '')
                ->values()
                ->all();

            $cmsAbout = [];
            if (!empty($hero)) {
                $cmsAbout['hero'] = $hero;
            }
            if (!empty($statsFilled)) {
                $cmsAbout['stats'] = $statsFilled;
            }
        } catch (\Throwable) {
            $cmsAbout = [];
        }

        View::share([
            'activeLanguages' => $languages,
            'activeCurrencies' => $currencies,
            'activeTourCategories' => $tourCategories,
            'currentCurrency' => session('currency', 'IDR'),
            'contactSettings' => [
                'email' => $contactEmail,
                'phone' => $contactPhone,
                'whatsapp' => $contactWhatsapp,
                'whatsapp_url' => $contactWhatsappUrl,
            ],
            'cmsAbout' => $cmsAbout,
        ]);
    }
}
