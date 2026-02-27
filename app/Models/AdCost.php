<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdCost extends Model
{
    protected $fillable = [
        'tour_package_id',
        'channel',
        'period_start',
        'period_end',
        'cost_idr',
        'leads_count',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'cost_idr' => 'decimal:2',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
