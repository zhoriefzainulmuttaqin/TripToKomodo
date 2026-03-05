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

        // Site identity
        try {
            $siteName = trim((string) WebSetting::get(WebSetting::KEY_SITE_NAME, ''));
            if ($siteName === '') {
                $siteName = (string) config('app.name', 'Trip to Komodo');
            }

            // Optional tagline (default empty so it won't show unless set in admin)
            $siteTagline = trim((string) WebSetting::get(WebSetting::KEY_SITE_TAGLINE, ''));

            $siteLogoPath = WebSetting::get(WebSetting::KEY_SITE_LOGO);
            $siteLogoUrl = !empty($siteLogoPath)
                ? '/storage/' . ltrim((string) $siteLogoPath, '/')
                : null;

            $loginSideImagePath = WebSetting::get(WebSetting::KEY_LOGIN_SIDE_IMAGE);
            $loginSideImageUrl = !empty($loginSideImagePath)
                ? '/storage/' . ltrim((string) $loginSideImagePath, '/')
                : null;
        } catch (\Throwable) {
            $siteName = (string) config('app.name', 'Trip to Komodo');
            $siteTagline = '';
            $siteLogoPath = null;
            $siteLogoUrl = null;
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

        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
        $currentLocale = (string) app()->getLocale();
        if (!in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = (string) config('app.fallback_locale', 'en');
        }

        $getLocalized = function (string $baseKey, mixed $default = '') use ($currentLocale): mixed {
            $localized = WebSetting::get($baseKey . '.' . $currentLocale);
            if ($localized !== null && trim((string) $localized) !== '') {
                return $localized;
            }

            return WebSetting::get($baseKey, $default);
        };

        // Footer (multibahasa)
        try {
            $footerTitle = trim((string) $getLocalized(WebSetting::KEY_FOOTER_TITLE, ''));
            if ($footerTitle === '') {
                $footerTitle = $siteName;
            }

            $footerDescription = trim((string) $getLocalized(WebSetting::KEY_FOOTER_DESCRIPTION, ''));
            if ($footerDescription === '') {
                $footerDescription = 'Eksklusif untuk penjualan paket trip Labuan Bajo: kapal premium, itinerary eksklusif, dan layanan concierge.';
            }

            $footerCopyrightRaw = trim((string) $getLocalized(WebSetting::KEY_FOOTER_COPYRIGHT, ''));
            $paymentMethodsRaw = (string) $getLocalized(WebSetting::KEY_FOOTER_PAYMENT_METHODS, '');

            $paymentMethods = collect(preg_split('/\r\n|\r|\n/', $paymentMethodsRaw))
                ->map(fn ($line) => trim((string) $line))
                ->filter(fn ($line) => $line !== '')
                ->values()
                ->all();

            $socialLinks = [
                'instagram' => trim((string) WebSetting::get(WebSetting::KEY_SOCIAL_INSTAGRAM, '')),
                'facebook' => trim((string) WebSetting::get(WebSetting::KEY_SOCIAL_FACEBOOK, '')),
                'tiktok' => trim((string) WebSetting::get(WebSetting::KEY_SOCIAL_TIKTOK, '')),
                'youtube' => trim((string) WebSetting::get(WebSetting::KEY_SOCIAL_YOUTUBE, '')),
            ];
            $socialLinks = array_filter($socialLinks, fn ($v) => $v !== '');
        } catch (\Throwable) {
            $footerTitle = $siteName;
            $footerDescription = 'Eksklusif untuk penjualan paket trip Labuan Bajo: kapal premium, itinerary eksklusif, dan layanan concierge.';
            $footerCopyrightRaw = '';
            $paymentMethods = [];
            $socialLinks = [];
        }

        $year = date('Y');
        $footerCopyright = $footerCopyrightRaw !== ''
            ? str_replace(['{year}', '{siteName}'], [$year, $siteName], $footerCopyrightRaw)
            : '© ' . $year . ' ' . $siteName . '. All rights reserved.';

        // CMS - About Us (multibahasa, override trans('about') when filled)
        $cmsAbout = [];
        try {
            $aboutTag = trim((string) $getLocalized(WebSetting::KEY_ABOUT_TAG, ''));
            $aboutHeadline = trim((string) $getLocalized(WebSetting::KEY_ABOUT_HEADLINE, ''));
            $aboutSubheadline = trim((string) $getLocalized(WebSetting::KEY_ABOUT_SUBHEADLINE, ''));
            $aboutLeadRaw = (string) $getLocalized(WebSetting::KEY_ABOUT_LEAD, '');

            $aboutImagePath = WebSetting::get(WebSetting::KEY_ABOUT_IMAGE);
            $aboutImageUrl = !empty($aboutImagePath)
                ? '/storage/' . ltrim((string) $aboutImagePath, '/')
                : '';

            $aboutImageAlt = trim((string) $getLocalized(WebSetting::KEY_ABOUT_IMAGE_ALT, ''));
            $aboutBadge = trim((string) $getLocalized(WebSetting::KEY_ABOUT_BADGE, ''));
            $aboutBadgeTitle = trim((string) $getLocalized(WebSetting::KEY_ABOUT_BADGE_TITLE, ''));
            $aboutBadgeDesc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_BADGE_DESC, ''));

            $visionTag = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VISION_TAG, ''));
            $visionTitle = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VISION_TITLE, ''));
            $visionBody = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VISION_BODY, ''));

            $missionTag = trim((string) $getLocalized(WebSetting::KEY_ABOUT_MISSION_TAG, ''));
            $missionTitle = trim((string) $getLocalized(WebSetting::KEY_ABOUT_MISSION_TITLE, ''));
            $missionBody = trim((string) $getLocalized(WebSetting::KEY_ABOUT_MISSION_BODY, ''));

            $valuesTag = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_TAG, ''));
            $valuesTitle = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_TITLE, ''));
            $valuesDesc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_DESC, ''));

            $valuesItem1Title = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_ITEM_1_TITLE, ''));
            $valuesItem1Desc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_ITEM_1_DESC, ''));
            $valuesItem2Title = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_ITEM_2_TITLE, ''));
            $valuesItem2Desc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_ITEM_2_DESC, ''));
            $valuesItem3Title = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_ITEM_3_TITLE, ''));
            $valuesItem3Desc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_VALUES_ITEM_3_DESC, ''));

            $highlights1Title = trim((string) $getLocalized(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_TITLE, ''));
            $highlights1Desc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_DESC, ''));
            $highlights2Title = trim((string) $getLocalized(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_TITLE, ''));
            $highlights2Desc = trim((string) $getLocalized(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_DESC, ''));

            $leadLines = collect(preg_split('/\r\n|\r|\n/', $aboutLeadRaw))
                ->map(fn ($line) => trim((string) $line))
                ->filter(fn ($line) => $line !== '')
                ->values()
                ->all();

            $stats = [
                [
                    'value' => trim((string) $getLocalized(WebSetting::KEY_ABOUT_STAT_1_VALUE, '')),
                    'label' => trim((string) $getLocalized(WebSetting::KEY_ABOUT_STAT_1_LABEL, '')),
                ],
                [
                    'value' => trim((string) $getLocalized(WebSetting::KEY_ABOUT_STAT_2_VALUE, '')),
                    'label' => trim((string) $getLocalized(WebSetting::KEY_ABOUT_STAT_2_LABEL, '')),
                ],
                [
                    'value' => trim((string) $getLocalized(WebSetting::KEY_ABOUT_STAT_3_VALUE, '')),
                    'label' => trim((string) $getLocalized(WebSetting::KEY_ABOUT_STAT_3_LABEL, '')),
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

            $statsFilled = [];
            foreach ($stats as $idx => $s) {
                $value = trim((string) ($s['value'] ?? ''));
                $label = trim((string) ($s['label'] ?? ''));

                if ($value !== '' || $label !== '') {
                    $statsFilled[$idx] = array_filter([
                        'value' => $value !== '' ? $value : null,
                        'label' => $label !== '' ? $label : null,
                    ], fn ($v) => $v !== null);
                }
            }

            $vision = array_filter([
                'tag' => $visionTag !== '' ? $visionTag : null,
                'title' => $visionTitle !== '' ? $visionTitle : null,
                'body' => $visionBody !== '' ? $visionBody : null,
            ], fn ($v) => $v !== null);

            $mission = array_filter([
                'tag' => $missionTag !== '' ? $missionTag : null,
                'title' => $missionTitle !== '' ? $missionTitle : null,
                'body' => $missionBody !== '' ? $missionBody : null,
            ], fn ($v) => $v !== null);

            $valuesItems = [];
            if ($valuesItem1Title !== '' || $valuesItem1Desc !== '') {
                $valuesItems[0] = array_filter([
                    'title' => $valuesItem1Title !== '' ? $valuesItem1Title : null,
                    'desc' => $valuesItem1Desc !== '' ? $valuesItem1Desc : null,
                ], fn ($v) => $v !== null);
            }
            if ($valuesItem2Title !== '' || $valuesItem2Desc !== '') {
                $valuesItems[1] = array_filter([
                    'title' => $valuesItem2Title !== '' ? $valuesItem2Title : null,
                    'desc' => $valuesItem2Desc !== '' ? $valuesItem2Desc : null,
                ], fn ($v) => $v !== null);
            }
            if ($valuesItem3Title !== '' || $valuesItem3Desc !== '') {
                $valuesItems[2] = array_filter([
                    'title' => $valuesItem3Title !== '' ? $valuesItem3Title : null,
                    'desc' => $valuesItem3Desc !== '' ? $valuesItem3Desc : null,
                ], fn ($v) => $v !== null);
            }

            $values = array_filter([
                'tag' => $valuesTag !== '' ? $valuesTag : null,
                'title' => $valuesTitle !== '' ? $valuesTitle : null,
                'desc' => $valuesDesc !== '' ? $valuesDesc : null,
                'items' => !empty($valuesItems) ? $valuesItems : null,
            ], fn ($v) => $v !== null);

            $highlights = [];
            if ($highlights1Title !== '' || $highlights1Desc !== '') {
                $highlights[0] = array_filter([
                    'title' => $highlights1Title !== '' ? $highlights1Title : null,
                    'desc' => $highlights1Desc !== '' ? $highlights1Desc : null,
                ], fn ($v) => $v !== null);
            }
            if ($highlights2Title !== '' || $highlights2Desc !== '') {
                $highlights[1] = array_filter([
                    'title' => $highlights2Title !== '' ? $highlights2Title : null,
                    'desc' => $highlights2Desc !== '' ? $highlights2Desc : null,
                ], fn ($v) => $v !== null);
            }

            $cmsAbout = [];
            if (!empty($hero)) {
                $cmsAbout['hero'] = $hero;
            }
            if (!empty($statsFilled)) {
                $cmsAbout['stats'] = $statsFilled;
            }
            if (!empty($vision)) {
                $cmsAbout['vision'] = $vision;
            }
            if (!empty($mission)) {
                $cmsAbout['mission'] = $mission;
            }
            if (!empty($values)) {
                $cmsAbout['values'] = $values;
            }
            if (!empty($highlights)) {
                $cmsAbout['highlights'] = $highlights;
            }
        } catch (\Throwable) {
            $cmsAbout = [];
        }

        // CMS - Car Rental (multibahasa, override trans('pages.rental') when filled)
        $cmsRental = [];
        try {
            $rentalPageTitle = trim((string) $getLocalized(WebSetting::KEY_RENTAL_PAGE_TITLE, ''));
            $rentalPageMeta = trim((string) $getLocalized(WebSetting::KEY_RENTAL_PAGE_META, ''));
            $rentalPageKeywords = trim((string) $getLocalized(WebSetting::KEY_RENTAL_PAGE_KEYWORDS, ''));

            $rentalHeroTag = trim((string) $getLocalized(WebSetting::KEY_RENTAL_HERO_TAG, ''));
            $rentalHeroTitle = trim((string) $getLocalized(WebSetting::KEY_RENTAL_HERO_TITLE, ''));
            $rentalHeroDesc = trim((string) $getLocalized(WebSetting::KEY_RENTAL_HERO_DESC, ''));

            $rentalCtaTitle = trim((string) $getLocalized(WebSetting::KEY_RENTAL_CTA_TITLE, ''));
            $rentalCtaDesc = trim((string) $getLocalized(WebSetting::KEY_RENTAL_CTA_DESC, ''));
            $rentalCtaButton = trim((string) $getLocalized(WebSetting::KEY_RENTAL_CTA_BUTTON, ''));

            $page = array_filter([
                'title' => $rentalPageTitle !== '' ? $rentalPageTitle : null,
                'meta' => $rentalPageMeta !== '' ? $rentalPageMeta : null,
                'keywords' => $rentalPageKeywords !== '' ? $rentalPageKeywords : null,
            ], fn ($v) => $v !== null);

            $hero = array_filter([
                'tag' => $rentalHeroTag !== '' ? $rentalHeroTag : null,
                'title' => $rentalHeroTitle !== '' ? $rentalHeroTitle : null,
                'desc' => $rentalHeroDesc !== '' ? $rentalHeroDesc : null,
            ], fn ($v) => $v !== null);

            $cta = array_filter([
                'title' => $rentalCtaTitle !== '' ? $rentalCtaTitle : null,
                'desc' => $rentalCtaDesc !== '' ? $rentalCtaDesc : null,
                'button' => $rentalCtaButton !== '' ? $rentalCtaButton : null,
            ], fn ($v) => $v !== null);

            $cmsRental = [];
            if (!empty($page)) {
                $cmsRental['page'] = $page;
            }
            if (!empty($hero)) {
                $cmsRental['hero'] = $hero;
            }
            if (!empty($cta)) {
                $cmsRental['cta'] = $cta;
            }
        } catch (\Throwable) {
            $cmsRental = [];
        }

        $siteInitials = trim(collect(preg_split('/\s+/', $siteName))
            ->filter(fn ($s) => $s !== '')
            ->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr((string) $w, 0, 1)))
            ->join(''));
        if ($siteInitials === '') {
            $siteInitials = 'TK';
        }

        View::share([
            'activeLanguages' => $languages,
            'activeCurrencies' => $currencies,
            'activeTourCategories' => $tourCategories,
            'currentCurrency' => session('currency', 'IDR'),

            // Site identity (global)
            'siteName' => $siteName,
            'siteTagline' => $siteTagline,
            'siteLogoUrl' => $siteLogoUrl,
            'siteLogoPath' => $siteLogoPath,
            'loginSideImageUrl' => $loginSideImageUrl,
            'loginSideImagePath' => $loginSideImagePath,
            'siteInitials' => $siteInitials,

            'contactSettings' => [
                'email' => $contactEmail,
                'phone' => $contactPhone,
                'whatsapp' => $contactWhatsapp,
                'whatsapp_url' => $contactWhatsappUrl,
            ],
            'footerSettings' => [
                'title' => $footerTitle,
                'description' => $footerDescription,
                'copyright' => $footerCopyright,
                'payment_methods' => $paymentMethods,
                'social_links' => $socialLinks,
            ],
            'cmsAbout' => $cmsAbout,
            'cmsRental' => $cmsRental,
        ]);
    }
}
