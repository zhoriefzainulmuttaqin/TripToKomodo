<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Language;
use App\Models\RentalCarTranslation;
use App\Models\TourPackageTranslation;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(?string $lang = null): Response
    {
        $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];
        $fallbackLocale = (string) config('app.fallback_locale', 'en');

        if ($lang !== null) {
            $lang = strtolower($lang);
            if (!in_array($lang, $supportedLocales, true)) {
                abort(404);
            }
        }

        // Languages list (from DB, fallback to supportedLocales).
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

        $languageCodes = $languages
            ->map(fn ($l) => strtolower((string) ($l->code ?? '')))
            ->filter(fn ($c) => $c !== '')
            ->unique()
            ->values()
            ->all();

        // Main sitemap (no lang param): include hreflang alternates.
        if ($lang === null) {
            $entries = $this->buildAlternateEntries($languageCodes, $fallbackLocale);
            $xml = $this->buildXml($entries);

            return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
        }

        // Language-specific sitemap: keep it lightweight.
        $entries = $this->buildSimpleEntries($languageCodes);
        $xml = $this->buildXml($entries);

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    /**
     * @param  array<int, string>  $languageCodes
     * @return array<int, array{loc:string,lastmod:?string}>
     */
    protected function buildSimpleEntries(array $languageCodes): array
    {
        $urls = [];

        foreach ($languageCodes as $code) {
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

                $urls[] = [
                    'loc' => url($code . '/tours/' . $slug),
                    'lastmod' => $this->toAtomString($translation->updated_at ?? null),
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

                $urls[] = [
                    'loc' => url($code . '/rental-mobil/' . $slug),
                    'lastmod' => $this->toAtomString($translation->updated_at ?? null),
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

                $urls[] = [
                    'loc' => url($code . '/komodo-insider/' . $slug),
                    'lastmod' => $this->toAtomString($post->updated_at ?? null),
                ];
            }
        }

        return $urls;
    }

    /**
     * @param  array<int, string>  $languageCodes
     * @return array<int, array{loc:string,lastmod:?string,alternates:array<int,array{hreflang:string,href:string}>}>
     */
    protected function buildAlternateEntries(array $languageCodes, string $fallbackLocale): array
    {
        $fallbackLocale = strtolower($fallbackLocale);
        if (!in_array($fallbackLocale, $languageCodes, true) && !empty($languageCodes)) {
            $fallbackLocale = $languageCodes[0];
        }

        // Latest update signals for list pages.
        $lastmodTours = null;
        $lastmodRentals = null;
        $lastmodPosts = null;

        try {
            $lastmodTours = $this->toAtomString(TourPackageTranslation::query()->where('is_active', true)->orderByDesc('updated_at')->value('updated_at'));
        } catch (\Throwable) {
            $lastmodTours = null;
        }

        try {
            $lastmodRentals = $this->toAtomString(RentalCarTranslation::query()->where('is_active', true)->orderByDesc('updated_at')->value('updated_at'));
        } catch (\Throwable) {
            $lastmodRentals = null;
        }

        try {
            $lastmodPosts = $this->toAtomString(BlogPost::query()->where('is_published', true)->orderByDesc('updated_at')->value('updated_at'));
        } catch (\Throwable) {
            $lastmodPosts = null;
        }

        $lastmodHome = $this->maxAtom([$lastmodTours, $lastmodRentals, $lastmodPosts]);

        $entries = [];

        // Static / list pages as groups.
        $static = [
            ['path' => '', 'lastmod' => $lastmodHome],
            ['path' => 'tours', 'lastmod' => $lastmodTours],
            ['path' => 'about', 'lastmod' => null],
            ['path' => 'contact', 'lastmod' => null],
            ['path' => 'komodo-insider', 'lastmod' => $lastmodPosts],
            ['path' => 'rental-mobil', 'lastmod' => $lastmodRentals],
        ];

        foreach ($static as $page) {
            $alternates = [];
            foreach ($languageCodes as $code) {
                $href = $page['path'] === ''
                    ? url($code)
                    : url($code . '/' . $page['path']);

                $alternates[] = ['hreflang' => $code, 'href' => $href];
            }

            $loc = $page['path'] === ''
                ? url($fallbackLocale)
                : url($fallbackLocale . '/' . $page['path']);

            $alternates[] = ['hreflang' => 'x-default', 'href' => $loc];


            $entries[] = [
                'loc' => $loc,
                'lastmod' => $page['lastmod'],
                'alternates' => $alternates,
            ];
        }

        // Tour detail pages grouped by tour_package_id.
        try {
            $tourTranslations = TourPackageTranslation::query()
                ->whereIn('language_code', $languageCodes)
                ->where('is_active', true)
                ->get(['tour_package_id', 'language_code', 'slug', 'updated_at']);
        } catch (\Throwable) {
            $tourTranslations = collect();
        }

        $tourGroups = $tourTranslations->groupBy('tour_package_id');
        foreach ($tourGroups as $items) {
            $alternates = [];
            $lastmodCandidates = [];
            $fallbackHref = null;

            foreach ($items as $t) {
                $code = strtolower((string) ($t->language_code ?? ''));
                $slug = (string) ($t->slug ?? '');
                if ($code === '' || $slug === '') {
                    continue;
                }

                $href = url($code . '/tours/' . $slug);
                $alternates[] = ['hreflang' => $code, 'href' => $href];
                $lastmodCandidates[] = $this->toAtomString($t->updated_at ?? null);

                if ($code === $fallbackLocale) {
                    $fallbackHref = $href;
                }
            }

            if (empty($alternates)) {
                continue;
            }

            $loc = $fallbackHref ?? $alternates[0]['href'];
            $alternates[] = ['hreflang' => 'x-default', 'href' => $loc];

            $entries[] = [
                'loc' => $loc,
                'lastmod' => $this->maxAtom($lastmodCandidates),
                'alternates' => $alternates,
            ];
        }

        // Rental car detail pages grouped by rental_car_id.
        try {
            $rentalTranslations = RentalCarTranslation::query()
                ->whereIn('language_code', $languageCodes)
                ->where('is_active', true)
                ->get(['rental_car_id', 'language_code', 'slug', 'updated_at']);
        } catch (\Throwable) {
            $rentalTranslations = collect();
        }

        $rentalGroups = $rentalTranslations->groupBy('rental_car_id');
        foreach ($rentalGroups as $items) {
            $alternates = [];
            $lastmodCandidates = [];
            $fallbackHref = null;

            foreach ($items as $t) {
                $code = strtolower((string) ($t->language_code ?? ''));
                $slug = (string) ($t->slug ?? '');
                if ($code === '' || $slug === '') {
                    continue;
                }

                $href = url($code . '/rental-mobil/' . $slug);
                $alternates[] = ['hreflang' => $code, 'href' => $href];
                $lastmodCandidates[] = $this->toAtomString($t->updated_at ?? null);

                if ($code === $fallbackLocale) {
                    $fallbackHref = $href;
                }
            }

            if (empty($alternates)) {
                continue;
            }

            $loc = $fallbackHref ?? $alternates[0]['href'];
            $alternates[] = ['hreflang' => 'x-default', 'href' => $loc];

            $entries[] = [
                'loc' => $loc,
                'lastmod' => $this->maxAtom($lastmodCandidates),
                'alternates' => $alternates,
            ];
        }

        // Blog posts grouped by group_key (fallback to id if missing).
        try {
            $posts = BlogPost::query()
                ->whereIn('language_code', $languageCodes)
                ->where('is_published', true)
                ->get(['id', 'group_key', 'language_code', 'slug', 'updated_at']);
        } catch (\Throwable) {
            $posts = collect();
        }

        $postGroups = $posts->groupBy(function ($p) {
            $key = (string) ($p->group_key ?? '');
            if ($key !== '') {
                return $key;
            }

            return 'id:' . (string) ($p->id ?? '0');
        });

        foreach ($postGroups as $items) {
            $alternates = [];
            $lastmodCandidates = [];
            $fallbackHref = null;

            foreach ($items as $p) {
                $code = strtolower((string) ($p->language_code ?? ''));
                $slug = (string) ($p->slug ?? '');
                if ($code === '' || $slug === '') {
                    continue;
                }

                $href = url($code . '/komodo-insider/' . $slug);
                $alternates[] = ['hreflang' => $code, 'href' => $href];
                $lastmodCandidates[] = $this->toAtomString($p->updated_at ?? null);

                if ($code === $fallbackLocale) {
                    $fallbackHref = $href;
                }
            }

            if (empty($alternates)) {
                continue;
            }

            $loc = $fallbackHref ?? $alternates[0]['href'];
            $alternates[] = ['hreflang' => 'x-default', 'href' => $loc];

            $entries[] = [
                'loc' => $loc,
                'lastmod' => $this->maxAtom($lastmodCandidates),
                'alternates' => $alternates,
            ];
        }

        return $entries;
    }

    protected function toAtomString($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if ($value instanceof \DateTimeInterface) {
                return CarbonImmutable::instance($value)->toAtomString();
            }

            return CarbonImmutable::parse((string) $value)->toAtomString();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @param  array<int, ?string>  $atoms
     */
    protected function maxAtom(array $atoms): ?string
    {
        $best = null;

        foreach ($atoms as $atom) {
            if (empty($atom)) {
                continue;
            }

            if ($best === null) {
                $best = $atom;
                continue;
            }

            if ($atom > $best) {
                $best = $atom;
            }
        }

        return $best;
    }

    /**
     * @param  array<int, array{loc:string,lastmod:?string,alternates?:array<int,array{hreflang:string,href:string}>}>  $entries
     */
    protected function buildXml(array $entries): string
    {
        $hasAlternates = false;
        foreach ($entries as $e) {
            if (!empty($e['alternates'])) {
                $hasAlternates = true;
                break;
            }
        }

        $items = array_map(function (array $item): string {
            $loc = htmlspecialchars($item['loc'], ENT_XML1);
            $lastmod = $item['lastmod'] ?? null;
            $alternates = $item['alternates'] ?? [];

            $xml = '<url><loc>' . $loc . '</loc>';

            foreach ($alternates as $alt) {
                $hreflang = htmlspecialchars((string) ($alt['hreflang'] ?? ''), ENT_XML1);
                $href = htmlspecialchars((string) ($alt['href'] ?? ''), ENT_XML1);
                if ($hreflang === '' || $href === '') {
                    continue;
                }

                $xml .= '<xhtml:link rel="alternate" hreflang="' . $hreflang . '" href="' . $href . '"/>';
            }

            if (!empty($lastmod)) {
                $xml .= '<lastmod>' . htmlspecialchars((string) $lastmod, ENT_XML1) . '</lastmod>';
            }

            $xml .= '</url>';

            return $xml;
        }, $entries);

        $xmlns = 'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        if ($hasAlternates) {
            $xmlns .= ' xmlns:xhtml="http://www.w3.org/1999/xhtml"';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset ' . $xmlns . '>'
            . implode('', $items)
            . '</urlset>';
    }
}
