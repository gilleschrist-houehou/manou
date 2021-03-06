<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecrutementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recrutements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title',255);
            $table->text('description');
            $table->string('image',255)->nullable();
            $table->string('piece',255)->nullable();
            $table->boolean('visible')->default('0');
            $table->date('datefin')->nullable();
            $table->integer('created_by')->length(4)->unsigned();
            $table->integer('updated_by')->length(4)->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('recrutements');
    }
}
