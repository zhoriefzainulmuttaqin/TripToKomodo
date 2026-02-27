<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code',
        'symbol',
        'exchange_rate_to_idr',
        'is_active',
    ];

    protected $casts = [
        'exchange_rate_to_idr' => 'decimal:6',
        'is_active' => 'boolean',
    ];
}
