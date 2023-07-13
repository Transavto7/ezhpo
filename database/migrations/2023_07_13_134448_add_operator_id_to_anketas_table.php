<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperatorIdToAnketasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anketas', function (Blueprint $table) {
            $table->bigInteger('operator_id')->nullable()->index();
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
            $table->dropColumn('operator_id');
        });
    }
}
