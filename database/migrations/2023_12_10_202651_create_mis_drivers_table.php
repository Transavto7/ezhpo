<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMisDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mis_drivers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('driver_id')->unique();
            $table->foreign('driver_id')->references('hash_id')->on('drivers')->onDelete('cascade');
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
        Schema::dropIfExists('mis_drivers');
    }
}
