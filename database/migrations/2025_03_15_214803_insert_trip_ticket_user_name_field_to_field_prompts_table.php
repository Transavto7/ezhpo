<?php

use App\FieldPrompt;
use App\Models\TripTicket;
use Illuminate\Database\Migrations\Migration;

class InsertTripTicketUserNameFieldToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userName = [
            'attributes' => [
                'type' => TripTicket::SLUG,
                'field' => 'user_name'
            ],
            'values' => [
                'name' => 'ФИО ответственного',
                'content' => '<p>Ответственный за проведение и внесение путевого листа в электронный журнал</p>',
                'sort' => FieldPrompt::where('type', '=', TripTicket::SLUG)->orderByDesc('sort')->first()->sort + 1,
            ]
        ];

        FieldPrompt::query()->updateOrCreate($userName['attributes'], $userName['values']);
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
                'type' => TripTicket::SLUG,
                'field' => 'user_name',
            ])
            ->delete();
    }
}
