<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOneCSyncedFieldToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'one_c_synced';
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
                'name' => 'Синхронизация 1С',
                'content' => 'Компания создана в 1С, данные компании - синхронизированы'
            ]
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'reqs_validated');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'one_c_synced';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->dropColumn($fieldName);
        });

        FieldPrompt::query()
            ->where('type', '=', 'company')
            ->where('field', '=', $fieldName)
            ->delete();
    }
}
