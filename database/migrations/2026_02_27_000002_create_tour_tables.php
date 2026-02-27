<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_operators', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->decimal('default_commission_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('tour_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->decimal('base_price_idr', 15, 2);
            $table->unsignedInteger('duration_days')->default(1);
            $table->unsignedInteger('duration_nights')->default(0);
            $table->unsignedInteger('min_people')->default(1);
            $table->unsignedInteger('max_people')->nullable();
            $table->string('difficulty')->nullable();
            $table->string('status')->default('draft');
            $table->date('starts_from')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });

        Schema::create('tour_package_translations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->string('language_code', 5)->index();
            $table->string('slug');
            $table->string('title');
            $table->string('summary', 300)->nullable();
            $table->longText('description')->nullable();
            $table->longText('itinerary')->nullable();
            $table->longText('includes')->nullable();
            $table->longText('excludes')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['language_code', 'slug']);
        });

        Schema::create('tour_images', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->string('url');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tour_faqs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->string('language_code', 5)->index();
            $table->string('question');
            $table->text('answer');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('tour_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->string('language_code', 5)->index();
            $table->string('author_name')->nullable();
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        Schema::create('tour_operator_offers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->cascadeOnDelete();
            $table->foreignId('tour_package_id')->constrained('tour_packages')->cascadeOnDelete();
            $table->decimal('base_price_idr', 15, 2);
            $table->string('notes')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_operator_offers');
        Schema::dropIfExists('tour_reviews');
        Schema::dropIfExists('tour_faqs');
        Schema::dropIfExists('tour_images');
        Schema::dropIfExists('tour_package_translations');
        Schema::dropIfExists('tour_packages');
        Schema::dropIfExists('tour_operators');
    }
};
