<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TourOperator extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'contact_name',
        'contact_email',
        'contact_phone',
        'default_commission_rate',
        'is_active',
    ];

    protected $casts = [
        'default_commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(TourPackage::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(TourOperatorOffer::class);
    }
}
