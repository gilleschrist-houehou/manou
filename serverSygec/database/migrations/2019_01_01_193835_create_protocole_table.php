<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProtocoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protocoles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code',6)->unique();
            $table->string('libelle');
            $table->string('ligne_code',6);

            $table->integer('created_by');
            $table->integer('updated_by');



            $table->timestamps();
        });

        Schema::table('protocoles', function($table) {
            $table->foreign('ligne_code')->references('code')->on('lignes');
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('protocole');
    }
}
