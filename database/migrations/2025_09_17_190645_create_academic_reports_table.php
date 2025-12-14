<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('academic_reports', function (Blueprint $table) {
        $table->id();
        $table->string('title')->nullable();          // Optional title or description
        $table->string('file_name');                  // Stored filename
        $table->string('original_name');              // Original uploaded name
        $table->string('mime_type');                  // e.g., application/pdf
        $table->unsignedBigInteger('size');           // Size in bytes
        $table->string('compressed_path');            // Path of the compressed file
        $table->unsignedBigInteger('user_id')->nullable(); // ID of uploader
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_reports');
    }
};
