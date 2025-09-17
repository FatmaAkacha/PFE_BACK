<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->string('etat')->nullable();
            $table->string('preparateur')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('devise')->nullable();
            $table->decimal('tauxEchange', 10, 4)->nullable();
            $table->date('dateDocument')->nullable();
            $table->date('dateLivraison')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            //
        });
    }
}
