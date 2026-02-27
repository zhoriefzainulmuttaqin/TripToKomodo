<?php

namespace App\Services;

use App\Models\TourPackage;
use Illuminate\Support\Str;

class SeoService
{
    public function canonical(string $path): string
    {
        return url($path);
    }

    public function breadcrumbSchema(array $items): array
    {
        $list = [];
        foreach ($items as $index => $item) {
            $list[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $list,
        ];
    }

    public function tourStructuredData(TourPackage $package, array $data): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $data['title'] ?? $package->code,
            'description' => Str::limit(strip_tags($data['description'] ?? ''), 160, ''),
            'touristType' => 'Leisure',
            'offers' => [
                '@type' => 'Offer',
                'priceCurrency' => $data['currency_code'] ?? 'IDR',
                'price' => $data['price'] ?? $package->base_price_idr,
                'availability' => 'https://schema.org/InStock',
                'url' => $data['url'] ?? url('/'),
            ],
        ];
    }

    public function faqSchema(array $faqs): array
    {
        $items = array_map(function ($faq): array {
            return [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }, $faqs);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items,
        ];
    }

    public function reviewSchema(array $rating): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'AggregateRating',
            'ratingValue' => $rating['value'],
            'reviewCount' => $rating['count'],
        ];
    }
}
