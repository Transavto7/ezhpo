<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class HideOperatorIdFieldInFieldPrompts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $field = FieldPrompt::query()
            ->where('field', 'operator_id')
            ->first();

        if ($field) {
            $field->delete();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $field = FieldPrompt::withTrashed()
            ->where('field', 'operator_id')
            ->first();

        if ($field && $field->deleted_at) {
            $field->restore();
        }
    }
}
