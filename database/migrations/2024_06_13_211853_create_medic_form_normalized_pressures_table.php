<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicFormNormalizedPressuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medic_form_normalized_pressures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('pressure', 7);
            $table->unsignedBigInteger('form_id')->unique();
            $table->foreign('form_id')
                ->references('id')
                ->on('anketas')
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
        Schema::dropIfExists('medic_form_normalized_pressures');
    }
}
