<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourPackageAvailability extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tour_package_id',
        'date',
        'is_available',
        'available_slots',
        'price_idr_override',
        'note',
    ];

    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
        'available_slots' => 'integer',
        'price_idr_override' => 'decimal:2',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
