<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractServiceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_service', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('service_id');

            $table->integer('service_cost');

            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_service');
    }
}
