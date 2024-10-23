<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDayHashFieldsToFormsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medic_forms', function (Blueprint $table) {
            $table->string('day_hash')->nullable();
        });

        Schema::table('tech_forms', function (Blueprint $table) {
            $table->string('day_hash')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medic_forms', function (Blueprint $table) {
            $table->dropColumn('day_hash');
        });

        Schema::table('tech_forms', function (Blueprint $table) {
            $table->dropColumn('day_hash');
        });
    }
}
