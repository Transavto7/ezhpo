<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropCarGosNumberColumnInTechFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tech_forms', function (Blueprint $table) {
            $table->dropColumn('car_gos_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tech_forms', function (Blueprint $table) {
            $table->string('car_gos_number')->nullable();
        });
    }
}
