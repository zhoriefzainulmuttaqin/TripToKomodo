<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalCar extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'is_active',
        'seats',
        'transmission',
        'fuel',
        'luggage',
        'price_per_day_idr',
        'image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'seats' => 'integer',
        'luggage' => 'integer',
        'price_per_day_idr' => 'integer',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(RentalCarTranslation::class);
    }

    public function translationFor(string $locale, ?string $fallback = null): ?RentalCarTranslation
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
