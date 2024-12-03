<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKppFieldToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'kpp';
        $type = 'company';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->string($fieldName, 9)->nullable();
        });

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName
            ],
            [
                'deleted_at' => null,
                'name' => 'КПП',
                'content' => 'Код причины постановки на учет (КПП) — это код, который дополняет ИНН и содержит информацию об основании постановки на учет в налоговом органе'
            ]
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'inn');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'kpp';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->dropColumn($fieldName);
        });

        FieldPrompt::query()
            ->where('type', '=', 'company')
            ->where('field', '=', $fieldName)
            ->delete();
    }
}
