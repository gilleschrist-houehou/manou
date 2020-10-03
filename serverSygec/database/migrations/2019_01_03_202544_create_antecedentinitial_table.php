<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAntecedentinitialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antecedentinitials', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_code');
            
            $table->tinyInteger('alcool');
            $table->integer('qteAlcool');

            $table->tinyInteger('tabagisme');
            $table->tinyInteger('paquetAnneeTabac');
            $table->tinyInteger('hepatite');

            $table->date('dateHepatite');
            $table->tinyInteger('zona');

            $table->date('dateZona');
            $table->tinyInteger('depression');
            $table->date('datePression');

            $table->string('autreAntecedent');

            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();
        });

         Schema::table('antecedentinitials', function($table) {
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
        Schema::dropIfExists('antecedentinitial');
    }
}
