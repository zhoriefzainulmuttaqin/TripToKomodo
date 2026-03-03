<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'group_key',
        'language_code',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_path',
        'og_image_path',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'meta_robots',
        'og_title',
        'og_description',
        'schema_json_ld',
        'view_count',
        'reading_time_minutes',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'view_count' => 'integer',
        'reading_time_minutes' => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function featuredImageUrl(): ?string
    {
        $path = (string) ($this->featured_image_path ?? '');
        if ($path === '') {
            return null;
        }

        return '/storage/' . ltrim($path, '/');
    }

    public function ogImageUrl(): ?string
    {
        $path = (string) ($this->og_image_path ?? '');
        if ($path === '') {
            return $this->featuredImageUrl();
        }

        return '/storage/' . ltrim($path, '/');
    }

    public function readingTimeMinutesComputed(): ?int
    {
        if ($this->reading_time_minutes !== null) {
            return (int) $this->reading_time_minutes;
        }

        return self::estimateReadingTimeMinutes($this->content);
    }

    public static function estimateReadingTimeMinutes(?string $html): ?int
    {
        $html = (string) ($html ?? '');
        if (trim($html) === '') {
            return null;
        }

        $text = trim(preg_replace('/\s+/u', ' ', strip_tags($html)) ?? '');
        if ($text === '') {
            return null;
        }

        // If it's mostly CJK without spaces, estimate by character count.
        $hasCjk = preg_match('/[\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{3040}-\x{30FF}\x{AC00}-\x{D7AF}]/u', $text) === 1;
        $hasSpaces = strpos($text, ' ') !== false;

        if ($hasCjk && !$hasSpaces) {
            $chars = function_exists('mb_strlen') ? mb_strlen($text) : strlen($text);
            $cpm = 400; // chars per minute
            return max(1, (int) ceil($chars / $cpm));
        }

        $parts = preg_split('/\s+/u', $text) ?: [];
        $words = 0;
        foreach ($parts as $p) {
            if (trim((string) $p) !== '') {
                $words++;
            }
        }

        $wpm = 200;
        return max(1, (int) ceil($words / $wpm));
    }
}
