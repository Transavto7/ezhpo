<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameIsDopFieldLabelsToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('field_prompts')
            ->where('field', '=', 'is_dop')
            ->update([
                'name' => 'Неполный осмотр',
                'content' => '<p>Режим для внесения ПЛ с недостающими данными, например если неизвестно точное время/ ФИО водителя и т.д.</p><p>Обычный осмотр - 0</p><p>Неполный осмотр - 1</p>',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('field_prompts')
            ->where('field', '=', 'is_dop')
            ->update([
                'name' => 'Режим ввода ПЛ',
                'content' => '<p>Режим для внесения ПЛ с недостающими данными, например если неизвестно точное время/ ФИО водителя и т.д.</p><p>Обычный осмотр - 0</p><p>Использован режим ввода - 1</p>',
            ]);
    }
}
