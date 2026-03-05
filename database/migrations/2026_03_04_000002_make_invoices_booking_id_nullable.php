<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jika tabel sudah terlanjur dibuat dengan booking_id NOT NULL,
        // kita ubah jadi nullable agar invoice bisa dibuat manual.

        try {
            Schema::table('invoices', function ($table): void {
                $table->dropForeign(['booking_id']);
            });
        } catch (\Throwable) {
            // ignore
        }

        // Hindari kebutuhan doctrine/dbal (column change) dengan raw SQL.
        DB::statement('ALTER TABLE `invoices` MODIFY `booking_id` BIGINT UNSIGNED NULL');

        try {
            Schema::table('invoices', function ($table): void {
                $table->foreign('booking_id')->references('id')->on('bookings')->nullOnDelete();
            });
        } catch (\Throwable) {
            // ignore
        }
    }

    public function down(): void
    {
        try {
            Schema::table('invoices', function ($table): void {
                $table->dropForeign(['booking_id']);
            });
        } catch (\Throwable) {
            // ignore
        }

        DB::statement('ALTER TABLE `invoices` MODIFY `booking_id` BIGINT UNSIGNED NOT NULL');

        try {
            Schema::table('invoices', function ($table): void {
                $table->foreign('booking_id')->references('id')->on('bookings')->cascadeOnDelete();
            });
        } catch (\Throwable) {
            // ignore
        }
    }
};
