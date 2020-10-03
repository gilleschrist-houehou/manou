<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZonecommunesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zonecommunes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('zone_code',3);
            $table->string('commune_code');
            $table->string('type_localite',15);
            $table->timestamps();
        });

        Schema::table('zonecommunes', function($table) {
            $table->foreign('zone_code')->references('code')->on('zonesanitaires');
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
        Schema::dropIfExists('zonecommune');
    }
}
