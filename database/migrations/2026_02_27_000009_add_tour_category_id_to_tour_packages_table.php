<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tour_packages')) {
            return;
        }

        if (Schema::hasColumn('tour_packages', 'tour_category_id')) {
            return;
        }

        Schema::table('tour_packages', function (Blueprint $table) {
            $table->foreignId('tour_category_id')
                ->nullable()
                ->after('tour_operator_id')
                ->constrained('tour_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tour_packages') || !Schema::hasColumn('tour_packages', 'tour_category_id')) {
            return;
        }

        Schema::table('tour_packages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tour_category_id');
        });
    }
};
