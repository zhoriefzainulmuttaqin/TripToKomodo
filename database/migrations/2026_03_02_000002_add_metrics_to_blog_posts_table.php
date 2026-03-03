<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->unsignedBigInteger('view_count')->default(0)->index()->after('schema_json_ld');
            $table->unsignedSmallInteger('reading_time_minutes')->nullable()->after('view_count');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropColumn(['view_count', 'reading_time_minutes']);
        });
    }
};
