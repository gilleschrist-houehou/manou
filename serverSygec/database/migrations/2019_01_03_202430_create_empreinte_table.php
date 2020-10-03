<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpreinteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('empreintes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patient_code');

            $table->string('empreintePouce');
            $table->string('empreinteIndex');
            $table->string('empreinteMajeur');
            
            $table->dateTime('dateEnrolement');
            $table->string('centre_code');
            

            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();
        });
        Schema::table('empreintes', function($table) {
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
        Schema::dropIfExists('empreinte');
    }
}
