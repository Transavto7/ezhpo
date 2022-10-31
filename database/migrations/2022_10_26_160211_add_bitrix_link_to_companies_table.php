<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Symfony\Component\Console\Output\ConsoleOutput;

class AddBitrixLinkToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->text('bitrix_link')->after('deleted_at')->nullable();
        });

        $this->addFieldAfter('company','bitrix_link','Ссылка на компанию в Bitrix24', 'document_bdd');
    }

    public function addFieldAfter(string $type, string $field, string $name, $fieldAfter) {
        $fields = \App\FieldPrompt::where('type', $type)->get();
        $output = new ConsoleOutput();
        foreach ($fields as $tableField) {
            if ($fieldAfter === $tableField->field) {
                \App\FieldPrompt::create([
                    'type' => $type,
                    'field' => $field,
                    'name' => $name
                ]);
            }

            \App\FieldPrompt::create([
                'type' => $tableField->type,
                'field' => $tableField->field,
                'name' => $tableField->name
            ]);
        }
        \App\FieldPrompt::whereIn('id', $fields->pluck('id'))->forceDelete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('bitrix_link');
        });

        \App\FieldPrompt::where('type', 'company')->where('field', 'bitrix_link')->forceDelete();
    }
}
