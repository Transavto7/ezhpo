<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripTicketLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_ticket_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36);

            $table->string('trip_ticket_id', 36);
            $table->foreign('trip_ticket_id')->references('uuid')->on('trip_tickets');

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('type');

            $table->jsonb('payload')->nullable();

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
        Schema::dropIfExists('trip_ticket_logs');
    }
}
