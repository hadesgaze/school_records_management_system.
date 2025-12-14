<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_instructions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('program')->nullable();
            $table->string('course_code')->nullable();
            $table->text('description')->nullable();
            $table->string('semester');
            $table->string('school_year');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('compressed_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_instructions');
    }
};
