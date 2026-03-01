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

        return view('admin.web-settings.edit', [
            'heroBackgroundPath' => $heroBackgroundPath,
            'heroBackgroundUrl' => $heroBackgroundUrl,
            'contactEmail' => $contactEmail,
            'contactPhone' => $contactPhone,
            'contactWhatsapp' => $contactWhatsapp,

            // About CMS
            'aboutTag' => WebSetting::get(WebSetting::KEY_ABOUT_TAG),
            'aboutHeadline' => WebSetting::get(WebSetting::KEY_ABOUT_HEADLINE),
            'aboutSubheadline' => WebSetting::get(WebSetting::KEY_ABOUT_SUBHEADLINE),
            'aboutLead' => WebSetting::get(WebSetting::KEY_ABOUT_LEAD),
            'aboutImagePath' => $aboutImagePath,
            'aboutImageUrl' => $aboutImageUrl,
            'aboutImageAlt' => WebSetting::get(WebSetting::KEY_ABOUT_IMAGE_ALT),
            'aboutBadge' => WebSetting::get(WebSetting::KEY_ABOUT_BADGE),
            'aboutBadgeTitle' => WebSetting::get(WebSetting::KEY_ABOUT_BADGE_TITLE),
            'aboutBadgeDesc' => WebSetting::get(WebSetting::KEY_ABOUT_BADGE_DESC),
            'aboutStat1Value' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_VALUE),
            'aboutStat1Label' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_1_LABEL),
            'aboutStat2Value' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_VALUE),
            'aboutStat2Label' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_2_LABEL),
            'aboutStat3Value' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_VALUE),
            'aboutStat3Label' => WebSetting::get(WebSetting::KEY_ABOUT_STAT_3_LABEL),
        ]);

    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'section' => ['nullable', 'string', 'max:40'],
            'hero_background_image' => ['nullable', 'image', 'max:5120'],
            'remove_hero_background' => ['nullable', 'boolean'],
            'contact_email' => ['nullable', 'email', 'max:120'],
            'contact_phone' => ['nullable', 'string', 'max:50'],
            'contact_whatsapp' => ['nullable', 'string', 'max:50'],

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
        ]);

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

        if (array_key_exists('about_tag', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_TAG, (string) ($validated['about_tag'] ?? ''));
        }
        if (array_key_exists('about_headline', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_HEADLINE, (string) ($validated['about_headline'] ?? ''));
        }
        if (array_key_exists('about_subheadline', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_SUBHEADLINE, (string) ($validated['about_subheadline'] ?? ''));
        }
        if (array_key_exists('about_lead', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_LEAD, (string) ($validated['about_lead'] ?? ''));
        }
        if (array_key_exists('about_image_alt', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_IMAGE_ALT, (string) ($validated['about_image_alt'] ?? ''));
        }
        if (array_key_exists('about_badge', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_BADGE, (string) ($validated['about_badge'] ?? ''));
        }
        if (array_key_exists('about_badge_title', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_BADGE_TITLE, (string) ($validated['about_badge_title'] ?? ''));
        }
        if (array_key_exists('about_badge_desc', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_BADGE_DESC, (string) ($validated['about_badge_desc'] ?? ''));
        }

        if (array_key_exists('about_stat_1_value', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_STAT_1_VALUE, (string) ($validated['about_stat_1_value'] ?? ''));
        }
        if (array_key_exists('about_stat_1_label', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_STAT_1_LABEL, (string) ($validated['about_stat_1_label'] ?? ''));
        }
        if (array_key_exists('about_stat_2_value', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_STAT_2_VALUE, (string) ($validated['about_stat_2_value'] ?? ''));
        }
        if (array_key_exists('about_stat_2_label', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_STAT_2_LABEL, (string) ($validated['about_stat_2_label'] ?? ''));
        }
        if (array_key_exists('about_stat_3_value', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_STAT_3_VALUE, (string) ($validated['about_stat_3_value'] ?? ''));
        }
        if (array_key_exists('about_stat_3_label', $validated)) {
            $setOrForget(WebSetting::KEY_ABOUT_STAT_3_LABEL, (string) ($validated['about_stat_3_label'] ?? ''));
        }

        $section = (string) ($validated['section'] ?? $request->input('section', 'all'));
        $section = $section !== '' ? $section : 'all';

        if (!$hasChanges) {
            return redirect()->route('admin.web-settings.edit', ['section' => $section])->with('status', 'Tidak ada perubahan.');
        }

        $status = !empty($messages) ? implode(' ', $messages) : 'Pengaturan website berhasil disimpan.';

        return redirect()->route('admin.web-settings.edit', ['section' => $section])->with('status', $status);
    }

}
