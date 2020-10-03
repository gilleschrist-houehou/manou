<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCentreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centres', function (Blueprint $table) {
            $table->increments('id');
            $table->string('libelle');
            $table->string('code',6)->unique();
            $table->string('commune_code');
            $table->string('typecentre_code');
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();


        });

         Schema::table('centres', function($table) {
            $table->foreign('typecentre_code')->references('code')->on('typecentres');
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
        Schema::dropIfExists('centres');
    }
}
