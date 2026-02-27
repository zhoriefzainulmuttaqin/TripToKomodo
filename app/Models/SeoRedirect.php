<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoRedirect extends Model
{
    protected $fillable = [
        'from_url',
        'to_url',
        'status_code',
        'language_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
