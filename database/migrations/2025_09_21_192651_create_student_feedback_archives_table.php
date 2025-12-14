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
        Schema::create('student_feedback_archives', function (Blueprint $table) {
    $table->id();
    $table->string('file_name');
    $table->string('original_name');
    $table->string('semester');
    $table->string('school_year');
    $table->string('year_level');
    $table->string('section');
    $table->string('mime_type')->nullable();
    $table->unsignedBigInteger('size')->nullable();
    $table->string('path');
    $table->string('compressed_path')->nullable();
    $table->boolean('is_compressed')->default(false);
    $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_feedback_archives');
    }
};
