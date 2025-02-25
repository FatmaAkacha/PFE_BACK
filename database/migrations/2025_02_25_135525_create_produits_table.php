<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->id(); // ID primaire
            $table->string('nom'); // Nom du produit
            $table->text('description'); // Description du produit
            $table->integer('prix'); // Prix avec deux décimales
            $table->integer('quantitystock'); // Quantité en stock
            $table->integer('seuil'); // Seuil de stock
            $table->timestamps(); // Timestamp (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('produits');
    }
}
