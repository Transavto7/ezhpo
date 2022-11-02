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
        $fields = \App\FieldPrompt::where('type', 'company')->get();
        foreach ($fields as $tableField) {
            if ($tableField->field === 'user_id') {
                $dismissed = $fields->where('field', 'dismissed')->first();
                if ($dismissed) {
                    \App\FieldPrompt::create([
                        'type' => $dismissed->type,
                        'field' => $dismissed->field,
                        'name' => $dismissed->name,
                        'content' => $dismissed->content
                    ]);
                }

                \App\FieldPrompt::create([
                    'type' => 'company',
                    'field' => 'comment',
                    'name' => 'Комментарий'
                ]);
            }

            if ($tableField->field !== 'dismissed') {
                \App\FieldPrompt::create([
                    'type' => $tableField->type,
                    'field' => $tableField->field,
                    'name' => $tableField->name,
                    'content' => $tableField->content
                ]);
            }
        }
        \App\FieldPrompt::whereIn('id', $fields->pluck('id'))->forceDelete();
    }

    public function addFieldAfter(string $type, string $field, string $name, $fieldAfter) {

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
