<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInstrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instrs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('hash_id')->default('000000')->unique();
            $table->text('type_briefing');
            $table->text('name');
            $table->longText('descr')->nullable();
            $table->text('photos')->nullable();
            $table->text('youtube')->nullable();
            $table->integer('active')->default(0);

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
        Schema::dropIfExists('instrs');
    }
}
