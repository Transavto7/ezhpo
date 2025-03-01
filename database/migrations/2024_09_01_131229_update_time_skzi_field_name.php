<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class UpdateTimeSkziFieldName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FieldPrompt::query()
            ->where('field', 'time_skzi')
            ->where('type', 'car')
            ->update([
                'name' => "Срок действия СКЗИ\настройки тахографа ЕСТР"
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
            ->where('field', 'time_skzi')
            ->where('type', 'car')
            ->update([
                'name' => "Срок действия СКЗИ"
            ]);
    }
}
