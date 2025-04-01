<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToDevisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->string('etat')->nullable(); // Ajout de la colonne "etat"
            $table->string('preparateur')->nullable(); // Ajout de la colonne "preparateur"
            $table->date('dateDocument')->nullable(); // Ajout de la colonne "dateDocument"
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('devis', function (Blueprint $table) {
            $table->dropColumn(['etat', 'preparateur', 'dateDocument']);
        });
    }
}
