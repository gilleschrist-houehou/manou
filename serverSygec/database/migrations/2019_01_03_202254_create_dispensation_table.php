<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDispensationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dispensations', function (Blueprint $table) {
            $table->increments('id');

            $table->string('patient_code');
            $table->string('protocole_code',6);
            $table->tinyInteger('qte');
            $table->date('dateDispensation');
            
            $table->integer('created_by');
            $table->integer('updated_by');

            $table->timestamps();

            
        });

        Schema::table('dispensations', function($table) {
           $table->foreign('patient_code')->references('code')->on('patients');
            $table->foreign('protocole_code')->references('code')->on('protocoles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dispensation');
    }
}
