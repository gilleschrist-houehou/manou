<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('codeAncien');
            $table->string('nom');
            $table->string('prenoms');

            $table->date('dateNaissance');

            $table->string('profession');
            $table->string('sexe');
            $table->string('adresse');
            $table->string('contacts');
            $table->string('situationMatri');
            $table->string('niveauEtude');
            $table->string('nationalite');
            $table->string('ethnie');
            $table->string('religion');
            $table->string('photo');
            $table->string('statutActuel');
            $table->string('dateEnreg');
            $table->string('dateUpdate');
            $table->string('reactifsUtilises');
            $table->string('porteEntree');
            $table->string('siPTME');
            $table->date('dateAccouchement');
            $table->string('ptme_code');


            $table->string('centre_code'); // Lieu d'enregistrement


            $table->string('typePopulation');
            $table->tinyInteger('verifDeuxPrelev');

            $table->date('dateDecouverte');
            $table->string('typeVirus');
            $table->string('lieuTest');
            $table->tinyInteger('tauxCD4Init');

            $table->date('dateTestCD4Init');
            $table->string('chargeViraleInit');
            $table->date('datePrelevement');
            $table->integer('verifAntecedent');


            $table->timestamps();
        });

        Schema::table('patients', function($table) {
            $table->foreign('ptme_code')->references('code')->on('centres');
            $table->foreign('centre_code')->references('code')->on('centres');
               
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('patient');
    }
}
