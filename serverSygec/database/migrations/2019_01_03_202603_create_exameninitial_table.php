<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExameninitialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exameninitials', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_code');

            $table->string('centre_code');

            $table->float('poids');
            $table->float('taille');
            $table->float('imc');
            $table->float('to');
            $table->float('indexKarno');

            $table->tinyInteger('fievre');

            $table->date('dateFievre');
            $table->tinyInteger('diarrhe');
            $table->date('dateDiarrhe');
            $table->tinyInteger('amaigrissement');

            $table->tinyInteger('touxDyspnee');
            $table->date('dateToux');
            $table->tinyInteger('anemie');
            $table->tinyInteger('ictere');
            $table->tinyInteger('candidose');

            $table->tinyInteger('tuberculose');
            $table->string('typeTuberculose');
            $table->tinyInteger('autresPneumo');
            $table->tinyInteger('hematomegalie');
            $table->tinyInteger('splenomegalie');
            $table->tinyInteger('adenopatie');

            $table->string('aireAdenopatie');
            $table->tinyInteger('toxoplasmose');
            $table->tinyInteger('atteinteNeuro');

            $table->tinyInteger('lymphome');
            $table->tinyInteger('sarcomeKaposi');
            $table->tinyInteger('crytococcose');
            $table->tinyInteger('troublePsy');
            $table->string('autres');
            $table->string('classificationOMS');
            $table->float('hemoglobine');
            
            $table->float('plaquette');
            $table->float('creatinne');
            $table->float('tgo');

            $table->float('tgp');
            $table->float('glycemie');
            $table->float('agHBS');

            $table->timestamps();
        });

        Schema::table('exameninitials', function($table) {
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
        Schema::dropIfExists('exameninitial');
    }
}
