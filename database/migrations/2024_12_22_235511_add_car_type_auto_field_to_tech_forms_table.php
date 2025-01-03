<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCarTypeAutoFieldToTechFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tech_forms', function (Blueprint $table) {
            $table->string('car_type_auto')->nullable();
        });

        DB::table('tech_forms')
            ->leftJoin('cars as c', 'tech_forms.car_id', '=', 'c.hash_id')
            ->whereNotNull('c.type_auto')
            ->whereNull('tech_forms.car_type_auto')
            ->where('is_dop', 1)
            ->update([
                'car_type_auto' => DB::raw("`c`.`type_auto`")
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tech_forms', function (Blueprint $table) {
            $table->dropColumn('car_type_auto');
        });
    }
}
