<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profils', function (Blueprint $table) {
            $table->increments('id');
            $table->string('libelle');
            $table->string('code',3)->unique();
            $table->string('niveauacces_code');
            $table->tinyInteger('statistique');
            $table->tinyInteger('dossierPatient');
            $table->tinyInteger('dossierPatientComplet');
            $table->tinyInteger('dispensation');
            $table->tinyInteger('modierDossierPatient');
            $table->tinyInteger('parametre');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });

        Schema::table('profils', function($table) {
            $table->foreign('niveauacces_code')->references('code')->on('niveauacces');
           
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profil');
    }
}
