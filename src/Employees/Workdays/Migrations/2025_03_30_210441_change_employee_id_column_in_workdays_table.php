<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEmployeeIdColumnInWorkdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedBigInteger('new_employee_id')->nullable()->after('employee_id');
        });

        DB::statement('
            update workdays w
            join employees e on w.employee_id = e.related_user_id
            set w.new_employee_id = e.id
        ');

        Schema::table('workdays', function (Blueprint $table) {
            $table->dropForeign('workdays_employee_id_foreign');
            $table->dropIndex('workdays_employee_id_foreign');
        });

        Schema::table('workdays', function (Blueprint $table) {
            $table->renameColumn('employee_id', 'old_employee_id');

            $table->renameColumn('new_employee_id', 'employee_id');
        });

        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
            $table->unsignedBigInteger('old_employee_id')->nullable()->change();
        });

        Schema::table('workdays', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('employees');
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
            $table->unsignedBigInteger('new_employee_id')->nullable()->after('employee_id');
        });

        DB::statement('
            update workdays w
            join employees e on w.employee_id = e.id
            set w.new_employee_id = e.related_user_id
        ');

        Schema::table('workdays', function (Blueprint $table) {
            $table->dropForeign('workdays_employee_id_foreign');
            $table->dropIndex('workdays_employee_id_foreign');
        });

        Schema::table('workdays', function (Blueprint $table) {
            $table->dropColumn('employee_id');
            $table->renameColumn('new_employee_id', 'employee_id');
        });

        Schema::table('workdays', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
        });

        Schema::table('workdays', function (Blueprint $table) {
            $table->foreign('employee_id')->references('id')->on('users');
        });
    }
}
