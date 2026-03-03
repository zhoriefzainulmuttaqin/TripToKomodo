<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    /** @var array<int, string> */
    protected array $supportedLocales = ['id', 'en', 'zh', 'es', 'de', 'ru'];

    public function index(Request $request): View
    {
        $lang = strtolower((string) $request->query('lang', ''));
        if ($lang === '' || !in_array($lang, $this->supportedLocales, true)) {
            $lang = 'id';
        }

        $status = strtolower((string) $request->query('status', 'published'));
        $q = trim((string) $request->query('q', ''));

        $query = BlogPost::query()->where('language_code', $lang);

        if ($status === 'draft') {
            $query->where('is_published', false);
        } else {
            $query->where('is_published', true);
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', '%' . $q . '%')
                    ->orWhere('slug', 'like', '%' . $q . '%')
                    ->orWhere('meta_title', 'like', '%' . $q . '%');
            });
        }

        $posts = $query->orderByDesc('published_at')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('admin.blog-posts.index', [
            'posts' => $posts,
            'lang' => $lang,
            'status' => $status,
            'q' => $q,
            'supportedLocales' => $this->supportedLocales,
        ]);
    }

    public function create(Request $request): View
    {
        $lang = strtolower((string) $request->query('lang', 'id'));
        if (!in_array($lang, $this->supportedLocales, true)) {
            $lang = 'id';
        }

        $translateFromId = (int) $request->query('translate_from', 0);
        $translateFrom = $translateFromId > 0 ? BlogPost::query()->find($translateFromId) : null;

        $post = new BlogPost();
        $post->language_code = $lang;

        if ($translateFrom) {
            $post->group_key = $translateFrom->group_key;
        }

        return view('admin.blog-posts.create', [
            'post' => $post,
            'supportedLocales' => $this->supportedLocales,
            'translateFrom' => $translateFrom,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        $lang = strtolower((string) ($validated['language_code'] ?? 'id'));
        if (!in_array($lang, $this->supportedLocales, true)) {
            $lang = 'id';
        }

        $translateFromId = (int) ($validated['translate_from'] ?? 0);
        $translateFrom = $translateFromId > 0 ? BlogPost::query()->find($translateFromId) : null;

        $post = new BlogPost();
        $post->group_key = $translateFrom?->group_key ?: (string) Str::uuid();
        $post->language_code = $lang;

        $this->fillAndSave($post, $validated, $request);

        return redirect()->route('admin.blog-posts.edit', $post)->with('status', 'Artikel berhasil dibuat.');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', [
            'post' => $blogPost,
            'supportedLocales' => $this->supportedLocales,
            'translations' => BlogPost::query()
                ->where('group_key', $blogPost->group_key)
                ->orderBy('language_code')
                ->get(['id', 'language_code', 'title', 'slug', 'is_published']),
        ]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $this->validatePayload($request, $blogPost->id);

        $this->fillAndSave($blogPost, $validated, $request);

        return redirect()->route('admin.blog-posts.edit', $blogPost)->with('status', 'Artikel berhasil disimpan.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        // Delete stored images if any
        foreach (['featured_image_path', 'og_image_path'] as $col) {
            $path = (string) ($blogPost->{$col} ?? '');
            if ($path !== '') {
                try {
                    Storage::disk('public')->delete($path);
                } catch (\Throwable) {
                    // ignore
                }
            }
        }

        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('status', 'Artikel berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $lang = strtolower((string) $request->input('language_code', 'id'));

        $uniqueSlug = Rule::unique('blog_posts', 'slug')
            ->where(fn ($q) => $q->where('language_code', $lang));
        if ($ignoreId) {
            $uniqueSlug = $uniqueSlug->ignore($ignoreId);
        }

        return $request->validate([
            'language_code' => ['required', 'string', 'max:10', Rule::in($this->supportedLocales)],
            'translate_from' => ['nullable', 'integer'],

            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', $uniqueSlug],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['nullable', 'string'],

            'featured_image' => ['nullable', 'image', 'max:5120'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'og_image' => ['nullable', 'image', 'max:5120'],
            'remove_og_image' => ['nullable', 'boolean'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'string', 'max:500'],
            'meta_robots' => ['nullable', 'string', 'max:60'],

            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:255'],

            'schema_json_ld' => ['nullable', 'string'],

            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    protected function fillAndSave(BlogPost $post, array $validated, Request $request): void
    {
        $lang = strtolower((string) ($validated['language_code'] ?? $post->language_code ?? 'id'));
        if (!in_array($lang, $this->supportedLocales, true)) {
            $lang = $post->language_code ?: 'id';
        }
        $post->language_code = $lang;

        $post->title = trim((string) ($validated['title'] ?? ''));

        $inputSlug = trim((string) ($validated['slug'] ?? ''));
        $baseSlug = $inputSlug === '' ? Str::slug($post->title) : Str::slug($inputSlug);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'post';

        $slug = $baseSlug;
        $counter = 2;
        while (BlogPost::query()
            ->where('language_code', $post->language_code)
            ->where('slug', $slug)
            ->when($post->exists, fn ($q) => $q->where('id', '!=', $post->id))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $post->slug = $slug;

        $post->excerpt = trim((string) ($validated['excerpt'] ?? '')) ?: null;
        $post->content = (string) ($validated['content'] ?? '');

        if (Schema::hasColumn('blog_posts', 'reading_time_minutes')) {
            $post->reading_time_minutes = BlogPost::estimateReadingTimeMinutes($post->content);
        }



        $post->meta_title = trim((string) ($validated['meta_title'] ?? '')) ?: null;
        $post->meta_description = trim((string) ($validated['meta_description'] ?? '')) ?: null;
        $post->meta_keywords = trim((string) ($validated['meta_keywords'] ?? '')) ?: null;
        $post->canonical_url = trim((string) ($validated['canonical_url'] ?? '')) ?: null;
        $post->meta_robots = trim((string) ($validated['meta_robots'] ?? '')) ?: null;
        $post->og_title = trim((string) ($validated['og_title'] ?? '')) ?: null;
        $post->og_description = trim((string) ($validated['og_description'] ?? '')) ?: null;
        $post->schema_json_ld = trim((string) ($validated['schema_json_ld'] ?? '')) ?: null;

        $post->is_published = (bool) ($validated['is_published'] ?? false);
        if ($post->is_published) {
            $post->published_at = !empty($validated['published_at']) ? $validated['published_at'] : ($post->published_at ?: now());
        } else {
            $post->published_at = null;
        }

        $userId = Auth::id();
        if ($post->exists) {
            $post->updated_by = $userId;
        } else {
            $post->created_by = $userId;
            $post->updated_by = $userId;
        }

        // Images
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $newPath = Storage::disk('public')->putFile('blog/featured', $file);

            if (!empty($post->featured_image_path) && $post->featured_image_path !== $newPath) {
                try {
                    Storage::disk('public')->delete($post->featured_image_path);
                } catch (\Throwable) {
                    // ignore
                }
            }

            $post->featured_image_path = $newPath;
        } else {
            $remove = (bool) ($validated['remove_featured_image'] ?? false);
            if ($remove && !empty($post->featured_image_path)) {
                try {
                    Storage::disk('public')->delete($post->featured_image_path);
                } catch (\Throwable) {
                    // ignore
                }
                $post->featured_image_path = null;
            }
        }

        if ($request->hasFile('og_image')) {
            $file = $request->file('og_image');
            $newPath = Storage::disk('public')->putFile('blog/og', $file);

            if (!empty($post->og_image_path) && $post->og_image_path !== $newPath) {
                try {
                    Storage::disk('public')->delete($post->og_image_path);
                } catch (\Throwable) {
                    // ignore
                }
            }

            $post->og_image_path = $newPath;
        } else {
            $remove = (bool) ($validated['remove_og_image'] ?? false);
            if ($remove && !empty($post->og_image_path)) {
                try {
                    Storage::disk('public')->delete($post->og_image_path);
                } catch (\Throwable) {
                    // ignore
                }
                $post->og_image_path = null;
            }
        }

        $post->save();
    }
}
