<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ChangeOperatorIdColumnInMedicFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medic_forms', function (Blueprint $table) {
            $table->unsignedBigInteger('old_operator_id')->nullable()->after('operator_id');
            $table->index('old_operator_id');
        });

        DB::statement('update medic_forms set old_operator_id = operator_id where operator_id is not null');

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->dropForeign(['operator_id']);
        });

        DB::statement('update medic_forms set operator_id = null where operator_id is not null');

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->foreign('operator_id')->references('id')->on('employees');
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
            $table->dropForeign(['operator_id']);
        });

        DB::statement('update medic_forms set operator_id = old_operator_id where old_operator_id is not null');

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->foreign('operator_id')->references('id')->on('users');
        });

        Schema::table('medic_forms', function (Blueprint $table) {
            $table->dropColumn('old_operator_id');
        });
    }
}
