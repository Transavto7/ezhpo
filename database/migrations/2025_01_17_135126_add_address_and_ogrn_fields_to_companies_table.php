<?php

use App\FieldPrompt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAddressAndOgrnFieldsToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = 'company';

        $fields = [
            'address' => [
                'name' => 'Адрес',
                'content' => 'Юридический адрес компании или ИП'
            ],
            'ogrn' => [
                'name' => 'ОГРН',
                'content' => 'ОГРН (основной государственный регистрационный номер) — государственный регистрационный номер записи о создании юридического лица.'
            ],
        ];

        foreach ($fields as $fieldName => $attributes) {
            Schema::table('companies', function (Blueprint $table) use ($fieldName) {
                $table->string($fieldName)->nullable();
            });

            $attributes['deleted_at'] = null;

            FieldPrompt::query()->updateOrCreate(
                [
                    'type' => $type,
                    'field' => $fieldName
                ],
                $attributes
            );

            FieldPrompt::moveAfterOther($type, $fieldName, 'kpp');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $fieldNames = [
            'ogrn',
            'address'
        ];

        Schema::table('companies', function (Blueprint $table) use ($fieldNames) {
            $table->dropColumn($fieldNames);
        });

        FieldPrompt::query()
            ->where('type', '=', 'company')
            ->whereIn('field', $fieldNames)
            ->delete();
    }
}
