<?php

use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDatePrmoToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('field_prompts', function (Blueprint $table) {
            FieldPrompt::query()->updateOrCreate(
                [
                    'type' => FormTypeEnum::MEDIC,
                    'field' => 'date_prmo'
                ],
                [
                    'deleted_at' => null,
                    'name' => 'Дата ПРМО',
                    'content' => '<p>Дата прохождения последнего ФАКТИЧЕСКОГО медицинского осмотра водителя</p>'
                ]
            );
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
            //
        });
    }
}
