<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedAtToCrmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('cars', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('discounts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('instrs', function (Blueprint $table) {
            $table->softDeletes();
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
            //
        });
    }
}
