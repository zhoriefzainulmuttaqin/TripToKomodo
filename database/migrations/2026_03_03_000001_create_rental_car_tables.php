<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rental_cars', function (Blueprint $table): void {
            $table->id();
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('seats')->nullable();
            $table->string('transmission', 50)->nullable();
            $table->string('fuel', 50)->nullable();
            $table->unsignedInteger('luggage')->nullable();

            // Base price per day in IDR
            $table->unsignedBigInteger('price_per_day_idr')->default(0);

            // Simple primary image (optional). Stored as public URL like "/storage/..."
            $table->string('image')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'deleted_at']);
        });

        Schema::create('rental_car_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rental_car_id')->constrained('rental_cars')->cascadeOnDelete();

            $table->string('language_code', 10);
            $table->string('slug', 255);
            $table->string('name', 255);
            $table->text('excerpt')->nullable();
            $table->longText('description')->nullable();

            $table->string('meta_title', 120)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('meta_keywords', 300)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['language_code', 'slug']);
            $table->unique(['rental_car_id', 'language_code']);
            $table->index(['language_code', 'is_active', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_car_translations');
        Schema::dropIfExists('rental_cars');
    }
};
