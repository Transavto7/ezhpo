<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_dates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('hash_id');
            $table->string('item_model');
            $table->string('field');
            $table->integer('days')->default(0);
            $table->string('action')->default('+');

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
        Schema::dropIfExists('d_dates');
    }
}
