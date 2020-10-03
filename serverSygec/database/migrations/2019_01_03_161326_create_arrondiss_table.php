<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArrondissTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arrondissements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('libelle');
            $table->string('code',6)->unique();
            $table->string('commune_code');
            $table->double('x_utm');
            $table->double('y_utm');
            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();
        });

        Schema::table('arrondissements', function($table) {
            $table->foreign('commune_code')->references('code')->on('communes');
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arrondissements');
    }
}
