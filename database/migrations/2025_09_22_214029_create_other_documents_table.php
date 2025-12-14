<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('other_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('version')->nullable();
            $table->date('date_implemented')->nullable();
            $table->string('category')->nullable();      // e.g., Memo, Form, Letter
            $table->json('tags')->nullable();            // optional tags as JSON array

            // file info
            $table->string('file_name');        // stored file name
            $table->string('original_name');    // original client filename
            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable(); // bytes
            $table->string('compressed_path');  // relative path to zip in public disk

            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('other_documents');
    }
};
