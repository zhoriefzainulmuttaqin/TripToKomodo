<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('destinations')) {
            return;
        }

        Schema::table('destinations', function (Blueprint $table) {
            if (!Schema::hasColumn('destinations', 'image')) {
                $table->string('image')->nullable()->after('name');
            }
            if (!Schema::hasColumn('destinations', 'description')) {
                $table->text('description')->nullable()->after('image');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('destinations')) {
            return;
        }

        Schema::table('destinations', function (Blueprint $table) {
            if (Schema::hasColumn('destinations', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('destinations', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
