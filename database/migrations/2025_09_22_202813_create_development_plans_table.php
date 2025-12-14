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
    Schema::create('development_plans', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description')->nullable();
        $table->date('date_implemented')->nullable();
        $table->string('version')->nullable();
        $table->string('file_name');
        $table->string('original_name');
        $table->string('mime_type')->nullable();
        $table->bigInteger('size')->nullable();
        $table->string('compressed_path');
        $table->unsignedBigInteger('user_id')->nullable();
        $table->timestamps();

        $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_plans');
    }
};
