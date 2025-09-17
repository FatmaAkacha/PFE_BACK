<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreparateurIdToDocumentsTable extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Vérifie si la colonne n'existe pas déjà
            if (!Schema::hasColumn('documents', 'preparateur_id')) {
                $table->unsignedBigInteger('preparateur_id')->nullable();
                $table->foreign('preparateur_id')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            // Supprime la contrainte et la colonne seulement si elle existe
            if (Schema::hasColumn('documents', 'preparateur_id')) {
                $table->dropForeign(['preparateur_id']);
                $table->dropColumn('preparateur_id');
            }
        });
    }
}
