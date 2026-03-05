<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id', 64)->nullable()->index();
            $table->string('event_type', 40)->index();
            $table->string('page_path')->nullable()->index();
            $table->text('page_url')->nullable();
            $table->text('referrer')->nullable();

            $table->string('source_channel', 40)->nullable()->index();
            $table->string('source_detail', 120)->nullable();
            $table->string('utm_source', 120)->nullable();
            $table->string('utm_medium', 120)->nullable();
            $table->string('utm_campaign', 120)->nullable();
            $table->string('utm_term', 120)->nullable();
            $table->string('utm_content', 120)->nullable();

            $table->string('country_code', 2)->nullable()->index();
            $table->string('device_type', 20)->nullable()->index();
            $table->string('browser', 40)->nullable();

            $table->string('contact_target', 120)->nullable();
            $table->unsignedInteger('engagement_seconds')->default(0);

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('occurred_at')->useCurrent()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_events');
    }
};
