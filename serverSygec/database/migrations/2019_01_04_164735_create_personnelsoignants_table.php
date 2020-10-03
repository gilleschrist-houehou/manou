<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonnelsoignantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personnelsoignants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom');
            $table->string('prenoms');
            $table->string('code',10)->unique();
            $table->string('titre_code',3);
            $table->string('centre_code',6);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

            
        });

        Schema::table('personnelsoignants', function($table) {
           $table->foreign('titre_code')->references('code')->on('titres');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personnelsoignants');
    }
}
