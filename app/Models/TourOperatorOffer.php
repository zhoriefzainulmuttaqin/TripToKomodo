<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourOperatorOffer extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tour_operator_id',
        'tour_package_id',
        'base_price_idr',
        'notes',
        'valid_from',
        'valid_to',
        'is_active',
    ];

    protected $casts = [
        'base_price_idr' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function operator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class, 'tour_operator_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
