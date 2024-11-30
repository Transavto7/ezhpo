<?php

use App\FieldPrompt;
use App\Models\TripTicket;
use Illuminate\Database\Migrations\Migration;

class InsertTripTickedFieldsToFieldPromptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $fields = [
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'ticket_number'
                ],
                'values' => [
                    'name' => 'Номер ПЛ',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'created_at'
                ],
                'values' => [
                    'name' => 'Дата оформления',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'company_name'
                ],
                'values' => [
                    'name' => 'Компания',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'start_date'
                ],
                'values' => [
                    'name' => 'Дата начала действия',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'validity_period'
                ],
                'values' => [
                    'name' => 'Дней действует',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'medic_form_id'
                ],
                'values' => [
                    'name' => 'ID медосмотра',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'driver_name'
                ],
                'values' => [
                    'name' => 'ФИО водителя',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'tech_form_id'
                ],
                'values' => [
                    'name' => 'ID техосмотра',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'car_number'
                ],
                'values' => [
                    'name' => 'Госномер Т/С',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'logistics_method'
                ],
                'values' => [
                    'name' => 'Вид сообщения',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'transportation_type'
                ],
                'values' => [
                    'name' => 'Вид перевозки',
                ]
            ],
            [
                'attributes' => [
                    'type' => TripTicket::SLUG,
                    'field' => 'template_code'
                ],
                'values' => [
                    'name' => 'Печатный шаблон',
                ]
            ],
        ];

        foreach ($fields as $field) {
            FieldPrompt::query()->updateOrCreate($field['attributes'], $field['values']);
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
