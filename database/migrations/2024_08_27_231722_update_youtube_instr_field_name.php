<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class UpdateYoutubeInstrFieldName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()
            ->where('field', 'youtube')
            ->where('type', 'instr')
            ->update([
                'name' => "Ссылка на YouTube\RUTUBE"
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        FieldPrompt::query()
            ->where('field', 'youtube')
            ->where('type', 'instr')
            ->update([
                'name' => "Ссылка на YouTube"
            ]);
    }
}
