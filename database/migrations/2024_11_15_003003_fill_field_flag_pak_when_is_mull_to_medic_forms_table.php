<?php

use App\Enums\FlagPakEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FillFieldFlagPakWhenIsMullToMedicFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medic_forms', function (Blueprint $table) {
            DB::table('medic_forms')
                ->whereNull('flag_pak')
                ->update([
                    'flag_pak' => FlagPakEnum::INTERNAL,
                ]);
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
            DB::table('medic_forms')
                ->where('flag_pak', '=', FlagPakEnum::INTERNAL)
                ->update([
                    'flag_pak' => null,
                ]);
        });
    }
}
