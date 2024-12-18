<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class AddDriverPhoneToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'driver_phone';
        $type = 'medic';

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName
            ],
            [
                'deleted_at' => null,
                'name' => 'Телефон водителя',
                'content' => 'Телефон для связи с водителем'
            ]
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'driver_fio');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'driver_phone';

        FieldPrompt::query()
            ->where('type', '=', 'medic')
            ->where('field', '=', $fieldName)
            ->delete();
    }
}
