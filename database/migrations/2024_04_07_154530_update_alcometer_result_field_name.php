<?php

use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateAlcometerResultFieldName extends Migration
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
                'name' => 'Уровень алкоголя в выдыхаемом водухе'
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
        //
    }
}
