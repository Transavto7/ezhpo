<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdatePromptForMedicFlagPakInFieldPrompts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_prompts', function (Blueprint $table) {
            DB::table('field_prompts')
                ->where('type', '=', 'medic')
                ->where('field', '=', 'flag_pak')
                ->update([
                    'name' => 'Вид осмотра',
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
        Schema::table('field_prompts', function (Blueprint $table) {
            DB::table('field_prompts')
                ->where('type', '=', 'medic')
                ->where('field', '=', 'flag_pak')
                ->update([
                    'name' => 'Флаг СДПО',
                ]);
        });
    }
}
