<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToWorkdays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedTinyInteger('timezone')->default(3);
            $table->unsignedBigInteger('open_workday_id')->nullable();
            $table->foreign('open_workday_id')->references('id')->on('workdays');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->dropColumn(['timezone', 'open_workday_id']);
            $table->dropForeign('open_workday_id');
        });
    }
}
