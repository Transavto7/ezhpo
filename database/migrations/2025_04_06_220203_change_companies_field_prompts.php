<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ChangeCompaniesFieldPrompts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('field_prompts')
            ->where('type', '=', 'company')
            ->where('field', '=', 'user_id')
            ->update([
                'field' => 'responsible_id',
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('field_prompts')
            ->where('type', '=', 'company')
            ->where('field', '=', 'responsible_id')
            ->update([
                'field' => 'user_id',
            ]);
    }
}
