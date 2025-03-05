<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn('date');
            $table->dropColumn('price_hour');
            $table->date('date_from');
            $table->date('date_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropColumn('date_from');
            $table->dropColumn('date_to');
            $table->dateTime('date');
            $table->unsignedSmallInteger('price_hour')->default(0);
        });
    }
}
