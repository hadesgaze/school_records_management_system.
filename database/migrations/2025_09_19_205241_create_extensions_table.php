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
    Schema::create('extensions', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->date('date_implemented')->nullable();
        $table->text('description')->nullable();
        $table->string('file_name');
        $table->string('original_name');
        $table->string('mime_type');
        $table->bigInteger('size');
        $table->string('compressed_path')->nullable();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('extensions');
}

};
