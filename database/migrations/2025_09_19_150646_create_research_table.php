<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research', function (Blueprint $table) {
            $table->id();
            $table->string('title');                  // Research title
            $table->date('date_published')->nullable(); // Publication date
            $table->string('authors')->nullable();     // Comma-separated authors
            $table->string('file_name');               // Stored filename (UUID)
            $table->string('original_name');           // Original uploaded filename
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('compressed_path')->nullable();
            $table->foreignId('user_id')->nullable()
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research');
    }
};
