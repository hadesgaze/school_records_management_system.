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
    Schema::create('faculty_needs', function (Blueprint $table) {
        $table->id();
        $table->string('original_name'); // Original file name
        $table->string('filename');      // Stored compressed file name
        $table->string('extension');     // File extension (zip)
        $table->bigInteger('size');      // File size in bytes
        $table->timestamps();            // created_at and updated_at
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_needs');
    }
};
