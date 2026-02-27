<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\TourPackageTranslation;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(?string $lang = null): Response
    {
        $languages = $lang
            ? Language::query()->where('code', $lang)->get()
            : Language::query()->where('is_active', true)->get();

        $urls = [];
        foreach ($languages as $language) {
            $translations = TourPackageTranslation::query()
                ->where('language_code', $language->code)
                ->get(['slug']);

            foreach ($translations as $translation) {
                $urls[] = url($language->code . '/tours/' . $translation->slug);
            }
        }

        $xml = $this->buildXml($urls);

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    protected function buildXml(array $urls): string
    {
        $items = array_map(function (string $url): string {
            return "<url><loc>{$url}</loc></url>";
        }, $urls);

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . implode('', $items)
            . '</urlset>';
    }
}
