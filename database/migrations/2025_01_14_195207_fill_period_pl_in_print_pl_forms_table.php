<?php

use Illuminate\Database\Migrations\Migration;

class FillPeriodPlInPrintPlFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('print_pl_forms')
            ->leftJoin('forms', 'forms.uuid', '=', 'print_pl_forms.forms_uuid')
            ->whereNull('print_pl_forms.period_pl')
            ->whereNotNull('forms.date')
            ->update([
                'print_pl_forms.period_pl' => DB::raw("DATE_FORMAT(forms.date, '%Y-%m')"),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
