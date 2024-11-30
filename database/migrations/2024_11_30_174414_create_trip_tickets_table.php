<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36)->unique();

            $table->string('ticket_number');

            $table->string('company_id');
            $table->foreign('company_id')->references('hash_id')->on('companies');

            $table->date('start_date')->nullable();
            $table->integer('validity_period')->default(1);

            $table->string('medic_form_id')->nullable();
            $table->foreign('medic_form_id')->references('uuid')->on('forms');

            $table->string('driver_id')->nullable();
            $table->foreign('driver_id')->references('hash_id')->on('drivers');

            $table->string('tech_form_id')->nullable();
            $table->foreign('tech_form_id')->references('uuid')->on('forms');

            $table->string('car_id')->nullable();
            $table->foreign('car_id')->references('hash_id')->on('cars');

            $table->string('logistics_method');
            $table->string('transportation_type');
            $table->string('template_code');

            $table->timestamps();
            $table->softDeletes();
            $table->string('deleted_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_tickets');
    }
}
