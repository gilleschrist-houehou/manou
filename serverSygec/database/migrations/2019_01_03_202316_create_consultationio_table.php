<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultationios', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_code');
            $table->float('poids');
            $table->float('taille');
            $table->float('ta');
            $table->float('to');
            $table->float('ik');
            $table->tinyInteger('rechercheIB');
            $table->string('autresPlaintes');
            $table->tinyInteger('examenClinique');
            $table->tinyInteger('demandeBarrXpert');
            $table->tinyInteger('demandeChargeVirale');
            $table->tinyInteger('demandeCD4');
            $table->tinyInteger('demandeVHBAutre');
            $table->tinyInteger('resultatTB');
            $table->tinyInteger('resultatChargeVirale');
            $table->tinyInteger('resultatCD4');
            $table->tinyInteger('resultatVHB');
            $table->string('diagnosticIO');
            $table->string('diagnosticAutre');



            $table->timestamps();
        });

        Schema::table('consultationios', function($table) {
            $table->foreign('patient_code')->references('code')->on('patients');
          
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultationio');
    }
}
