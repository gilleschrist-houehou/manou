<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraitementarvanterieurTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traitementarvanterieurs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_code');


            $table->tinyInteger('verifTraitementARVAnterieur');
            $table->string('centre_code');

            $table->tinyInteger('transfertAvecDossier');

            $table->date('dateDebut');
            $table->date('dateFin');
            $table->tinyInteger('succes');
            $table->tinyInteger('cotri');
            $table->tinyInteger('tuberculose');
            $table->tinyInteger('io');
            $table->tinyInteger('autres');

            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();
        });

        Schema::table('traitementarvanterieurs', function($table) {
            $table->foreign('patient_code')->references('code')->on('patients');

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
        Schema::dropIfExists('traitementarvanterieur');
    }
}
