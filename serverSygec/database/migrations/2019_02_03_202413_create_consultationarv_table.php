<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationarvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultationarvs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('centre_code');

            $table->tinyInteger('siTraitementJ0');
            $table->tinyInteger('siTraitementJ14');
            $table->tinyInteger('siTraitementJ30');
            $table->date('dateConsultation');

            $table->string('derniereAdresse');
            $table->string('tel');
            $table->string('personnelsoignant_code');


            $table->float('poids');
            $table->float('taille');
            $table->float('imc');
            $table->integer('indiceKarno');
            $table->integer('tauxCD4');
            $table->string('traitementARVInit');
            $table->string('traitementAssocie');

            $table->date('dateProchainRDV');
            $table->string('manifestationsCliniques');

            $table->string('evenement');
            $table->tinyInteger('siModificationTraitementARV');
            $table->string('protocole_code');

            $table->string('ligne_code');


            $table->string('motifChangementTraitement');
            $table->string('changementEffectue');
            
            $table->string('code_nouvelle_ligne');


            $table->string('conclusion');
            $table->string('statutPatient');
            $table->string('criteresEligibilite');
            $table->string('schemaInitial');
            $table->string('bilanSuivi');
            $table->string('transfereDe');


            $table->timestamps();
        });

        Schema::table('consultationarvs', function($table) {
            $table->foreign('code_nouvelle_ligne')->references('code')->on('lignes');
            $table->foreign('personnelsoignant_code')->references('code')->on('personnelsoignants');

            $table->foreign('protocole_code')->references('code')->on('protocoles');

            $table->foreign('ligne_code')->references('code')->on('lignes');
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
        Schema::dropIfExists('consultationarv');
    }
}
