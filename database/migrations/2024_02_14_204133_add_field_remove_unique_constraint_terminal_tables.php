<?php

use App\TerminalCheck;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldRemoveUniqueConstraintTerminalTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terminal_devices', function (Blueprint $table) {
            $table->dropUnique('terminal_devices_device_serial_number_unique');
        });

        Schema::table('terminal_checks', function (Blueprint $table) {
            $table->dropUnique('terminal_checks_serial_number_unique');
            $table->date('date_end_check');
        });

        foreach (TerminalCheck::withTrashed()->get() as $item) {
            $item->date_end_check = $item->date_check->copy()->addYear();
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('terminal_devices', function (Blueprint $table) {
            $table->unique('device_serial_number');
        });

        Schema::table('terminal_checks', function (Blueprint $table) {
            $table->unique('serial_number');
            $table->dropColumn('date_end_check');
        });
    }
}
