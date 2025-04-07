<?php

use App\FieldPrompt;
use App\Models\TripTicket;
use Illuminate\Database\Migrations\Migration;

class InsertTripTicketTypeAndTripTicketStatusFieldToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = [
            'attributes' => [
                'type' => TripTicket::SLUG,
                'field' => 'type'
            ],
            'values' => [
                'name' => 'Вид ПЛ',
                'sort' => FieldPrompt::where('type', '=', TripTicket::SLUG)->orderByDesc('sort')->first()->sort + 1,
            ]
        ];

        FieldPrompt::query()->updateOrCreate($type['attributes'], $type['values']);

        $status = [
            'attributes' => [
                'type' => TripTicket::SLUG,
                'field' => 'status'
            ],
            'values' => [
                'name' => 'Статус',
                'sort' => FieldPrompt::where('type', '=', TripTicket::SLUG)->orderByDesc('sort')->first()->sort + 1,
            ]
        ];

        FieldPrompt::query()->updateOrCreate($status['attributes'], $status['values']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('field_prompts')
            ->where('type',  '=', TripTicket::SLUG)
            ->whereIn('field',  ['type', 'status'])
            ->delete();
    }
}
