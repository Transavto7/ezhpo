<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerminalsCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terminals_counters', function (Blueprint $table) {
            $table->string('id', 36)->primary();
            $table->unsignedBigInteger('terminal_id');
            $table->string('type');
            $table->integer('count');
            $table->timestamps();

            $table->foreign('terminal_id')
                ->references('id')
                ->on('terminals')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('terminals_counters');
    }
}
