<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_margins', function (Blueprint $table): void {
            $table->id();
            $table->string('scope_type');
            $table->unsignedBigInteger('scope_id')->nullable();
            $table->string('margin_type');
            $table->decimal('margin_value', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['scope_type', 'scope_id']);
        });

        Schema::create('commission_rules', function (Blueprint $table): void {
            $table->id();
            $table->string('role');
            $table->string('commission_type');
            $table->decimal('commission_value', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ad_costs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tour_package_id')->nullable()->constrained('tour_packages')->nullOnDelete();
            $table->string('channel');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('cost_idr', 15, 2)->default(0);
            $table->unsignedInteger('leads_count')->default(0);
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table): void {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('tour_package_id')->nullable()->constrained('tour_packages')->nullOnDelete();
            $table->foreignId('tour_operator_id')->nullable()->constrained('tour_operators')->nullOnDelete();
            $table->unsignedBigInteger('affiliate_id')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new');
            $table->string('currency_code', 3)->nullable();
            $table->decimal('base_price_idr', 15, 2)->nullable();
            $table->decimal('selling_price_idr', 15, 2)->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('nationality', 2)->nullable();
            $table->decimal('budget_min', 15, 2)->nullable();
            $table->decimal('budget_max', 15, 2)->nullable();
            $table->json('interest_tags')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('abandoned_at')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('inquiry_id')->constrained('inquiries')->cascadeOnDelete();
            $table->decimal('total_price_idr', 15, 2);
            $table->decimal('profit_idr', 15, 2)->default(0);
            $table->decimal('cs_commission_idr', 15, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('ad_costs');
        Schema::dropIfExists('commission_rules');
        Schema::dropIfExists('price_margins');
    }
};
