<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeTerminalIdColumnInMedicFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medic_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('old_terminal_id')->nullable()->after('terminal_id');
            $table->index('old_terminal_id');
        });

        DB::statement('update medic_forms set old_terminal_id = terminal_id where terminal_id is not null');

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->dropForeign(['terminal_id']);
        });

        DB::statement('update medic_forms set terminal_id = null where terminal_id is not null');

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->foreign('terminal_id')->references('id')->on('terminals');
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
            $table->dropForeign(['terminal_id']);
        });

        DB::statement('update medic_forms set terminal_id = old_terminal_id where old_terminal_id is not null');

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->foreign('terminal_id')->references('id')->on('users');
        });

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->dropColumn('old_terminal_id');
        });
    }
}
