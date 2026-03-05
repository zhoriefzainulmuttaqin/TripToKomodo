<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();

            // Invoice bisa manual (tanpa transaksi/booking di web).
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->nullOnDelete();

            $table->string('invoice_number')->unique();
            $table->string('status')->default('dp'); // dp | paid
            $table->timestamp('issued_at')->nullable();

            $table->string('currency_code', 3)->default('IDR');
            $table->decimal('total_amount_idr', 15, 2)->default(0);
            $table->decimal('paid_amount_idr', 15, 2)->default(0);
            $table->decimal('remaining_amount_idr', 15, 2)->default(0);

            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            // Snapshot customer + trip data for stable invoices
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();

            $table->unsignedBigInteger('tour_package_id')->nullable();
            $table->string('tour_package_title')->nullable();
            $table->date('travel_date')->nullable();
            $table->unsignedInteger('traveler_count')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
            $table->index(['issued_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

