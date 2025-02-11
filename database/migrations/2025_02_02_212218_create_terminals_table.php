<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerminalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('blocked')->default(0);
            $table->integer('pv_id')->default(0);
            $table->integer('stamp_id')->nullable();
            $table->dateTime('last_connection_at')->nullable();
            $table->boolean('auto_created')->default(false);
            $table->softDeletes();
            $table->string('deleted_id', 191)->nullable();
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
        Schema::dropIfExists('terminals');
    }
}
