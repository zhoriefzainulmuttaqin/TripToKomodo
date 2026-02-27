<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceMargin extends Model
{
    protected $fillable = [
        'scope_type',
        'scope_id',
        'margin_type',
        'margin_value',
        'is_active',
    ];

    protected $casts = [
        'margin_value' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
