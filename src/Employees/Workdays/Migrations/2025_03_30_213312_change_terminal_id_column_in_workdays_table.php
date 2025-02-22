<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTerminalIdColumnInWorkdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedBigInteger('new_terminal_id')->nullable()->after('terminal_id');
        });

        DB::statement('
            update workdays w
            join terminals e on w.terminal_id = e.related_user_id
            set w.new_terminal_id = e.id
        ');

        Schema::table('workdays', function (Blueprint $table) {
            $table->dropForeign('workdays_terminal_id_foreign');
            $table->dropIndex('workdays_terminal_id_foreign');

            $table->renameColumn('terminal_id', 'old_terminal_id');

            $table->renameColumn('new_terminal_id', 'terminal_id');
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
        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedBigInteger('new_terminal_id')->nullable()->after('terminal_id');
        });

        DB::statement('
            update workdays w
            join terminals e on w.terminal_id = e.id
            set w.new_terminal_id = e.related_user_id
        ');

        Schema::table('workdays', function (Blueprint $table) {
            $table->dropForeign('workdays_terminal_id_foreign');
            $table->dropIndex('workdays_terminal_id_foreign');

            $table->dropColumn('terminal_id');
            $table->renameColumn('new_terminal_id', 'terminal_id');

            $table->foreign('terminal_id')->references('id')->on('users');
        });
    }
}
