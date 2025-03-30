<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyDebtsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_debts', function (Blueprint $table) {
            $table->string('hash_id')->unique()->index();
            $table->foreign('hash_id')->references('hash_id')->on('companies');

            $table->dateTime('relevant_on');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_debts');
    }
}
