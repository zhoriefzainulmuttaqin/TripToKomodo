<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryProfile extends Model
{
    protected $fillable = [
        'country_code',
        'default_language_code',
        'default_currency_code',
    ];
}
