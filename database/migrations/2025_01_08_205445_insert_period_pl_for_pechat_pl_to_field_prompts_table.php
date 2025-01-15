<?php

use Illuminate\Database\Migrations\Migration;

class InsertPeriodPlForPechatPlToFieldPromptsTable extends Migration
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
                'type' => 'pechat_pl',
                'field' => 'period_pl',
                'name' => 'Период действия ПЛ',
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
                'type' => 'pechat_pl',
                'field' => 'period_pl',
            ])
            ->delete();
    }
}
