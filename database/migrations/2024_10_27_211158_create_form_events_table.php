<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36)->unique();

            $table->string('form_uuid', 36);
            $table->foreign('form_uuid')->references('uuid')->on('forms');

            $table->string('event_type');
            $table->jsonb('payload');

            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('model_type')->nullable();

            $table->timestamps();
        });
    }

    /**˘
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('form_events');
    }
}
