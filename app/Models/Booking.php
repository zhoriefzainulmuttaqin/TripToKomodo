<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'inquiry_id',
        'total_price_idr',
        'profit_idr',
        'cs_commission_idr',
        'status',
        'closed_at',
    ];

    protected $casts = [
        'total_price_idr' => 'decimal:2',
        'profit_idr' => 'decimal:2',
        'cs_commission_idr' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }
}
