<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class ChangeUsersTypeInFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()
            ->where('type', '=', 'users')
            ->where('field', '=', 'hash_id')
            ->update(['field' => 'id']);

        FieldPrompt::query()
            ->where('type', '=', 'users')
            ->update(['type' => 'employees']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        FieldPrompt::query()
            ->where('type', '=', 'employees')
            ->update(['type' => 'users']);

        FieldPrompt::query()
            ->where('type', '=', 'users')
            ->where('field', '=', 'id')
            ->update(['field' => 'hash_id']);
    }
}
