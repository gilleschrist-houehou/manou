<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVillageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('libelle');
            $table->string('code',10)->unique();
            $table->string('arrondissement_code',6);
            $table->double('x_utm');
            $table->double('y_utm');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });

        Schema::table('villages', function($table) {
            $table->foreign('arrondissement_code')->references('code')->on('arrondissements');
           
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('villages');
    }
}
