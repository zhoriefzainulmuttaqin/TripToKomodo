<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;



class TourPackage extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tour_operator_id',
        'tour_category_id',
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

    public function primaryImage(): HasOne
    {
        return $this->hasOne(TourImage::class)->where('is_primary', true)->orderBy('sort_order');
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class, 'tour_package_destinations')
            ->withPivot(['sort_order'])
            ->withTimestamps()
            ->orderBy('tour_package_destinations.sort_order');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(TourPackageAvailability::class, 'tour_package_id')->orderBy('date');
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }

    public function translationFor(string $languageCode): ?TourPackageTranslation
    {
        return $this->translations()->where('language_code', $languageCode)->first();
    }
}
