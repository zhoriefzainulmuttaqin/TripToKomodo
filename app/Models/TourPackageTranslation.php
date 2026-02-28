<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPackageTranslation extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tour_package_id',
        'language_code',
        'slug',
        'title',
        'summary',
        'description',
        'itinerary',
        'includes',
        'excludes',
        'transportation',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
