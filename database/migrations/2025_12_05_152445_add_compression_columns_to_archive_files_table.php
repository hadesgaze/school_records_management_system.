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
        Schema::table('archive_files', function (Blueprint $table) {
            $table->boolean('is_compressed')->default(false)->after('file_size');
            $table->bigInteger('compressed_size')->nullable()->after('is_compressed');
            $table->decimal('compression_ratio', 5, 2)->nullable()->after('compressed_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archive_files', function (Blueprint $table) {
            $table->dropColumn(['is_compressed', 'compressed_size', 'compression_ratio']);
        });
    }
};