<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reqs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('hash_id')->default('000000')->unique();
            $table->string('name');
            $table->string('inn')->nullable();
            $table->string('bik')->nullable();
            $table->string('kc')->nullable();
            $table->string('rc')->nullable();
            $table->string('banks')->nullable();
            $table->string('director')->nullable();
            $table->string('director_fio')->nullable();
            $table->text('signature')->nullable();
            $table->text('seal')->nullable();
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
        Schema::dropIfExists('reqs');
    }
}
