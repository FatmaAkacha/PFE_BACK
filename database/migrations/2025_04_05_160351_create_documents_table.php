<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_class_id'); // Assurez-vous que c'est unsignedBigInteger
            $table->string('libelle');
            $table->string('code');
            $table->timestamps();
        
            $table->foreign('document_class_id')  // Déclaration de la clé étrangère
                  ->references('id')
                  ->on('document_classes')
                  ->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
}
