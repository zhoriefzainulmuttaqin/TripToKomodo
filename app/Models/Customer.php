<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'country',
        'other_contacts',
        'document_path',
        'document_original_name',
        'document_mime',
        'document_size',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'other_contacts' => 'array',
        'document_size' => 'integer',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
