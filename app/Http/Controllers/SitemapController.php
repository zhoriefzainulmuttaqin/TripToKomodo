<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Language;
use App\Models\RentalCarTranslation;
use App\Models\TourPackageTranslation;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(?string $lang = null): Response
    {
        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];

        if ($lang !== null) {
            $lang = strtolower($lang);
            if (!in_array($lang, $supportedLocales, true)) {
                abort(404);
            }
        }

        try {
            $languages = $lang
                ? Language::query()->where('code', $lang)->get(['code'])
                : Language::query()->where('is_active', true)->get(['code']);
        } catch (\Throwable) {
            $languages = collect();
        }

        if ($languages->isEmpty()) {
            $languages = collect($supportedLocales)->map(fn ($code) => (object) ['code' => $code]);
            if ($lang !== null) {
                $languages = $languages->filter(fn ($l) => $l->code === $lang)->values();
            }
        }

        $urls = [];

        foreach ($languages as $language) {
            $code = (string) ($language->code ?? 'en');

            // Static pages
            $urls[] = ['loc' => url($code), 'lastmod' => null];
            $urls[] = ['loc' => url($code . '/tours'), 'lastmod' => null];
            $urls[] = ['loc' => url($code . '/about'), 'lastmod' => null];
            $urls[] = ['loc' => url($code . '/contact'), 'lastmod' => null];
            $urls[] = ['loc' => url($code . '/komodo-insider'), 'lastmod' => null];
            $urls[] = ['loc' => url($code . '/rental-mobil'), 'lastmod' => null];

            // Tour detail pages
            try {
                $translations = TourPackageTranslation::query()
                    ->where('language_code', $code)
                    ->where('is_active', true)
                    ->get(['slug', 'updated_at']);
            } catch (\Throwable) {
                $translations = collect();
            }

            foreach ($translations as $translation) {
                $slug = (string) ($translation->slug ?? '');
                if ($slug === '') {
                    continue;
                }

                $lastmod = null;
                try {
                    $lastmod = $translation->updated_at?->toAtomString();
                } catch (\Throwable) {
                    $lastmod = null;
                }

                $urls[] = [
                    'loc' => url($code . '/tours/' . $slug),
                    'lastmod' => $lastmod,
                ];
            }

            // Rental car detail pages
            try {
                $rentalTranslations = RentalCarTranslation::query()
                    ->where('language_code', $code)
                    ->where('is_active', true)
                    ->get(['slug', 'updated_at']);
            } catch (\Throwable) {
                $rentalTranslations = collect();
            }

            foreach ($rentalTranslations as $translation) {
                $slug = (string) ($translation->slug ?? '');
                if ($slug === '') {
                    continue;
                }

                $lastmod = null;
                try {
                    $lastmod = $translation->updated_at?->toAtomString();
                } catch (\Throwable) {
                    $lastmod = null;
                }

                $urls[] = [
                    'loc' => url($code . '/rental-mobil/' . $slug),
                    'lastmod' => $lastmod,
                ];
            }

            // Blog post pages
            try {
                $posts = BlogPost::query()
                    ->where('language_code', $code)
                    ->where('is_published', true)
                    ->get(['slug', 'updated_at']);
            } catch (\Throwable) {
                $posts = collect();
            }

            foreach ($posts as $post) {
                $slug = (string) ($post->slug ?? '');
                if ($slug === '') {
                    continue;
                }

                $lastmod = null;
                try {
                    $lastmod = $post->updated_at?->toAtomString();
                } catch (\Throwable) {
                    $lastmod = null;
                }

                $urls[] = [
                    'loc' => url($code . '/komodo-insider/' . $slug),
                    'lastmod' => $lastmod,
                ];
            }
        }

        $xml = $this->buildXml($urls);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * @param  array<int, array{loc:string,lastmod:?string}>  $urls
     */
    protected function buildXml(array $urls): string
    {
        $items = array_map(function (array $item): string {
            $loc = htmlspecialchars($item['loc'], ENT_XML1);
            $lastmod = $item['lastmod'] ?? null;

            $xml = '<url><loc>' . $loc . '</loc>';
            if (!empty($lastmod)) {
                $xml .= '<lastmod>' . htmlspecialchars((string) $lastmod, ENT_XML1) . '</lastmod>';
            }
            $xml .= '</url>';

            return $xml;
        }, $urls);

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . implode('', $items)
            . '</urlset>';
    }
}
