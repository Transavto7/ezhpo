<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmployeeIdColumnInTripTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable()->after('template_code');
            $table->foreign('employee_id')->references('id')->on('employees');
        });

        Schema::table('trip_tickets', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trip_tickets', function (Blueprint $table) {
            Schema::table('trip_tickets', function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        });
    }
}
