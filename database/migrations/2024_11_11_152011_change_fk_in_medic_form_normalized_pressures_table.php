<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFkInMedicFormNormalizedPressuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medic_form_normalized_pressures', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->foreign('form_id')
                ->references('id')
                ->on('forms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medic_form_normalized_pressures', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->foreign('form_id')
                ->references('id')
                ->on('anketas')
                ->onDelete('cascade');
        });
    }
}
