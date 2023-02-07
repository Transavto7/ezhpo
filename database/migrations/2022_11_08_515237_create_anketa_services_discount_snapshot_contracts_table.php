<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnketaServicesDiscountSnapshotContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anketa_services_discount_snapshot_contracts', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('anketa_id')->index();
            $table->unsignedBigInteger('service_id')->index();

            $table->integer('service_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anketa_services_discount_snapshot_contracts');
    }
}
