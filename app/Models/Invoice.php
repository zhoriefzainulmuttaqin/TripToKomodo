<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_id',
        'invoice_number',
        'status',
        'issued_at',
        'currency_code',
        'total_amount_idr',
        'paid_amount_idr',
        'remaining_amount_idr',
        'payment_method',
        'payment_reference',
        'paid_at',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'tour_package_id',
        'tour_package_title',
        'travel_date',
        'traveler_count',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'travel_date' => 'date',
        'total_amount_idr' => 'decimal:2',
        'paid_amount_idr' => 'decimal:2',
        'remaining_amount_idr' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo

    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate invoice number format: INV-YYYYMM-0001
     */
    public static function generateNextNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';

        return DB::transaction(function () use ($prefix): string {
            $latest = (string) (static::query()
                ->where('invoice_number', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('invoice_number')
                ->value('invoice_number') ?? '');

            $next = 1;
            if ($latest !== '' && str_starts_with($latest, $prefix)) {
                $tail = substr($latest, strlen($prefix));
                $n = (int) $tail;
                if ($n > 0) {
                    $next = $n + 1;
                }
            }

            return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
        });
    }
}
