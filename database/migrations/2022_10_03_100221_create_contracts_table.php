<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');

//            $table->string('type')->nullable(); // type of contract

            $table->unsignedBigInteger('company_id')->nullable(); //
            $table->unsignedBigInteger('our_company_id')->nullable(); //

//            $table->boolean('main_for_company')->default(0); //
            $table->date('date_of_end')->nullable(); // vremya deistviya v dnyah


            $table->string('name')->nullable(); // nazvanie
            $table->string('sum')->nullable(); // сумма договора

            $table->foreign('company_id')->references('id')->on('companies');

            $table->integer('deleted_id')->nullable();
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
        Schema::dropIfExists('contracts');
    }
}
