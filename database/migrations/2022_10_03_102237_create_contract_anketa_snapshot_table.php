<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractAnketaSnapshotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_anketa_snapshot', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('anketa_id')->nullable(); //
            $table->unsignedBigInteger('contract_id')->nullable(); //
            $table->unsignedBigInteger('our_company_id')->nullable(); //

            $table->integer('time_of_action')->nullable(); // vremya deistviya v dnyah
//            $table->string('type')->nullable(); // type of contract
            $table->string('sum')->nullable(); // сумма договора

//            $table->unsignedBigInteger('company_info_snapshot')->nullable(); // Данные о компании на момент редактирования
            $table->unsignedBigInteger('company_id')->nullable(); // Актуальная компания - привязка
            $table->unsignedBigInteger('company_inn')->nullable(); // Актуальная компания - inn

            $table->unsignedBigInteger('driver_id')->nullable(); //
            $table->unsignedBigInteger('car_id')->nullable(); //

            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->foreign('car_id')->references('id')->on('cars');

            $table->softDeletes();
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
        Schema::dropIfExists('contract_anketa_snapshot');
    }
}
