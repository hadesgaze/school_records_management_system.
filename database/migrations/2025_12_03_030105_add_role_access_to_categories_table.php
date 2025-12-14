<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   // In the migration file
public function up()
{
    Schema::table('categories', function (Blueprint $table) {
        // Add a column to store accessible roles (JSON array)
        $table->json('accessible_roles')->nullable()->after('description');
        
        // Or if you prefer single role ownership:
        // $table->enum('created_for', ['faculty', 'dean', 'chairperson', 'all'])->default('faculty');
    });
}

public function down()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->dropColumn('accessible_roles');
    });
}
};
