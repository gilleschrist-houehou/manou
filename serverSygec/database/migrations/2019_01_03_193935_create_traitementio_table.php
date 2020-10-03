<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraitementioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traitementios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient_code');
            $table->tinyInteger('CTM');

            $table->dateTime('dateDebutTraitement');
            $table->dateTime('dateFinTraitement');

            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();


        });

        Schema::table('traitementios', function($table) {
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
        Schema::dropIfExists('traitementio');
    }
}
