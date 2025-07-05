<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class UpdateDateTechviewFieldName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()
            ->where('field', 'date_techview')
            ->where('type', 'car')
            ->update([
                'name' => "Дата окончания действия диагностической карты"
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        FieldPrompt::query()
            ->where('field', 'date_techview')
            ->where('type', 'car')
            ->update([
                'name' => "Дата техосмотра"
            ]);
    }
}
