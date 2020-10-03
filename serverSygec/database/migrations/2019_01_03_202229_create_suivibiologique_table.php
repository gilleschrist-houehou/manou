<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSuivibiologiqueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suivibiologiques', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_code');
            $table->tinyInteger('chargeVirale');

            $table->integer('CD4');
            $table->integer('NFS');

            $table->tinyInteger('transaminases');
            $table->tinyInteger('creat');
            $table->tinyInteger('autresBilans');
            $table->date('dateSuivi');

            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();

            
        });

        Schema::table('suivibiologiques', function($table) {
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
        Schema::dropIfExists('suivibiologique');
    }
}
