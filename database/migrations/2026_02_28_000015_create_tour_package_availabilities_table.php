<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tour_package_availabilities')) {
            return;
        }

        Schema::create('tour_package_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->date('date');
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('available_slots')->nullable();
            $table->decimal('price_idr_override', 15, 2)->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tour_package_id', 'date']);
            $table->index(['tour_package_id', 'is_available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_package_availabilities');
    }
};
