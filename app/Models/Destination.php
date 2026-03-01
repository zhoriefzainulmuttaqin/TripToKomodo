<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Destination extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'description',
        'category',
        'distance',
        'lat',
        'lng',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'lat' => 'decimal:7',
        'lng' => 'decimal:7',
        'is_active' => 'boolean',
    ];

    public function tourPackages(): BelongsToMany
    {
        return $this->belongsToMany(TourPackage::class, 'tour_package_destinations')
            ->withPivot(['sort_order'])
            ->withTimestamps();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(DestinationTranslation::class);
    }

    public function translationFor(string $locale, ?string $fallback = null): ?DestinationTranslation
    {
        $fallback = $fallback ?? (string) config('app.fallback_locale', 'en');

        if ($this->relationLoaded('translations')) {
            return $this->translations->firstWhere('language_code', $locale)
                ?? $this->translations->firstWhere('language_code', $fallback);
        }

        $translations = $this->translations()
            ->whereIn('language_code', array_values(array_unique([$locale, $fallback])))
            ->get();

        return $translations->firstWhere('language_code', $locale)
            ?? $translations->firstWhere('language_code', $fallback);

    }
}

