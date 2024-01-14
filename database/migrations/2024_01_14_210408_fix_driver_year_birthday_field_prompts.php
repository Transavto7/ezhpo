<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class FixDriverYearBirthdayFieldPrompts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('field_prompts')
            ->where('type', 'medic')
            ->where('name', 'Дата рождения')
            ->update(['field' => 'year_birthday']);
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
