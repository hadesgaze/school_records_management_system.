<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('category_fields', function (Blueprint $table) {
            // Don't specify 'after' - just add it at the end
            $table->integer('order')->default(0);
        });
    }

    public function down()
    {
        Schema::table('category_fields', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};