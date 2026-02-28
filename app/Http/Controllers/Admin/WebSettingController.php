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



        return view('admin.web-settings.edit', [
            'heroBackgroundPath' => $heroBackgroundPath,
            'heroBackgroundUrl' => $heroBackgroundUrl,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_background_image' => ['nullable', 'image', 'max:5120'],
            'remove_hero_background' => ['nullable', 'boolean'],
        ]);

        $currentPath = WebSetting::get(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE);

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

            return redirect()->route('admin.web-settings.edit')->with('status', 'Gambar hero berhasil diperbarui.');
        }

        $remove = (bool) ($validated['remove_hero_background'] ?? false);
        if ($remove && !empty($currentPath)) {
            try {
                Storage::disk('public')->delete($currentPath);
            } catch (\Throwable) {
                // ignore
            }

            WebSetting::forget(WebSetting::KEY_HOME_HERO_BACKGROUND_IMAGE);

            return redirect()->route('admin.web-settings.edit')->with('status', 'Gambar hero berhasil dihapus.');
        }

        return redirect()->route('admin.web-settings.edit')->with('status', 'Tidak ada perubahan.');
    }
}
