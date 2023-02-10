<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndexesForAnketasAndDriversTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->index('company_id', 'drivers_company_id_idx');
        });

        Schema::table('anketas', function (Blueprint $table) {
            $table->index('driver_id', 'anketas_driver_id_idx');
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
            $table->dropIndex( 'drivers_company_id_idx');
        });

        Schema::table('anketas', function (Blueprint $table) {
            $table->dropIndex('anketas_driver_id_idx');
        });
    }
}
