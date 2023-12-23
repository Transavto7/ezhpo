<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMisAnketasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mis_anketas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('anketa_id')->unsigned()->unique();
            $table->foreign('anketa_id')->references('id')->on('anketas')->onDelete('cascade');
            $table->uuid('id_mis');
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
        Schema::dropIfExists('mis_anketas');
    }
}
