<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDeletedIdToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('towns', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
        });
        Schema::table('settings', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
        });
        Schema::table('points', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
        });
        Schema::table('roles', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
            $table->softDeletes();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->string('deleted_id')->nullable();
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
