<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourPackage extends Model
{
    protected $fillable = [
        'tour_operator_id',
        'code',
        'base_price_idr',
        'duration_days',
        'duration_nights',
        'min_people',
        'max_people',
        'difficulty',
        'status',
        'starts_from',
        'ends_at',
        'is_featured',
    ];

    protected $casts = [
        'base_price_idr' => 'decimal:2',
        'starts_from' => 'date',
        'ends_at' => 'date',
        'is_featured' => 'boolean',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class, 'tour_operator_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(TourPackageTranslation::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class);
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(TourFaq::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TourReview::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(TourOperatorOffer::class);
    }

    public function translationFor(string $languageCode): ?TourPackageTranslation
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }
}
