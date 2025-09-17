<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->string('image')->nullable();
            $table->string('category')->nullable();
            $table->string('inventoryStatus')->nullable();
            $table->float('rating')->nullable();
        });
    }
    
    public function down()
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->dropColumn(['image', 'category', 'inventoryStatus', 'rating']);
        });
    }
}   
