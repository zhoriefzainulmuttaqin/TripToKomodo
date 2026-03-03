<?php

namespace App\Http\Controllers\Admin;

use App\Models\WebSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RentalController
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

        $suffix = '.' . $editLang;

        return view('admin.rental.edit', [
            'editLang' => $editLang,

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
        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];

        $validated = $request->validate([
            'lang' => ['nullable', 'string', 'max:10'],

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

        $lang = trim((string) ($validated['lang'] ?? $request->input('lang', '')));
        if (!in_array($lang, $supportedLocales, true)) {
            $lang = (string) (session('locale') ?? config('app.locale', 'en'));
        }
        if (!in_array($lang, $supportedLocales, true)) {
            $lang = 'en';
        }

        $suffix = '.' . $lang;

        $hasChanges = false;

        $setOrForget = function (string $key, string $input) use (&$hasChanges): void {
            $val = trim($input);
            if ($val !== '') {
                WebSetting::set($key, $val);
                $hasChanges = true;
            } else {
                WebSetting::forget($key);
            }
        };

        if (array_key_exists('rental_page_title', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_PAGE_TITLE . $suffix, (string) ($validated['rental_page_title'] ?? ''));
        }
        if (array_key_exists('rental_page_meta', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_PAGE_META . $suffix, (string) ($validated['rental_page_meta'] ?? ''));
        }
        if (array_key_exists('rental_page_keywords', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_PAGE_KEYWORDS . $suffix, (string) ($validated['rental_page_keywords'] ?? ''));
        }

        if (array_key_exists('rental_hero_tag', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_HERO_TAG . $suffix, (string) ($validated['rental_hero_tag'] ?? ''));
        }
        if (array_key_exists('rental_hero_title', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_HERO_TITLE . $suffix, (string) ($validated['rental_hero_title'] ?? ''));
        }
        if (array_key_exists('rental_hero_desc', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_HERO_DESC . $suffix, (string) ($validated['rental_hero_desc'] ?? ''));
        }

        if (array_key_exists('rental_cta_title', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_CTA_TITLE . $suffix, (string) ($validated['rental_cta_title'] ?? ''));
        }
        if (array_key_exists('rental_cta_desc', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_CTA_DESC . $suffix, (string) ($validated['rental_cta_desc'] ?? ''));
        }
        if (array_key_exists('rental_cta_button', $validated)) {
            $setOrForget(WebSetting::KEY_RENTAL_CTA_BUTTON . $suffix, (string) ($validated['rental_cta_button'] ?? ''));
        }

        $status = $hasChanges ? 'Rental mobil berhasil disimpan.' : 'Tidak ada perubahan.';

        return redirect()
            ->route('admin.rental.edit', ['lang' => $lang])
            ->with('status', $status);
    }
}
