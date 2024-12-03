<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReqsValidatedFieldToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'reqs_validated';
        $type = 'company';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->boolean($fieldName)->default(false);
        });

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName
            ],
            [
                'deleted_at' => null,
                'name' => 'Корректные реквизиты',
                'content' => 'Реквизиты компании проверены в сервисе DADATA'
            ]
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'kpp');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'reqs_validated';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->dropColumn($fieldName);
        });

        FieldPrompt::query()
            ->where('type', '=', 'company')
            ->where('field', '=', $fieldName)
            ->delete();
    }
}
