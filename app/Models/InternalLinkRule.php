<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternalLinkRule extends Model
{
    protected $fillable = [
        'keyword',
        'target_url',
        'language_code',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
