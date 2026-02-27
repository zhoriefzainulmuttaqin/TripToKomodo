<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inquiry extends Model
{
    protected $fillable = [
        'tracking_code',
        'tour_package_id',
        'tour_operator_id',
        'affiliate_id',
        'coupon_id',
        'source',
        'status',
        'currency_code',
        'base_price_idr',
        'selling_price_idr',
        'name',
        'email',
        'phone',
        'nationality',
        'budget_min',
        'budget_max',
        'interest_tags',
        'ip_address',
        'user_agent',
        'abandoned_at',
    ];

    protected $casts = [
        'interest_tags' => 'array',
        'base_price_idr' => 'decimal:2',
        'selling_price_idr' => 'decimal:2',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'abandoned_at' => 'datetime',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(TourOperator::class, 'tour_operator_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class, 'affiliate_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }
}
