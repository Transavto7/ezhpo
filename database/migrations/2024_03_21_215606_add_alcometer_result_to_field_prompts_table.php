<?php

use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class AddAlcometerResultToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()->updateOrCreate(
            [
                'type' => FormTypeEnum::MEDIC,
                'field' => 'alcometer_result'
            ],
            [
                'deleted_at' => null,
                'name' => 'Уровень алкоголя в крови',
                'content' => '<p><span class="ql-color-#000000">Результаты замера уровня алкоголя алкотестером. При качественном режиме - 0 или 1, при количественном - вещественное число</span></p>'
            ]
        );
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
