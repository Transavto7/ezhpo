<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workdays', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 36)->unique();

            $table->dateTime('date');

            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('users');

            $table->unsignedBigInteger('terminal_id');
            $table->foreign('terminal_id')->references('id')->on('users');

            $table->double('t_people')->nullable();
            $table->boolean('t_people_test_status')->nullable();

            $table->unsignedTinyInteger('pressure_systolic')->nullable();
            $table->unsignedTinyInteger('pressure_diastolic')->nullable();
            $table->boolean('pressure_test_status')->nullable();

            $table->unsignedTinyInteger('type_anketa');

            $table->unsignedTinyInteger('pulse')->nullable();
            $table->boolean('pulse_test_status')->nullable();

            $table->double('alcometer_result')->nullable();
            $table->integer('alcometer_mode')->nullable();
            $table->boolean('alcometer_test_status')->nullable();

            $table->boolean('narko_test_status')->nullable();

            $table->text('photo')->nullable();
            $table->text('video')->nullable();

            $table->boolean('is_allowed_work');
            $table->boolean('is_manual')->default(false);
            $table->boolean('is_real');

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
        Schema::dropIfExists('workdays');
    }
}
