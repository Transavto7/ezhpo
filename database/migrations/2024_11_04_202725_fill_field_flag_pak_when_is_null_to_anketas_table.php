<?php

use App\Enums\FlagPakEnum;
use App\Enums\FormTypeEnum;
use Illuminate\Database\Migrations\Migration;

class FillFieldFlagPakWhenIsNullToAnketasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('anketas')
            ->whereNull('flag_pak')
            ->where('is_pak', '!=', 1)
            ->where('type_anketa', '=', FormTypeEnum::MEDIC)
            ->update([
                'flag_pak' => FlagPakEnum::INTERNAL,
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
