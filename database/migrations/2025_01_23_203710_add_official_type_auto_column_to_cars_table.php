<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOfficialTypeAutoColumnToCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'official_type_auto';
        $type = 'car';

        Schema::table('cars', function (Blueprint $table) use ($fieldName) {
            $table->string($fieldName)->nullable();
        });

        $attributes = [
            'name' => 'Категория Т\С (из документов)',
            'content' => 'Категория Т\С, указанная в СТС или ПТС. Отображается при печати ПЛ.',
            'deleted_at' => null
        ];

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName
            ],
            $attributes
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'type_auto');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'official_type_auto';

        Schema::table('cars', function (Blueprint $table) use ($fieldName) {
            $table->dropColumn([$fieldName]);
        });

        FieldPrompt::query()
            ->where('type', 'car')
            ->where('field', $fieldName)
            ->delete();
    }
}
