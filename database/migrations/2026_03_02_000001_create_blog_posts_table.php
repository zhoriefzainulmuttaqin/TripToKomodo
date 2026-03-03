<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();

            // Translation grouping (same article across languages)
            $table->string('group_key', 36)->index();
            $table->string('language_code', 10)->index();

            // Core content
            $table->string('title');
            $table->string('slug');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();

            // Images
            $table->string('featured_image_path')->nullable();
            $table->string('og_image_path')->nullable();

            // SEO
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 255)->nullable();
            $table->string('meta_keywords', 500)->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('meta_robots', 60)->nullable();
            $table->string('og_title', 255)->nullable();
            $table->string('og_description', 255)->nullable();
            $table->longText('schema_json_ld')->nullable();

            // Publishing
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();

            // Admin
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();

            $table->timestamps();

            $table->unique(['language_code', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
