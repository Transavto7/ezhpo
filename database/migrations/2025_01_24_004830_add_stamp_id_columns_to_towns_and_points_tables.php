<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStampIdColumnsToTownsAndPointsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            'point' => 'pv_id',
            'town' => 'name'
        ];

        $attributes = [
            'name' => 'Штамп',
            'content' => 'Штамп медицинской лицензии',
            'deleted_at' => null
        ];

        $fieldName = 'stamp_id';

        foreach ($types as $type => $afterOtherField) {
            Schema::table("{$type}s", function (Blueprint $table) use ($fieldName) {
                $table->integer($fieldName)->nullable()->constrained('stamps')->onDelete('set null');
            });

            FieldPrompt::query()->updateOrCreate(
                [
                    'type' => $type,
                    'field' => $fieldName
                ],
                $attributes
            );

            FieldPrompt::moveAfterOther($type, $fieldName, $afterOtherField);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $types = [
            'point',
            'town'
        ];

        $fieldName = 'stamp_id';

        foreach ($types as $type) {
            Schema::table("{$type}s", function (Blueprint $table) use ($fieldName) {
                $table->dropColumn($fieldName);
            });
        }

        FieldPrompt::query()
            ->where('type', '=', $type)
            ->where('field', $fieldName)
            ->delete();
    }
}
