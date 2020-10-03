<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArvprotocoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arvprotocoles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('protocole_code',6);
            $table->string('arv_code',6);
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();

        });

        Schema::table('arvprotocoles', function($table) {
            $table->foreign('protocole_code')->references('code')->on('protocoles');

            $table->foreign('arv_code')->references('code')->on('arvs');
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arvprotocole');
    }
}
