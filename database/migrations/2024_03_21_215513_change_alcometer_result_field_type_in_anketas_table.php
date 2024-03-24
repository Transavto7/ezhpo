<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeAlcometerResultFieldTypeInAnketasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anketas', function (Blueprint $table) {
            $table->float('alcometer_result')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('anketas', function (Blueprint $table) {
            $table->integer('alcometer_result')->nullable()->change();
        });
    }
}
