<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingAndFournisseurToProduitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('produits', function (Blueprint $table) {
            $table->decimal('prix_achat', 10, 2)->after('description');
            $table->decimal('prix_vente_ht', 10, 2)->after('prix_achat');
            $table->decimal('prix_vente_ttc', 10, 2)->after('prix_vente_ht');
            $table->decimal('remise_maximale', 5, 2)->after('prix_vente_ttc')->default(0);
            $table->uuid('fournisseur_id')->nullable()->after('categorie_id');
    
            $table->foreign('fournisseur_id')->references('id')->on('fournisseurs')->onDelete('set null');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('produits', function (Blueprint $table) {
            //
        });
    }
}
