<?php

use App\FieldPrompt;
use Illuminate\Database\Migrations\Migration;

class AddTerminalsFieldPrompts extends Migration
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
                [
                    'type' => 'terminals',
                    'field' => 'failures_count'
                ],
                [
                    'name' => 'Количество сбоев',
                ]
            ],
            [
                [
                    'type' => 'terminals',
                    'field' => 'date_check'
                ],
                [
                    'name' => 'Срок поверки терминала',
                ]
            ],
            [
                [
                    'type' => 'terminals',
                    'field' => 'serial_number'
                ],
                [
                    'name' => 'S/N',
                ]
            ],
            [
                [
                    'type' => 'terminals',
                    'field' => 'date_service_start'
                ],
                [
                    'name' => 'Дата начала оказания услуг',
                ]
            ],
            [
                [
                    'type' => 'terminals',
                    'field' => 'date_service_end'
                ],
                [
                    'name' => 'Дата окончания оказания услуг',
                ]
            ],
        ];

        foreach ($fields as $field) {
            FieldPrompt::query()->updateOrCreate(
                $field[0],
                $field[1]
            );
        }

        FieldPrompt::query()
            ->where('type', 'terminals')
            ->where('name', 'GMT')
            ->update([
                'field' => 'timezone'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
