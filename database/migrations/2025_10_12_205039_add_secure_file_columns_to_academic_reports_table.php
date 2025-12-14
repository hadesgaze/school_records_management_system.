<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('academic_reports', function (Blueprint $table) {
            // New “secure pipeline” columns
            $table->string('pdf_filename')->nullable()->after('file_name');
            $table->string('encrypted_path')->nullable()->after('pdf_filename');
            $table->boolean('is_encrypted')->default(true)->after('encrypted_path');
            $table->string('sha256')->nullable()->after('is_encrypted');
            $table->timestamp('converted_to_pdf_at')->nullable()->after('sha256');
        });
    }

    public function down(): void
    {
        Schema::table('academic_reports', function (Blueprint $table) {
            $table->dropColumn(['pdf_filename', 'encrypted_path', 'is_encrypted', 'sha256', 'converted_to_pdf_at']);
        });
    }
};
