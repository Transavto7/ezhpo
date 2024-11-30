<?php

use App\FieldPrompt;
use App\Models\TripTicket;
use Illuminate\Database\Migrations\Migration;

class InsertTripTicketsPeriodPlFieldToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $period = [
            'attributes' => [
                'type' => TripTicket::SLUG,
                'field' => 'period_pl'
            ],
            'values' => [
                'name' => 'Период выдачи ПЛ',
            ]
        ];

        FieldPrompt::query()->updateOrCreate($period['attributes'], $period['values']);

        $fields = [
            'ticket_number',
            'created_at',
            'company_name',
            'start_date',
            'period_pl',
            'validity_period',
            'medic_form_id',
            'driver_name',
            'tech_form_id',
            'car_number',
            'logistics_method',
            'transportation_type',
            'template_code',
        ];

        foreach ($fields as $sort => $field) {
            FieldPrompt::query()
                ->where('type', TripTicket::SLUG)
                ->where('field', $field)
                ->update(['sort' => $sort]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
