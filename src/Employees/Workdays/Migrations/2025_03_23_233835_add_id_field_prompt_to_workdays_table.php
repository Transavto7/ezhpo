<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class AddIdFieldPromptToWorkdaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'id';
        $type = 'workdays';

        $attributes = [
            'name' => 'ID осмотра',
            'content' => '',
            'deleted_at' => null,
        ];

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName,
            ],
            $attributes
        );

        FieldPrompt::moveBeforeOther($type, $fieldName, 'date');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'id';

        FieldPrompt::query()
            ->where('type', 'workdays')
            ->where('field', $fieldName)
            ->delete();
    }
}
