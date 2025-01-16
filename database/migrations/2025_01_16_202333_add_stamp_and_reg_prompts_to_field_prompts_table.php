<?php

use Illuminate\Database\Migrations\Migration;

class AddStampAndRegPromptsToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $now = new DateTimeImmutable();

        DB::table('field_prompts')
            ->insert([
                'type' => 'req',
                'field' => 'stamp_id',
                'name' => 'Штамп',
                'content' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

        DB::table('field_prompts')
            ->insert([
                'type' => 'town',
                'field' => 'req_id',
                'name' => 'Организация',
                'content' => '',
                'created_at' => $now,
                'updated_at' => $now,
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
            ->where([
                'type' => 'req',
                'field' => 'stamp_id',
            ])
            ->delete();

        DB::table('field_prompts')
            ->where([
                'type' => 'town',
                'field' => 'req_id',
            ])
            ->delete();
    }
}
