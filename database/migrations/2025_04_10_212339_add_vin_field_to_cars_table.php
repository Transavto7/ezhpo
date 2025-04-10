<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVinFieldToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'vin';
        $type = 'car';

        Schema::table('cars', function (Blueprint $table) use ($fieldName) {
            $table->string($fieldName)->nullable();
        });

        $attributes = [
            'name' => 'VIN-код',
            'content' => 'Уникальный код транспортного средства, состоящий из 17 знаков.',
            'deleted_at' => null
        ];

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName
            ],
            $attributes
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'official_type_auto');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'vin';

        Schema::table('cars', function (Blueprint $table) use ($fieldName) {
            $table->dropColumn([$fieldName]);
        });

        FieldPrompt::query()
            ->where('type', 'car')
            ->where('field', $fieldName)
            ->delete();
    }
}
