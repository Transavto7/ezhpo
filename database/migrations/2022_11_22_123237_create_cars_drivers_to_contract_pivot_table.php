<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarsDriversToContractPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_contact_pivot', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('driver_id')->index();
            $table->unsignedBigInteger('contract_id')->index();
        });

        Schema::create('car_contact_pivot', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('car_id')->index();
            $table->unsignedBigInteger('contract_id')->index();
        });


        Schema::table('contracts', function (Blueprint $table) {
            $table->date('date_of_start')->after('date_of_end')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_contact_pivot');
        Schema::dropIfExists('car_contact_pivot');
    }
}
