<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TourFaq extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tour_package_id',
        'language_code',
        'question',
        'answer',
        'sort_order',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(TourPackage::class, 'tour_package_id');
    }
}
