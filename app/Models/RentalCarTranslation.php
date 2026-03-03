<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalCarTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'rental_car_id',
        'language_code',
        'slug',
        'name',
        'excerpt',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function rentalCar(): BelongsTo
    {
        return $this->belongsTo(RentalCar::class);
    }
}
