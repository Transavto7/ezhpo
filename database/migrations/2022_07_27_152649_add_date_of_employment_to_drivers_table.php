<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDateOfEmploymentToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->date('date_of_employment')->nullable(); // Дата трудоустройства
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {

        });
    }
}
