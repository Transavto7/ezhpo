<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExaminationsCountFieldsToTerminalsTable extends Migration
{
    /**
     * Выполнение миграции.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->integer('month_amount')->default(0);
            $table->integer('last_month_amount')->default(0);
        });
    }

    /**
     * Откат миграции.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminals', function (Blueprint $table) {
            $table->dropColumn('month_amount');
            $table->dropColumn('last_month_amount');
        });
    }
}
