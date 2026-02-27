<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'inquiry_id',
        'phone',
        'template',
        'payload',
        'status',
        'sent_at',
        'response_payload',
    ];

    protected $casts = [
        'payload' => 'array',
        'response_payload' => 'array',
        'sent_at' => 'datetime',
    ];

    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(Inquiry::class);
    }
}
