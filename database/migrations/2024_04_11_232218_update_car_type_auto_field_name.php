<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class UpdateCarTypeAutoFieldName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()
            ->where('field', 'type_auto')
            ->where('type', 'car')
            ->update([
                'name' => "Категория Т\С"
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
            ->where('field', 'type_auto')
            ->where('type', 'car')
            ->update([
                'name' => "Тип т\с"
            ]);
    }
}
