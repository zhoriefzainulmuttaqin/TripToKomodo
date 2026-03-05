<?php

namespace App\Http\Controllers\Admin;

use App\Models\WebSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class WebSettingController
{
    public function edit(): View
    {
        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
        $editLang = (string) request()->query('lang', '');
        if ($editLang === '' || !in_array($editLang, $supportedLocales, true)) {
            $editLang = (string) (session('locale') ?? config('app.locale', 'en'));
        }
        if (!in_array($editLang, $supportedLocales, true)) {
            $editLang = 'en';
        }

        $heroBackgroundPath = WebSetting::get(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE);

        $heroBackgroundUrl = null;
        if (!empty($heroBackgroundPath)) {
            try {
                // Pakai URL relatif supaya tidak tergantung APP_URL (mis. localhost vs triptokomodo.test)
                $heroBackgroundUrl = '/storage/' . ltrim((string) $heroBackgroundPath, '/');
            } catch (\Throwable) {
                $heroBackgroundUrl = null;
            }
        }

        $siteName = WebSetting::get(WebSetting::KEY_SITE_NAME);
        $siteTagline = WebSetting::get(WebSetting::KEY_SITE_TAGLINE);

        $siteLogoPath = WebSetting::get(WebSetting::KEY_SITE_LOGO);
        $siteLogoUrl = null;
        if (!empty($siteLogoPath)) {
            try {
                $siteLogoUrl = '/storage/' . ltrim((string) $siteLogoPath, '/');
            } catch (\Throwable) {
                $siteLogoUrl = null;
            }
        }

        $loginSideImagePath = WebSetting::get(WebSetting::KEY_LOGIN_SIDE_IMAGE);
        $loginSideImageUrl = null;
        if (!empty($loginSideImagePath)) {
            try {
                $loginSideImageUrl = '/storage/' . ltrim((string) $loginSideImagePath, '/');
            } catch (\Throwable) {
                $loginSideImageUrl = null;
            }
        }


        $contactEmail = WebSetting::get(WebSetting::KEY_CONTACT_EMAIL);
        $contactPhone = WebSetting::get(WebSetting::KEY_CONTACT_PHONE);
        $contactWhatsapp = WebSetting::get(WebSetting::KEY_CONTACT_WHATSAPP);


        $aboutImagePath = WebSetting::get(WebSetting::KEY_ABOUT_IMAGE);
        $aboutImageUrl = null;
        if (!empty($aboutImagePath)) {
            try {
                $aboutImageUrl = '/storage/' . ltrim((string) $aboutImagePath, '/');
            } catch (\Throwable) {
                $aboutImageUrl = null;
            }
        }

        $suffix = '.' . $editLang;

        return view('admin.web-settings.edit', [
            'editLang' => $editLang,

            'siteName' => $siteName,
            'siteTagline' => $siteTagline,
            'siteLogoPath' => $siteLogoPath,
            'siteLogoUrl' => $siteLogoUrl,
            'loginSideImagePath' => $loginSideImagePath,
            'loginSideImageUrl' => $loginSideImageUrl,


            'heroBackgroundPath' => $heroBackgroundPath,
            'heroBackgroundUrl' => $heroBackgroundUrl,
            'contactEmail' => $contactEmail,
            'contactPhone' => $contactPhone,
            'contactWhatsapp' => $contactWhatsapp,

            // Footer (localized by ?lang=xx)
            'footerTitle' => WebSetting::get(WebSetting::KEY_FOOTER_TITLE . $suffix, WebSetting::get(WebSetting::KEY_FOOTER_TITLE)),
            'footerDescription' => WebSetting::get(WebSetting::KEY_FOOTER_DESCRIPTION . $suffix, WebSetting::get(WebSetting::KEY_FOOTER_DESCRIPTION)),
            'footerCopyright' => WebSetting::get(WebSetting::KEY_FOOTER_COPYRIGHT . $suffix, WebSetting::get(WebSetting::KEY_FOOTER_COPYRIGHT)),
            'footerPaymentMethods' => WebSetting::get(WebSetting::KEY_FOOTER_PAYMENT_METHODS . $suffix, WebSetting::get(WebSetting::KEY_FOOTER_PAYMENT_METHODS)),

            // Social
            'socialInstagram' => WebSetting::get(WebSetting::KEY_SOCIAL_INSTAGRAM),
            'socialFacebook' => WebSetting::get(WebSetting::KEY_SOCIAL_FACEBOOK),
            'socialTiktok' => WebSetting::get(WebSetting::KEY_SOCIAL_TIKTOK),
            'socialYoutube' => WebSetting::get(WebSetting::KEY_SOCIAL_YOUTUBE),

            // About CMS (localized by ?lang=xx)
            'aboutTag' => WebSetting::get(WebSetting::KEY_ABOUT_TAG . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_TAG)),
            'aboutHeadline' => WebSetting::get(WebSetting::KEY_ABOUT_HEADLINE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_HEADLINE)),
            'aboutSubheadline' => WebSetting::get(WebSetting::KEY_ABOUT_SUBHEADLINE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_SUBHEADLINE)),
            'aboutLead' => WebSetting::get(WebSetting::KEY_ABOUT_LEAD . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_LEAD)),
            'aboutImagePath' => $aboutImagePath,
            'aboutImageUrl' => $aboutImageUrl,
            'aboutImageAlt' => WebSetting::get(WebSetting::KEY_ABOUT_IMAGE_ALT . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_IMAGE_ALT)),
            'aboutBadge' => WebSetting::get(WebSetting::KEY_ABOUT_BADGE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_BADGE)),
            'aboutBadgeTitle' => WebSetting::get(WebSetting::KEY_ABOUT_BADGE_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_BADGE_TITLE)),
            'aboutBadgeDesc' => WebSetting::get(WebSetting::KEY_ABOUT_BADGE_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_BADGE_DESC)),
            'aboutStat1Value' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_VALUE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_VALUE)),
            'aboutStat1Label' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_LABEL . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_LABEL)),
            'aboutStat2Value' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_VALUE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_VALUE)),
            'aboutStat2Label' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_LABEL . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_LABEL)),
            'aboutStat3Value' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_VALUE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_VALUE)),
            'aboutStat3Label' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_LABEL . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_LABEL)),
            
            // Vision
            'aboutVisionTag' => WebSetting::get(WebSetting::KEY_ABOUT_VISION_TAG . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VISION_TAG)),
            'aboutVisionTitle' => WebSetting::get(WebSetting::KEY_ABOUT_VISION_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VISION_TITLE)),
            'aboutVisionBody' => WebSetting::get(WebSetting::KEY_ABOUT_VISION_BODY . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VISION_BODY)),

            // Mission
            'aboutMissionTag' => WebSetting::get(WebSetting::KEY_ABOUT_MISSION_TAG . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_MISSION_TAG)),
            'aboutMissionTitle' => WebSetting::get(WebSetting::KEY_ABOUT_MISSION_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_MISSION_TITLE)),
            'aboutMissionBody' => WebSetting::get(WebSetting::KEY_ABOUT_MISSION_BODY . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_MISSION_BODY)),

            // Values
            'aboutValuesTag' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_TAG . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_TAG)),
            'aboutValuesTitle' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_TITLE)),
            'aboutValuesDesc' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_DESC)),
            'aboutValuesItem1Title' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_1_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_1_TITLE)),
            'aboutValuesItem1Desc' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_1_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_1_DESC)),
            'aboutValuesItem2Title' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_2_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_2_TITLE)),
            'aboutValuesItem2Desc' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_2_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_2_DESC)),
            'aboutValuesItem3Title' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_3_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_3_TITLE)),
            'aboutValuesItem3Desc' => WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_3_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_VALUES_ITEM_3_DESC)),

            // Highlights
            'aboutHighlights1Title' => WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_TITLE)),
            'aboutHighlights1Desc' => WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_DESC)),
            'aboutHighlights2Title' => WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_TITLE . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_TITLE)),
            'aboutHighlights2Desc' => WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_DESC . $suffix, WebSetting::get(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_DESC)),

            // Car Rental CMS (localized by ?lang=xx)
            'rentalPageTitle' => WebSetting::get(WebSetting::KEY_RENTAL_PAGE_TITLE . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_PAGE_TITLE)),
            'rentalPageMeta' => WebSetting::get(WebSetting::KEY_RENTAL_PAGE_META . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_PAGE_META)),
            'rentalPageKeywords' => WebSetting::get(WebSetting::KEY_RENTAL_PAGE_KEYWORDS . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_PAGE_KEYWORDS)),
            'rentalHeroTag' => WebSetting::get(WebSetting::KEY_RENTAL_HERO_TAG . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_HERO_TAG)),
            'rentalHeroTitle' => WebSetting::get(WebSetting::KEY_RENTAL_HERO_TITLE . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_HERO_TITLE)),
            'rentalHeroDesc' => WebSetting::get(WebSetting::KEY_RENTAL_HERO_DESC . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_HERO_DESC)),
            'rentalCtaTitle' => WebSetting::get(WebSetting::KEY_RENTAL_CTA_TITLE . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_CTA_TITLE)),
            'rentalCtaDesc' => WebSetting::get(WebSetting::KEY_RENTAL_CTA_DESC . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_CTA_DESC)),
            'rentalCtaButton' => WebSetting::get(WebSetting::KEY_RENTAL_CTA_BUTTON . $suffix, WebSetting::get(WebSetting::KEY_RENTAL_CTA_BUTTON)),
        ]);

    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section' => ['nullable', 'string', 'max:40'],
            'lang' => ['nullable', 'string', 'max:10'],
            'hero_background_image' => ['nullable', 'image', 'max:5120'],
            'remove_hero_background' => ['nullable', 'boolean'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],

            // Footer
            'footer_title' => ['nullable', 'string', 'max:80'],
            'footer_description' => ['nullable', 'string', 'max:600'],
            'footer_copyright' => ['nullable', 'string', 'max:160'],
            'footer_payment_methods' => ['nullable', 'string', 'max:800'],

            // Social
            'social_instagram' => ['nullable', 'url', 'max:255'],
            'social_facebook' => ['nullable', 'url', 'max:255'],
            'social_tiktok' => ['nullable', 'url', 'max:255'],
            'social_youtube' => ['nullable', 'url', 'max:255'],

            // Site identity
            'site_name' => ['nullable', 'string', 'max:80'],
            'site_tagline' => ['nullable', 'string', 'max:80'],
            'site_logo' => ['nullable', 'image', 'max:5120'],
            'remove_site_logo' => ['nullable', 'boolean'],
            'login_side_image' => ['nullable', 'image', 'max:5120'],
            'remove_login_side_image' => ['nullable', 'boolean'],



            // About CMS
            'about_tag' => ['nullable', 'string', 'max:60'],
            'about_headline' => ['nullable', 'string', 'max:180'],
            'about_subheadline' => ['nullable', 'string', 'max:240'],
            'about_lead' => ['nullable', 'string', 'max:2000'],
            'about_image' => ['nullable', 'image', 'max:5120'],
            'remove_about_image' => ['nullable', 'boolean'],
            'about_image_alt' => ['nullable', 'string', 'max:140'],
            'about_badge' => ['nullable', 'string', 'max:60'],
            'about_badge_title' => ['nullable', 'string', 'max:140'],
            'about_badge_desc' => ['nullable', 'string', 'max:200'],
            'about_stat_1_value' => ['nullable', 'string', 'max:40'],
            'about_stat_1_label' => ['nullable', 'string', 'max:60'],
            'about_stat_2_value' => ['nullable', 'string', 'max:40'],
            'about_stat_2_label' => ['nullable', 'string', 'max:60'],
            'about_stat_3_value' => ['nullable', 'string', 'max:40'],
            'about_stat_3_label' => ['nullable', 'string', 'max:60'],

            'about_vision_tag' => ['nullable', 'string', 'max:60'],
            'about_vision_title' => ['nullable', 'string', 'max:180'],
            'about_vision_body' => ['nullable', 'string', 'max:1000'],

            'about_mission_tag' => ['nullable', 'string', 'max:60'],
            'about_mission_title' => ['nullable', 'string', 'max:180'],
            'about_mission_body' => ['nullable', 'string', 'max:1000'],

            'about_values_tag' => ['nullable', 'string', 'max:60'],
            'about_values_title' => ['nullable', 'string', 'max:180'],
            'about_values_desc' => ['nullable', 'string', 'max:500'],
            'about_values_item_1_title' => ['nullable', 'string', 'max:100'],
            'about_values_item_1_desc' => ['nullable', 'string', 'max:500'],
            'about_values_item_2_title' => ['nullable', 'string', 'max:100'],
            'about_values_item_2_desc' => ['nullable', 'string', 'max:500'],
            'about_values_item_3_title' => ['nullable', 'string', 'max:100'],
            'about_values_item_3_desc' => ['nullable', 'string', 'max:500'],

            'about_highlights_1_title' => ['nullable', 'string', 'max:100'],
            'about_highlights_1_desc' => ['nullable', 'string', 'max:500'],
            'about_highlights_2_title' => ['nullable', 'string', 'max:100'],
            'about_highlights_2_desc' => ['nullable', 'string', 'max:500'],

            // Car Rental CMS
            'rental_page_title' => ['nullable', 'string', 'max:120'],
            'rental_page_meta' => ['nullable', 'string', 'max:300'],
            'rental_page_keywords' => ['nullable', 'string', 'max:300'],
            'rental_hero_tag' => ['nullable', 'string', 'max:60'],
            'rental_hero_title' => ['nullable', 'string', 'max:180'],
            'rental_hero_desc' => ['nullable', 'string', 'max:1000'],
            'rental_cta_title' => ['nullable', 'string', 'max:120'],
            'rental_cta_desc' => ['nullable', 'string', 'max:600'],
            'rental_cta_button' => ['nullable', 'string', 'max:60'],
        ]);

        $section = (string) ($validated['section'] ?? $request->input('section', 'all'));
        $section = $section !== '' ? $section : 'all';

        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
        $lang = trim((string) ($validated['lang'] ?? $request->input('lang', '')));
        $suffix = '';
        if (in_array($section, ['footer', 'about', 'rental'], true) && in_array($lang, $supportedLocales, true)) {
            $suffix = '.' . $lang;
        }

        $currentPath = WebSetting::get(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE);
        $hasChanges = false;
        $messages = [];

        if ($request->hasFile('hero_background_image')) {
            $file = $request->file('hero_background_image');

            $newPath = Storage::disk('public')->putFile('web-settings', $file);

            if (!empty($currentPath) && $currentPath !== $newPath) {
                try {
                    Storage::disk('public')->delete($currentPath);
                } catch (\Throwable) {
                    // ignore
                }
            }

            WebSetting::set(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE, $newPath);
            $hasChanges = true;
            $messages[] = 'Gambar hero berhasil diperbarui.';
        } else {
            $remove = (bool) ($validated['remove_hero_background'] ?? false);
            if ($remove && !empty($currentPath)) {
                try {
                    Storage::disk('public')->delete($currentPath);
                } catch (\Throwable) {
                    // ignore
                }

                WebSetting::forget(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE);
                $hasChanges = true;
                $messages[] = 'Gambar hero berhasil dihapus.';
            }
        }

        // Jika field tidak dikirim (mis. simpan tab lain), jangan overwrite.
        if (array_key_exists('contact_email', $validated)) {
            $contactEmail = trim((string) ($validated['contact_email'] ?? ''));
            if ($contactEmail !== '') {
                WebSetting::set(WebSetting::KEY_CONTACT_EMAIL, $contactEmail);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_CONTACT_EMAIL);
            }
        }

        if (array_key_exists('contact_phone', $validated)) {
            $contactPhone = trim((string) ($validated['contact_phone'] ?? ''));
            if ($contactPhone !== '') {
                WebSetting::set(WebSetting::KEY_CONTACT_PHONE, $contactPhone);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_CONTACT_PHONE);
            }
        }

        if (array_key_exists('contact_whatsapp', $validated)) {
            $contactWhatsapp = trim((string) ($validated['contact_whatsapp'] ?? ''));
            if ($contactWhatsapp !== '') {
                WebSetting::set(WebSetting::KEY_CONTACT_WHATSAPP, $contactWhatsapp);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_CONTACT_WHATSAPP);
            }
        }

        // Footer (multibahasa jika section=footer dan ada lang)
        if (array_key_exists('footer_title', $validated)) {
            $title = trim((string) ($validated['footer_title'] ?? ''));
            $key = WebSetting::KEY_FOOTER_TITLE . ($section === 'footer' ? $suffix : '');
            if ($title !== '') {
                WebSetting::set($key, $title);
                $hasChanges = true;
            } else {
                WebSetting::forget($key);
            }
        }

        if (array_key_exists('footer_description', $validated)) {
            $desc = trim((string) ($validated['footer_description'] ?? ''));
            $key = WebSetting::KEY_FOOTER_DESCRIPTION . ($section === 'footer' ? $suffix : '');
            if ($desc !== '') {
                WebSetting::set($key, $desc);
                $hasChanges = true;
            } else {
                WebSetting::forget($key);
            }
        }

        if (array_key_exists('footer_copyright', $validated)) {
            $copy = trim((string) ($validated['footer_copyright'] ?? ''));
            $key = WebSetting::KEY_FOOTER_COPYRIGHT . ($section === 'footer' ? $suffix : '');
            if ($copy !== '') {
                WebSetting::set($key, $copy);
                $hasChanges = true;
            } else {
                WebSetting::forget($key);
            }
        }

        if (array_key_exists('footer_payment_methods', $validated)) {
            $methods = trim((string) ($validated['footer_payment_methods'] ?? ''));
            $key = WebSetting::KEY_FOOTER_PAYMENT_METHODS . ($section === 'footer' ? $suffix : '');
            if ($methods !== '') {
                WebSetting::set($key, $methods);
                $hasChanges = true;
            } else {
                WebSetting::forget($key);
            }
        }

        // Social
        if (array_key_exists('social_instagram', $validated)) {
            $url = trim((string) ($validated['social_instagram'] ?? ''));
            if ($url !== '') {
                WebSetting::set(WebSetting::KEY_SOCIAL_INSTAGRAM, $url);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_SOCIAL_INSTAGRAM);
            }
        }
        if (array_key_exists('social_facebook', $validated)) {
            $url = trim((string) ($validated['social_facebook'] ?? ''));
            if ($url !== '') {
                WebSetting::set(WebSetting::KEY_SOCIAL_FACEBOOK, $url);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_SOCIAL_FACEBOOK);
            }
        }
        if (array_key_exists('social_tiktok', $validated)) {
            $url = trim((string) ($validated['social_tiktok'] ?? ''));
            if ($url !== '') {
                WebSetting::set(WebSetting::KEY_SOCIAL_TIKTOK, $url);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_SOCIAL_TIKTOK);
            }
        }
        if (array_key_exists('social_youtube', $validated)) {
            $url = trim((string) ($validated['social_youtube'] ?? ''));
            if ($url !== '') {
                WebSetting::set(WebSetting::KEY_SOCIAL_YOUTUBE, $url);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_SOCIAL_YOUTUBE);
            }
        }

        // Site Identity
        if (array_key_exists('site_name', $validated)) {
            $name = trim((string) ($validated['site_name'] ?? ''));
            if ($name !== '') {
                WebSetting::set(WebSetting::KEY_SITE_NAME, $name);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_SITE_NAME);
            }
        }

        if (array_key_exists('site_tagline', $validated)) {
            $tagline = trim((string) ($validated['site_tagline'] ?? ''));
            if ($tagline !== '') {
                WebSetting::set(WebSetting::KEY_SITE_TAGLINE, $tagline);
                $hasChanges = true;
            } else {
                WebSetting::forget(WebSetting::KEY_SITE_TAGLINE);
            }
        }

        $currentLogoPath = WebSetting::get(WebSetting::KEY_SITE_LOGO);
        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $newPath = Storage::disk('public')->putFile('web-settings/identity', $file);

            if (!empty($currentLogoPath) && $currentLogoPath !== $newPath) {
                try {
                    Storage::disk('public')->delete($currentLogoPath);
                } catch (\Throwable) {
                    // ignore
                }
            }

            WebSetting::set(WebSetting::KEY_SITE_LOGO, $newPath);
            $hasChanges = true;
            $messages[] = 'Logo website berhasil diperbarui.';
        } else {
            $remove = (bool) ($validated['remove_site_logo'] ?? false);
            if ($remove && !empty($currentLogoPath)) {
                try {
                    Storage::disk('public')->delete($currentLogoPath);
                } catch (\Throwable) {
                    // ignore
                }

                WebSetting::forget(WebSetting::KEY_SITE_LOGO);
                $hasChanges = true;
                $messages[] = 'Logo website berhasil dihapus.';
            }
        }

        $currentLoginSideImagePath = WebSetting::get(WebSetting::KEY_LOGIN_SIDE_IMAGE);
        if ($request->hasFile('login_side_image')) {
            $file = $request->file('login_side_image');
            $newPath = Storage::disk('public')->putFile('web-settings/identity', $file);

            if (!empty($currentLoginSideImagePath) && $currentLoginSideImagePath !== $newPath) {
                try {
                    Storage::disk('public')->delete($currentLoginSideImagePath);
                } catch (\Throwable) {
                    // ignore
                }
            }

            WebSetting::set(WebSetting::KEY_LOGIN_SIDE_IMAGE, $newPath);
            $hasChanges = true;
            $messages[] = 'Gambar samping login berhasil diperbarui.';
        } else {
            $removeLoginImage = (bool) ($validated['remove_login_side_image'] ?? false);
            if ($removeLoginImage && !empty($currentLoginSideImagePath)) {
                try {
                    Storage::disk('public')->delete($currentLoginSideImagePath);
                } catch (\Throwable) {
                    // ignore
                }

                WebSetting::forget(WebSetting::KEY_LOGIN_SIDE_IMAGE);
                $hasChanges = true;
                $messages[] = 'Gambar samping login berhasil dihapus.';
            }
        }


        // About CMS
        $currentAboutImagePath = WebSetting::get(WebSetting::KEY_ABOUT_IMAGE);

        if ($request->hasFile('about_image')) {
            $file = $request->file('about_image');
            $newPath = Storage::disk('public')->putFile('web-settings/about', $file);

            if (!empty($currentAboutImagePath) && $currentAboutImagePath !== $newPath) {
                try {
                    Storage::disk('public')->delete($currentAboutImagePath);
                } catch (\Throwable) {
                    // ignore
                }
            }

            WebSetting::set(WebSetting::KEY_ABOUT_IMAGE, $newPath);
            $hasChanges = true;
            $messages[] = 'Gambar About Us berhasil diperbarui.';
        } else {
            $remove = (bool) ($validated['remove_about_image'] ?? false);
            if ($remove && !empty($currentAboutImagePath)) {
                try {
                    Storage::disk('public')->delete($currentAboutImagePath);
                } catch (\Throwable) {
                    // ignore
                }

                WebSetting::forget(WebSetting::KEY_ABOUT_IMAGE);
                $hasChanges = true;
                $messages[] = 'Gambar About Us berhasil dihapus.';
            }
        }

        $setOrForget = function (string $key, string $input) use (&$hasChanges): void {
            $val = trim($input);
            if ($val !== '') {
                WebSetting::set($key, $val);
                $hasChanges = true;
            } else {
                WebSetting::forget($key);
            }
        };

        $aboutKey = function (string $baseKey) use ($section, $suffix): string {
            return $baseKey . ($section === 'about' ? $suffix : '');
        };

        if (array_key_exists('about_tag', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_TAG), (string) ($validated['about_tag'] ?? ''));
        }
        if (array_key_exists('about_headline', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_HEADLINE), (string) ($validated['about_headline'] ?? ''));
        }
        if (array_key_exists('about_subheadline', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_SUBHEADLINE), (string) ($validated['about_subheadline'] ?? ''));
        }
        if (array_key_exists('about_lead', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_LEAD), (string) ($validated['about_lead'] ?? ''));
        }
        if (array_key_exists('about_image_alt', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_IMAGE_ALT), (string) ($validated['about_image_alt'] ?? ''));
        }
        if (array_key_exists('about_badge', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_BADGE), (string) ($validated['about_badge'] ?? ''));
        }
        if (array_key_exists('about_badge_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_BADGE_TITLE), (string) ($validated['about_badge_title'] ?? ''));
        }
        if (array_key_exists('about_badge_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_BADGE_DESC), (string) ($validated['about_badge_desc'] ?? ''));
        }

        if (array_key_exists('about_stat_1_value', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_STAT_1_VALUE), (string) ($validated['about_stat_1_value'] ?? ''));
        }
        if (array_key_exists('about_stat_1_label', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_STAT_1_LABEL), (string) ($validated['about_stat_1_label'] ?? ''));
        }
        if (array_key_exists('about_stat_2_value', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_STAT_2_VALUE), (string) ($validated['about_stat_2_value'] ?? ''));
        }
        if (array_key_exists('about_stat_2_label', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_STAT_2_LABEL), (string) ($validated['about_stat_2_label'] ?? ''));
        }
        if (array_key_exists('about_stat_3_value', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_STAT_3_VALUE), (string) ($validated['about_stat_3_value'] ?? ''));
        }
        if (array_key_exists('about_stat_3_label', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_STAT_3_LABEL), (string) ($validated['about_stat_3_label'] ?? ''));
        }

        if (array_key_exists('about_vision_tag', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VISION_TAG), (string) ($validated['about_vision_tag'] ?? ''));
        }
        if (array_key_exists('about_vision_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VISION_TITLE), (string) ($validated['about_vision_title'] ?? ''));
        }
        if (array_key_exists('about_vision_body', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VISION_BODY), (string) ($validated['about_vision_body'] ?? ''));
        }

        if (array_key_exists('about_mission_tag', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_MISSION_TAG), (string) ($validated['about_mission_tag'] ?? ''));
        }
        if (array_key_exists('about_mission_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_MISSION_TITLE), (string) ($validated['about_mission_title'] ?? ''));
        }
        if (array_key_exists('about_mission_body', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_MISSION_BODY), (string) ($validated['about_mission_body'] ?? ''));
        }

        if (array_key_exists('about_values_tag', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_TAG), (string) ($validated['about_values_tag'] ?? ''));
        }
        if (array_key_exists('about_values_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_TITLE), (string) ($validated['about_values_title'] ?? ''));
        }
        if (array_key_exists('about_values_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_DESC), (string) ($validated['about_values_desc'] ?? ''));
        }

        if (array_key_exists('about_values_item_1_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_ITEM_1_TITLE), (string) ($validated['about_values_item_1_title'] ?? ''));
        }
        if (array_key_exists('about_values_item_1_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_ITEM_1_DESC), (string) ($validated['about_values_item_1_desc'] ?? ''));
        }
        if (array_key_exists('about_values_item_2_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_ITEM_2_TITLE), (string) ($validated['about_values_item_2_title'] ?? ''));
        }
        if (array_key_exists('about_values_item_2_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_ITEM_2_DESC), (string) ($validated['about_values_item_2_desc'] ?? ''));
        }
        if (array_key_exists('about_values_item_3_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_ITEM_3_TITLE), (string) ($validated['about_values_item_3_title'] ?? ''));
        }
        if (array_key_exists('about_values_item_3_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_VALUES_ITEM_3_DESC), (string) ($validated['about_values_item_3_desc'] ?? ''));
        }

        if (array_key_exists('about_highlights_1_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_TITLE), (string) ($validated['about_highlights_1_title'] ?? ''));
        }
        if (array_key_exists('about_highlights_1_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_HIGHLIGHTS_1_DESC), (string) ($validated['about_highlights_1_desc'] ?? ''));
        }
        if (array_key_exists('about_highlights_2_title', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_TITLE), (string) ($validated['about_highlights_2_title'] ?? ''));
        }
        if (array_key_exists('about_highlights_2_desc', $validated)) {
            $setOrForget($aboutKey(WebSetting::KEY_ABOUT_HIGHLIGHTS_2_DESC), (string) ($validated['about_highlights_2_desc'] ?? ''));
        }

        // Car Rental CMS
        $rentalKey = function (string $baseKey) use ($section, $suffix): string {
            return $baseKey . ($section === 'rental' ? $suffix : '');
        };

        if (array_key_exists('rental_page_title', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_PAGE_TITLE), (string) ($validated['rental_page_title'] ?? ''));
        }
        if (array_key_exists('rental_page_meta', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_PAGE_META), (string) ($validated['rental_page_meta'] ?? ''));
        }
        if (array_key_exists('rental_page_keywords', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_PAGE_KEYWORDS), (string) ($validated['rental_page_keywords'] ?? ''));
        }
        if (array_key_exists('rental_hero_tag', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_HERO_TAG), (string) ($validated['rental_hero_tag'] ?? ''));
        }
        if (array_key_exists('rental_hero_title', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_HERO_TITLE), (string) ($validated['rental_hero_title'] ?? ''));
        }
        if (array_key_exists('rental_hero_desc', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_HERO_DESC), (string) ($validated['rental_hero_desc'] ?? ''));
        }
        if (array_key_exists('rental_cta_title', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_CTA_TITLE), (string) ($validated['rental_cta_title'] ?? ''));
        }
        if (array_key_exists('rental_cta_desc', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_CTA_DESC), (string) ($validated['rental_cta_desc'] ?? ''));
        }
        if (array_key_exists('rental_cta_button', $validated)) {
            $setOrForget($rentalKey(WebSetting::KEY_RENTAL_CTA_BUTTON), (string) ($validated['rental_cta_button'] ?? ''));
        }

        if (!$hasChanges) {
            $redirectParams = ['section' => $section];
            if (in_array($section, ['footer', 'about', 'rental'], true) && $suffix !== '') {
                $redirectParams['lang'] = $lang;
            }

            return redirect()->route('admin.web-settings.edit', $redirectParams)->with('status', 'Tidak ada perubahan.');
        }


        $status = !empty($messages) ? implode(' ', $messages) : 'Pengaturan website berhasil disimpan.';

        $redirectParams = ['section' => $section];
        if (in_array($section, ['footer', 'about', 'rental'], true) && $suffix !== '') {
            $redirectParams['lang'] = $lang;
        }

        return redirect()->route('admin.web-settings.edit', $redirectParams)->with('status', $status);
    }

}
