<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContractToAnketasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('anketas', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_id')->nullable();
            $table->unsignedBigInteger('contract_snapshot_id')->nullable();

            $table->foreign('contract_id')->references('id')->on('contracts');
            $table->foreign('contract_snapshot_id')->references('id')->on('contracts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contract_anketa_snapshot');
    }
}
