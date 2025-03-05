<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedIdFieldToWorkdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedBigInteger('deleted_id')->nullable();
            $table->foreign('deleted_id')->references('id')->on('users');
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
            $table->dropForeign(['deleted_id']);
            $table->dropColumn(['deleted_id']);
        });
    }
}
