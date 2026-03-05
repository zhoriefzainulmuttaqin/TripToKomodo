<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table): void {
            $table->id();

            $table->string('full_name', 160);
            $table->string('phone', 50)->nullable(); // No HP/WhatsApp
            $table->string('email', 120)->nullable();
            $table->string('country', 80)->nullable();

            // Kontak lain: IG/WeChat/Telegram/Link dll (disimpan sebagai array string)
            $table->json('other_contacts')->nullable();

            // Dokumen (opsional)
            $table->string('document_path')->nullable();
            $table->string('document_original_name')->nullable();
            $table->string('document_mime', 120)->nullable();
            $table->unsignedInteger('document_size')->nullable();

            // Data lainnya
            $table->text('notes')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['full_name']);
            $table->index(['phone']);
            $table->index(['email']);
            $table->index(['country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
