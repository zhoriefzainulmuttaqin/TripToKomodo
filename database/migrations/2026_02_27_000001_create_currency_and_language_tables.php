<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 3)->unique();
            $table->string('symbol', 8);
            $table->decimal('exchange_rate_to_idr', 15, 6)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 5)->unique();
            $table->string('name');
            $table->string('native_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('country_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('country_code', 2)->unique();
            $table->string('default_language_code', 5)->index();
            $table->string('default_currency_code', 3)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_profiles');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('currencies');
    }
};
