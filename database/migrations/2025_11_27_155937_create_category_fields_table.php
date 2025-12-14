<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('field_name');
            $table->enum('field_type', ['text', 'textarea', 'date', 'file'])->default('text');
            $table->string('field_slug');
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            // Optional: unique slug per category
            $table->unique(['category_id', 'field_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_fields');
    }
};
