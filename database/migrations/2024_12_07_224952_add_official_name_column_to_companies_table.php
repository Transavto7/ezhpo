<?php

use App\Company;
use App\FieldPrompt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOfficialNameColumnToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fieldName = 'official_name';
        $type = 'company';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->string($fieldName)->nullable();
        });

        FieldPrompt::query()->updateOrCreate(
            [
                'type' => $type,
                'field' => $fieldName
            ],
            [
                'deleted_at' => null,
                'name' => 'Официальное название компании клиента',
                'content' => 'Официальное наименование компании для интеграции с 1С'
            ]
        );

        FieldPrompt::moveAfterOther($type, $fieldName, 'name');

        Company::withTrashed()
            ->update([
                "official_name" => DB::raw("`name`"),
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldName = 'official_name';

        Schema::table('companies', function (Blueprint $table) use ($fieldName) {
            $table->dropColumn($fieldName);
        });

        FieldPrompt::query()
            ->where('type', '=', 'company')
            ->where('field', '=', $fieldName)
            ->delete();
    }
}
