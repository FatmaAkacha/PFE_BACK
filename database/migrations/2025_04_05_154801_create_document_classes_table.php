<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('document_classes', function (Blueprint $table) {
            $table->id(); // Assurez-vous que la colonne `id` est un auto-increment ou unsignedBigInteger
            $table->string('libelle');
            $table->string('prefix');
            $table->boolean('isvent');
            $table->boolean('isachat');
            $table->boolean('actif');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document_classes');
    }
}
