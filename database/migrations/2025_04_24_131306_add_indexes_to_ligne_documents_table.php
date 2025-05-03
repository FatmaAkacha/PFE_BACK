<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToLigneDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ligne_documents', function (Blueprint $table) {
            $table->index('document_id');
            $table->index('produit_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ligne_documents', function (Blueprint $table) {
            $table->dropIndex(['document_id']);
            $table->dropIndex(['produit_id']);
        });
    }
}
