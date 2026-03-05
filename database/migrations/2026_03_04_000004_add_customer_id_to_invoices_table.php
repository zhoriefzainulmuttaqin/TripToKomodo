<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            if (!Schema::hasColumn('invoices', 'customer_id')) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('booking_id')
                    ->constrained('customers')
                    ->nullOnDelete();

                $table->index(['customer_id']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table): void {
            if (Schema::hasColumn('invoices', 'customer_id')) {
                try {
                    $table->dropForeign(['customer_id']);
                } catch (\Throwable) {
                    // ignore
                }

                try {
                    $table->dropIndex(['customer_id']);
                } catch (\Throwable) {
                    // ignore
                }

                $table->dropColumn('customer_id');
            }
        });
    }
};
