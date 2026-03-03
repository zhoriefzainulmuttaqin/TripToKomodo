<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Services\InternalLinkService;
use App\Services\SeoService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(string $lang): View
    {
        $lang = strtolower($lang);
        $locale = app()->getLocale();

        // Keep locale consistent with lang prefix
        if ($locale !== $lang) {
            app()->setLocale($lang);
        }

        $query = BlogPost::query()
            ->published()
            ->where('language_code', $lang)
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        $posts = $query->paginate(9)->withQueryString();

        return view('blog.index', [
            'posts' => $posts,
        ]);
    }

    public function show(string $lang, string $slug, SeoService $seo, InternalLinkService $internalLinkService): View
    {
        $lang = strtolower($lang);
        $locale = app()->getLocale();

        if ($locale !== $lang) {
            app()->setLocale($lang);
        }

        $post = BlogPost::query()
            ->published()
            ->where('language_code', $lang)
            ->where('slug', $slug)
            ->firstOrFail();

        // Count view (avoid double count on refresh within same session)
        $viewKey = 'blog_post_viewed_' . $post->id;
        if (!session()->has($viewKey) && Schema::hasColumn('blog_posts', 'view_count')) {
            try {
                BlogPost::query()->whereKey($post->id)->increment('view_count');
                $post->view_count = (int) ($post->view_count ?? 0) + 1;
                session()->put($viewKey, true);
            } catch (\Throwable) {
                // ignore (e.g. before migration)
            }
        }

        // Inject internal links for SEO (first match per rule)
        $contentHtml = $internalLinkService->inject((string) ($post->content ?? ''), $lang);

        $canonical = trim((string) ($post->canonical_url ?? ''));
        if ($canonical === '') {
            $canonical = $seo->canonical($lang . '/komodo-insider/' . $post->slug);
        }

        $breadcrumb = $seo->breadcrumbSchema([
            ['name' => 'Home', 'url' => $seo->canonical($lang)],
            ['name' => 'Komodo Insider', 'url' => $seo->canonical($lang . '/komodo-insider')],
            ['name' => $post->title, 'url' => $canonical],
        ]);

        $summary = trim((string) ($post->meta_description ?? $post->excerpt ?? ''));
        if ($summary === '') {
            $summary = Str::limit(trim(strip_tags($contentHtml)), 160, '');
        }

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $post->meta_title ?: $post->title,
            'description' => $summary,
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonical,
            ],
            'datePublished' => optional($post->published_at)->toAtomString(),
            'dateModified' => optional($post->updated_at)->toAtomString(),
        ];

        $ogImage = $post->ogImageUrl();
        if (!empty($ogImage)) {
            $articleSchema['image'] = url($ogImage);
        }

        return view('blog.show', [
            'post' => $post,
            'contentHtml' => $contentHtml,
            'canonical' => $canonical,
            'breadcrumbSchema' => $breadcrumb,
            'articleSchema' => $articleSchema,
            'translations' => BlogPost::query()
                ->where('group_key', $post->group_key)
                ->where('is_published', true)
                ->get(['language_code', 'slug']),
        ]);
    }
}
