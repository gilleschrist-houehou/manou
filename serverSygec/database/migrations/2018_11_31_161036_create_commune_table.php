<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommuneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('libelle');
            $table->string('code',3)->unique();
            $table->string('departement_code',3);
            //$table->string('zone_code');
            $table->double('x_utm');
            $table->double('y_utm');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

        });

        Schema::table('communes', function($table) {
            $table->foreign('departement_code')->references('code')->on('departements');
            
               
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('communes');
    }
}
