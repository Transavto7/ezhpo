<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCommentToCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->longText('comment')->after('note')->nullable();
        });

        $this->addFieldAfter('company','comment', 'Комментарий', 'user_id');
        \App\FieldPrompt::create([
            'type' => 'company',
            'field' => 'comment',
            'name' => 'Комментарий'
        ]);
    }

    public function addFieldAfter(string $type, string $field, string $name, $fieldAfter) {
        $fields = \App\FieldPrompt::where('type', $type)->get();
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
            $table->dropColumn('comment');
        });

        \App\FieldPrompt::where('type', 'company')->where('field', 'comment')->forceDelete();
    }
}
