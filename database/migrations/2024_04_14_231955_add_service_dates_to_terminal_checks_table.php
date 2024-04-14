<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceDatesToTerminalChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terminal_checks', function (Blueprint $table) {
            $table->date('date_service_start')->nullable();
            $table->date('date_service_end')->nullable();
            $table->integer('failures_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminal_checks', function (Blueprint $table) {
            $table->dropColumn([
                'date_service_start',
                'date_service_end',
                'failures_count'
            ]);
        });
    }
}
