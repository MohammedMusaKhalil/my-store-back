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
        // create categories table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('desc');
            $table->string('image');
            $table->timestamps();
        });

        // add category key to products table
        Schema::table('products', function ($table) {
            $table->integer('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // remove categories table
        Schema::dropIfExists('categories');

        // remove category key column
        Schema::table('products', function ($table) {
            $table->dropColumn('category');
        });
    }
};
